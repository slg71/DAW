<?php
require_once "sesion_control.php";

$_SESSION = array();

// 2. Borrar la cookie que almacena el identificador de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// 3. Borrar las cookies de "recordar usuario"
if (isset($_COOKIE['recordar_usuario'])) {
    setcookie('recordar_usuario', '', time() - 3600, '/');
}

if (isset($_COOKIE['recordar_pass'])) {
    setcookie('recordar_pass', '', time() - 3600, '/');
}

if (isset($_COOKIE['last_visit_recordar'])) {
    setcookie('last_visit_recordar', '', time() - 3600, '/');
}

// 4. Borrar la cookie de estilo
if (isset($_COOKIE['estilo'])) {
    setcookie('estilo', '', time() - 3600, '/');
}

// 5. Finalmente, destruir la sesión
session_destroy();

header('Location: index.php');
exit;
?>