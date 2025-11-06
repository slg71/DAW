<?php
// ======================================================
// control_acceso.php — Validación de inicio de sesión (FINAL)
// ======================================================

// Lista de usuarios válidos (con su estilo asignado)
$usuarios_con_estilo = [
    "leigh" => ["pwd" => "1234", "estilo" => "contraste.css"],
    "hugo"  => ["pwd" => "abcd", "estilo" => "letra_grande.css"],
    "maria" => ["pwd" => "pass", "estilo" => "letra_y_contraste.css"],
    "saray" => ["pwd" => "1111", "estilo" => "estilo.css"],
    "prueba"=> ["pwd" => "9876", "estilo" => "estilo.css"] 
];

// Tiempo de vida de la cookie de "Recordarme" (90 días)
$cookie_lifetime = time() + (90 * 24 * 60 * 60);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    $usuario = trim($_POST["usuario"] ?? "");
    $pwd     = trim($_POST["pwd"] ?? "");
    $recordar = isset($_POST["recordar"]); 

    // ... (Validaciones básicas)

    // Validar credenciales
    if (isset($usuarios_con_estilo[$usuario]) && $usuarios_con_estilo[$usuario]['pwd'] === $pwd) {
        // Credenciales correctas → crear sesión
        $_SESSION['usuario_id'] = $usuario;
        $_SESSION['estilo'] = $usuarios_con_estilo[$usuario]['estilo'];

        // ------------------------------------------------------------------
        // LÓGICA DE "RECORDARME" Y ÚLTIMA VISITA
        // ------------------------------------------------------------------
        if ($recordar) {
            $last_visit_antes = $_COOKIE["last_visit_recordar"] ?? null; 
            
            // Almacenamos el valor a mostrar en una variable de SESIÓN PERSISTENTE
            if($last_visit_antes) {
                $_SESSION['last_visit_time_to_show'] = $last_visit_antes; 
            } else {
                $_SESSION['last_visit_time_to_show'] = date("c");
            }
            
            // Lógica de 90 días: NO actualizar cookies si ya existen
            $recordar_existente = (isset($_COOKIE["recordar_usuario"]) && $_COOKIE["recordar_usuario"] === $usuario);

            if (!$recordar_existente) {
                setcookie("recordar_usuario", $usuario, $cookie_lifetime, "/");
                setcookie("recordar_pass", $pwd, $cookie_lifetime, "/");
            }
            
            // Actualizamos la cookie last_visit_recordar con la hora actual
            $current_time = date("c");
            setcookie("last_visit_recordar", $current_time, $cookie_lifetime, "/");

        } else {
            // Borrar cookies si no marcó
            setcookie("recordar_usuario", '', time() - 3600, '/');
            setcookie("recordar_pass", '', time() - 3600, '/');
            setcookie("last_visit_recordar", '', time() - 3600, '/');
            // Si el usuario no marcó recordar, eliminamos el mensaje de última visita
            unset($_SESSION['last_visit_time_to_show']); 
        }

        header("Location: menuRegistradoUsu.php");
        exit;
    } else {
        $_SESSION['mensaje_error_login'] = "Datos de Usuario inválidos.";
        header("Location: login.php?usuario=" . urlencode($usuario));
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>