<?php
//usuarios válidos
$usuario1 = "leigh";  $pwd1 = "1234";
$usuario2 = "hugo";   $pwd2 = "abcd";
$usuario3 = "maria";  $pwd3 = "pass";
$usuario4 = "saray";  $pwd4 = "1111";

//si el formulario se envió por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $pwd = trim($_POST["pwd"]);

    //si está vacío, mostramos el mismo mensaje q en js
    if ($usuario === "" || $pwd === "") {
        // Redirigir de vuelta con error de campos vacíos 
        header("Location: ../login.html?error=empty");
        exit; // [cite: 570]
    }

    $usuario_valido = false;

    //si coincide con alguno de los usuarios válidos
    if (($usuario == $usuario1 && $pwd == $pwd1) ||
        ($usuario == $usuario2 && $pwd == $pwd2) ||
        ($usuario == $usuario3 && $pwd == $pwd3) ||
        ($usuario == $usuario4 && $pwd == $pwd4)) {

        $usuario_valido = true;
    }

    if ($usuario_valido) {
        // ÉXITO: Redirigir a la página de usuario registrado
        header("Location: ../index_registrado.html");
        exit;
    } else {
        // FALLO: Redirigir a login con mensaje de error en la URL
        header("Location: ../login.html?error=incorrect");
        exit;
    }
}else {
    // Si se accede directamente al script sin enviar el formulario
    header("Location: ../login.html");
    exit;
}
?>
