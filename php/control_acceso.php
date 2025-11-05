<?php
// ======================================================
// control_acceso.php  — Validación completa en servidor
// ======================================================

$usuarios_validos = [
    "leigh" => "1234",
    "hugo"  => "abcd",
    "maria" => "pass",
    "saray" => "1111"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $pwd     = trim($_POST["pwd"] ?? "");

    // Validaciones básicas
    if ($usuario === "" || $pwd === "") {
        header("Location: login.php?error=empty");
        exit;
    }

    // Validación de formato de usuario
    if (strlen($usuario) < 3 || strlen($usuario) > 15) {
        header("Location: login.php?error=formato_usuario");
        exit;
    }
    if (is_numeric($usuario[0])) {
        header("Location: login.php?error=usuario_numero");
        exit;
    }
    if (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        header("Location: login.php?error=usuario_invalido");
        exit;
    }

    // Validar credenciales
    if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] === $pwd) {
        session_start();
        // Guardamos los datos del usuario en la sesión
        $_SESSION['usuario_id'] = $usuario;
        header("Location: inicio_registrado.php");
        exit;
    } else {
        header("Location: login.php?error=incorrect");
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>
