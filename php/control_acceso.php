<?php
// ======================================================
// control_acceso.php — Validacion de inicio de sesion
// ======================================================

$usuarios_con_estilo = [
    "leigh" => ["pwd" => "1234", "estilo" => "contraste.css"],
    "hugo"  => ["pwd" => "abcd", "estilo" => "letra_grande.css"],
    "maria" => ["pwd" => "pass", "estilo" => "estilo.css"],
    "saray" => ["pwd" => "1111", "estilo" => "estilo.css"],
    "prueba"=> ["pwd" => "9876", "estilo" => "estilo.css"] 
];

$cookie_lifetime = time() + (90 * 24 * 60 * 60);//90 dias

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    $usuario = trim($_POST["usuario"] ?? "");
    $pwd     = trim($_POST["pwd"] ?? "");
    $recordar = isset($_POST["recordar"]); 

    // Validar credenciales
    if (isset($usuarios_con_estilo[$usuario]) && $usuarios_con_estilo[$usuario]['pwd'] === $pwd) {
        // si las credenciales son correctas iniciamos sesion
        $_SESSION['usuario_id'] = $usuario;
        
        // GUARDAR el estilo en COOKIE
        $estilo_usuario = $usuarios_con_estilo[$usuario]['estilo'];
        setcookie('estilo', $estilo_usuario, $cookie_lifetime, '/', '', false, true);

        // ------------------------------------------------------------------
        // RECUERDAME Y ULTIMA VISITA
        // ------------------------------------------------------------------
        if ($recordar) {
            $last_visit_antes = $_COOKIE["last_visit_recordar"] ?? null; 
            
            // sesion continua
            if($last_visit_antes) {
                $_SESSION['last_visit_time_to_show'] = $last_visit_antes; 
            } else {
                $_SESSION['last_visit_time_to_show'] = date("c");
            }
            
            // no actualizar cookies si ya existen
            $recordar_existente = (isset($_COOKIE["recordar_usuario"]) && $_COOKIE["recordar_usuario"] === $usuario);

            if (!$recordar_existente) {
                // Añadir parámetros de seguridad a setcookie
                setcookie("recordar_usuario", $usuario, $cookie_lifetime, "/", '', false, true);
                setcookie("recordar_pass", $pwd, $cookie_lifetime, "/", '', false, true);
            }
            
            // actualizar la cookie last_visit_recordar con la hora actual
            $current_time = date("c");
            setcookie("last_visit_recordar", $current_time, $cookie_lifetime, "/", '', false, true);

        } else {
            // Si NO marca "recordarme", borrar las cookies de recordar
            setcookie("recordar_usuario", '', time() - 3600, '/');
            setcookie("recordar_pass", '', time() - 3600, '/');
            setcookie("last_visit_recordar", '', time() - 3600, '/');
            // si no quiere que lo recordemos lo olvidamos
            unset($_SESSION['last_visit_time_to_show']); 
            
        }

        header("Location: configurar.php");
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