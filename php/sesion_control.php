<?php
// ==========================================================
// sesion_control.php 
// ==========================================================

session_start();

$usuarios_con_estilo = [
    "leigh" => ["pwd" => "1234", "estilo" => "contraste.css"],
    "hugo"  => ["pwd" => "abcd", "estilo" => "letra_grande.css"],
    "maria" => ["pwd" => "pass", "estilo" => "letra_y_contraste.css"],
    "saray" => ["pwd" => "1111", "estilo" => "estilo.css"],
    "prueba"=> ["pwd" => "9876", "estilo" => "estilo.css"] 
];

$cookie_lifetime = time() + (90 * 24 * 60 * 60); // 90 dias

$ultima_visita = "Esta es tu primera visita con la opción 'Recordarme'."; 

// ----------------------------------------------------------
// RECUPERACION POR COOKIES - recuerdame
// ----------------------------------------------------------

$session_restored_by_cookie = false; 

if (!isset($_SESSION['usuario_id'])) {
    if (isset($_COOKIE['recordar_usuario']) && isset($_COOKIE['recordar_pass'])) {
        $usuario_cookie = $_COOKIE['recordar_usuario'];
        $pwd_cookie     = $_COOKIE['recordar_pass'];

        if (isset($usuarios_con_estilo[$usuario_cookie]) && $usuarios_con_estilo[$usuario_cookie]['pwd'] === $pwd_cookie) {
            
            // Restaurar sesión
            $_SESSION['usuario_id'] = $usuario_cookie;
            $session_restored_by_cookie = true;
            
            // Guardar estilo en COOKIE
            $estilo_usuario = $usuarios_con_estilo[$usuario_cookie]['estilo'];
            setcookie('estilo', $estilo_usuario, $cookie_lifetime, '/', '', false, true);
            
            // ultima visita a la pagina
            if (isset($_COOKIE['last_visit_recordar'])) {
                // el valor actual es la ultima visita 
                $ultima_visita = $_COOKIE['last_visit_recordar'];
                
                $_SESSION['last_visit_time_to_show'] = $ultima_visita;
                
                // actualizar la cookie last_visit_recordar con la hora actual
                $current_time = date("c"); 
                setcookie("last_visit_recordar", $current_time, $cookie_lifetime, "/", '', false, true);
            }
        }
    }
}

// si la sesion ya esta activa usamos esa hora de inicio
if (isset($_SESSION['usuario_id']) && isset($_SESSION['last_visit_time_to_show'])) {
    $ultima_visita = $_SESSION['last_visit_time_to_show'];
}


// ----------------------------------------------------------
// FORMATO DE FECHA Y HORA
// ----------------------------------------------------------
if ($ultima_visita !== "Esta es tu primera visita con la opción 'Recordarme'.") {
    try {
        $fecha = new DateTime($ultima_visita);
        $ultima_visita = "Tu última visita fue el " . $fecha->format("d/m/y") . " a las " . $fecha->format("H:i");

    } catch (Exception $e) {
        $ultima_visita = "Última visita registrada (Error de formato): " . $ultima_visita; 
    }
}

// Establecer estilo por defecto EN COOKIE
if (!isset($_COOKIE['estilo'])) {
    setcookie('estilo', 'estilo.css', $cookie_lifetime, '/', '', false, true);
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>