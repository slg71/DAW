<?php
session_start(); // Inicia para poder acceder a la sesión

// 1. Borra todas las variables de sesión
$_SESSION = array();

// 2. Borra las cookies de "Recordarme" (poniendo fecha pasada) [cite: 146, 247]
if (isset($_COOKIE['recordar_usuario'])) {
    setcookie('recordar_usuario', '', time() - 3600, '/');
    setcookie('recordar_pass', '', time() - 3600, '/');
}

// 3. Destruye la sesión
session_destroy();

// 4. Redirige al inicio
header('Location: index.php');
exit;
?>