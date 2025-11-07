<?php
session_start();
$_SESSION = array();

// borrar cookies y tmb las de la ult visita
if (isset($_COOKIE['recordar_usuario'])) {
    setcookie('recordar_usuario', '', time() - 3600, '/');
    setcookie('recordar_pass', '', time() - 3600, '/');
    setcookie('last_visit_recordar', '', time() - 3600, '/'); 
}

//borrar las cookies de estilo
if (isset($_COOKIE['estilo'])) {
    setcookie('estilo', '', time() - 3600, '/');
}

session_destroy();
//rompre el array e igualar a cero

header('Location: index.php');
exit;
?>