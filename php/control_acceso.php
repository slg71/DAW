<?php
// ======================================================
// control_acceso.php — Validacion de inicio de sesion
// ======================================================

// 1. Incluimos la conexión a la BD
include "conexion_bd.php"; 

$cookie_lifetime = time() + (90 * 24 * 60 * 60);//90 dias

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    $usuario = trim($_POST["usuario"] ?? "");
    $pwd = trim($_POST["pwd"] ?? "");
    $recordar = isset($_POST["recordar"]); 

    // 2. Conectamos a la BD
    $mysqli = conectarBD();

    if ($mysqli) {
        
        // 3. Creamos el SQL para buscar al usuario
        // Necesitamos el IdUsuario, la Clave y el Fichero de estilo
        // (Tuve que buscar cómo se hacía un JOIN, como en mis_datos.php pero con Estilos)
        $sql = "SELECT u.IdUsuario, u.NomUsuario, u.Clave, e.Fichero 
                FROM Usuarios u 
                JOIN Estilos e ON u.Estilo = e.IdEstilo
                WHERE u.NomUsuario = '" . mysqli_real_escape_string($mysqli, $usuario) . "'";
                
        // (Añadí mysqli_real_escape_string por si acaso, aunque no sé muy bien qué hace)

        // 4. Ejecutamos la consulta
        $resultado = mysqli_query($mysqli, $sql);

        // 5. Comprobamos si encontramos al usuario
        if ($resultado && mysqli_num_rows($resultado) == 1) {
            
            $fila_usuario = mysqli_fetch_assoc($resultado);

            // 6. Comprobamos la contraseña
            if ($pwd === $fila_usuario['Clave']) {
                                
                // 7.Guardamos el ID numérico de la BD.
                $_SESSION['usuario_id'] = $fila_usuario['IdUsuario'];
                $_SESSION['usuario_nombre'] = $fila_usuario['NomUsuario'];
                
                // 8. GUARDAR el estilo en COOKIE (desde la BD)
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
                        setcookie("recordar_pass", $pwd, $cookie_lifetime, "/", '', false, true); // Dejo esto como lo tenías
                    }
                    
                    $current_time = date("c");
                    setcookie("last_visit_recordar", $current_time, $cookie_lifetime, "/", '', false, true);

                } else {
                    setcookie("recordar_usuario", '', time() - 3600, '/');
                    setcookie("recordar_pass", '', time() - 3600, '/');
                    setcookie("last_visit_recordar", '', time() - 3600, '/');
                    unset($_SESSION['last_visit_time_to_show']); 
                }
                // --- Fin del código copiado ---

                // 9. Redirigimos a la página privada
                // La práctica dice "menú de usuario registrado"
                header("Location: MenuRegistradoUsu.php"); 
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

        mysqli_close($mysqli); // Cerramos la conexión, como en mis_datos.php

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