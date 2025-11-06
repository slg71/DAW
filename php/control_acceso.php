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
        // 1. Inicia la sesión (si no está iniciada ya)
        session_start();
        
        // 2. Guarda el mensaje de error en la sesión
        $_SESSION['mensaje_error_login'] = "Campo de Usuario o contraseña vacío.";
        
        // 3. Redirige a login.php
        header("Location: login.php");
        exit;
    }

    // Validación de formato de usuario
    if (strlen($usuario) < 3 || strlen($usuario) > 15 || is_numeric($usuario[0]) || !preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        session_start();
        
        $_SESSION['mensaje_error_login'] = "Formato de Usuario inválido.";
        
        header("Location: login.php");
        exit;
    }

<<<<<<< HEAD
    if ($usuario_valido) {
        // EXITO: Redirigir a la página de usuario registrado
        header("Location: ../index_registrado.html");
=======
    // Validar credenciales
    if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] === $pwd) {
        session_start();
        // Guardamos los datos del usuario en la sesión
        $_SESSION['usuario_id'] = $usuario;
        header("Location: inicio_registrado.php");
>>>>>>> origin/leigh
        exit;
    } else {
        session_start();
        
        $_SESSION['mensaje_error_login'] = "Datos de Usuario inválidos.";
        
        header("Location: login.php");
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>
