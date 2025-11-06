<?php
// ==========================================================
// sesion_control.php — Gestión unificada de sesión y cookies (CORREGIDO FINAL)
// ==========================================================

session_start();

// Lista de usuarios válidos (con su estilo asignado)
$usuarios_con_estilo = [
    "leigh" => ["pwd" => "1234", "estilo" => "contraste.css"],
    "hugo"  => ["pwd" => "abcd", "estilo" => "letra_grande.css"],
    "maria" => ["pwd" => "pass", "estilo" => "letra_y_contraste.css"],
    "saray" => ["pwd" => "1111", "estilo" => "estilo.css"],
    "prueba"=> ["pwd" => "9876", "estilo" => "estilo.css"] 
];
$cookie_lifetime = time() + (90 * 24 * 60 * 60);

// Se inicializa con el mensaje por defecto
$ultima_visita = "Esta es tu primera visita con la opción 'Recordarme'."; 

// ----------------------------------------------------------
// LÓGICA DE RECUPERACIÓN DE SESIÓN POR COOKIES (Recordarme)
// ----------------------------------------------------------

$session_restored_by_cookie = false; // Flag para saber si se restauró por cookie

if (!isset($_SESSION['usuario_id'])) {
    if (isset($_COOKIE['recordar_usuario']) && isset($_COOKIE['recordar_pass'])) {
        $usuario_cookie = $_COOKIE['recordar_usuario'];
        $pwd_cookie     = $_COOKIE['recordar_pass'];

        if (isset($usuarios_con_estilo[$usuario_cookie]) && $usuarios_con_estilo[$usuario_cookie]['pwd'] === $pwd_cookie) {
            
            // Restaurar sesión y estilo
            $_SESSION['usuario_id'] = $usuario_cookie;
            $_SESSION['estilo'] = $usuarios_con_estilo[$usuario_cookie]['estilo'];
            $session_restored_by_cookie = true;
            
            // Gestionar la Última Visita de Recuerdo
            if (isset($_COOKIE['last_visit_recordar'])) {
                // 1. El valor ACTUAL de la cookie es la ÚLTIMA visita a mostrar
                $ultima_visita = $_COOKIE['last_visit_recordar'];
                
                // 2. Almacenar en sesión para que sea visible en TODAS las páginas de esta sesión
                $_SESSION['last_visit_time_to_show'] = $ultima_visita;
                
                // 3. Actualizar la cookie last_visit_recordar con la HORA ACTUAL (para la próxima visita)
                $current_time = date("c"); 
                setcookie("last_visit_recordar", $current_time, $cookie_lifetime, "/");
            }
        }
    }
}

// ----------------------------------------------------------
// LÓGICA PARA CARGAS SUBSECUENTES (EL FIX)
// ----------------------------------------------------------
// Si la sesión está activa y ya tenemos una hora de última visita registrada en la sesión actual, la usamos.
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

// ----------------------------------------------------------
// GESTIÓN DE ESTILO Y CONTROL DE ACCESO
// ----------------------------------------------------------

if (!isset($_SESSION['estilo'])) {
    $_SESSION['estilo'] = "estilo.css"; 
}

// Si sigue sin haber sesión (ni por login ni por cookie), redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>