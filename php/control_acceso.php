<?php
// ======================================================
// control_acceso.php — Validacion de inicio de sesion
// ======================================================

// 1. Incluimos la conexión a la BD
require_once "conexion_bd.php"; 

$cookie_lifetime = time() + (90 * 24 * 60 * 60); // 90 dias

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    $usuario = trim($_POST["usuario"] ?? "");
    $pwd = trim($_POST["pwd"] ?? "");
    $recordar = isset($_POST["recordar"]); 

    // 2. Conectamos a la BD
    $mysqli = conectarBD();

    if ($mysqli) {
        
        // 3. Sentencia preparada para evitar Inyección SQL
        $sql = "SELECT u.IdUsuario, u.NomUsuario, u.Clave, u.Foto, e.Fichero 
                FROM Usuarios u 
                JOIN Estilos e ON u.Estilo = e.IdEstilo
                WHERE u.NomUsuario = ?";
        
        $stmt = mysqli_prepare($mysqli, $sql);
        
        if ($stmt) {
            // 4. Asociamos el parámetro
            mysqli_stmt_bind_param($stmt, "s", $usuario);
            
            // 5. Ejecutamos la consulta
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            // 6. Comprobamos si encontramos al usuario
            if ($resultado && mysqli_num_rows($resultado) == 1) {
                
                $fila_usuario = mysqli_fetch_assoc($resultado);

                // 7. Comprobamos la contraseña con password_verify()
                if (password_verify($pwd, $fila_usuario['Clave'])) {
                                    
                    // 8. Guardamos el ID numérico de la BD.
                    $_SESSION['usuario_id'] = $fila_usuario['IdUsuario'];
                    $_SESSION['usuario_nombre'] = $fila_usuario['NomUsuario'];
                    $_SESSION['usuario_foto'] = !empty($fila_usuario['Foto']) ? $fila_usuario['Foto'] : 'perfil.jpg';
                    
                    // 9. GUARDAR el estilo en COOKIE (desde la BD)
                    $estilo_usuario = $fila_usuario['Fichero'];
                    setcookie('estilo', $estilo_usuario, $cookie_lifetime, '/', '', false, true);

                    // ------------------------------------------------------------------
                    // RECUERDAME Y ULTIMA VISITA
                    // ------------------------------------------------------------------
                    if ($recordar) {
                        $last_visit_antes = $_COOKIE["last_visit_recordar"] ?? null; 
                        
                        if($last_visit_antes) {
                            $_SESSION['last_visit_time_to_show'] = $last_visit_antes; 
                        } else {
                            $_SESSION['last_visit_time_to_show'] = date("c");
                        }
                        
                        $recordar_existente = (isset($_COOKIE["recordar_usuario"]) && $_COOKIE["recordar_usuario"] === $usuario);

                        if (!$recordar_existente) {
                            setcookie("recordar_usuario", $usuario, $cookie_lifetime, "/", '', false, true);
                        }
                        
                        $current_time = date("c");
                        setcookie("last_visit_recordar", $current_time, $cookie_lifetime, "/", '', false, true);

                    } else {
                        setcookie("recordar_usuario", '', time() - 3600, '/');
                        // setcookie("recordar_pass", '', time() - 3600, '/'); // <-- ELIMINADO
                        setcookie("last_visit_recordar", '', time() - 3600, '/');
                        unset($_SESSION['last_visit_time_to_show']); 
                    }

                    // 10. Redirigimos a la pagina privada
                    header("Location: configurar.php"); 
                    exit;

                } else {
                    // Contraseña incorrecta
                    $_SESSION['mensaje_error_login'] = "Datos de Usuario inválidos.";
                    header("Location: login.php?usuario=" . urlencode($usuario));
                    exit;
                }
                
                mysqli_free_result($resultado);

            } else {
                // Usuario no encontrado
                $_SESSION['mensaje_error_login'] = "Datos de Usuario inválidos.";
                header("Location: login.php?usuario=" . urlencode($usuario));
                exit;
            }
            
            mysqli_stmt_close($stmt);

        } else {
            $_SESSION['mensaje_error_login'] = "Error al preparar la consulta.";
            header("Location: login.php");
            exit;
        }

        mysqli_close($mysqli);

    } else {
        // Error si no se puede conectar a la BD
        $_SESSION['mensaje_error_login'] = "Error de conexión con la base de datos.";
        header("Location: login.php");
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>