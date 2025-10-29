<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //pillo los valores
    $usuario = trim($_POST["usuario"] ?? "");
    $pwd = trim($_POST["pwd"] ?? "");
    $pwd2 = trim($_POST["pwd2"] ?? "");

    if ($usuario == "" || $pwd == "" || $pwd2 == "") {
        // Redirigir de vuelta con error de campos vacíos 
        header("Location: ../registro.html?error=empty");
        exit; // [cite: 570]
    }

    if ($pwd !== $pwd2) {
        // Redirigir de vuelta con error de campos que no coinciden
        header("Location: ../registro.html?error=pwd_nocoinciden");
        exit; // [cite: 570]
    }

//plantilla
// require_once "cabecera.php";
// require_once "inicio.php";

    echo "<h2>¡Registro completado con éxito!</h2>";
    echo "<p>Gracias por registrarte.</p>";
    echo "<p>Tus datos de registro son:</p>";
    echo "<ul>";
    echo "<li>Usuario: " . htmlspecialchars($usuario) . "</li>";
    echo "<li>Contraseña: [***Oculta por seguridad***]</li>";
    echo "</ul>";

    //plantilla
// require_once "pie.php";
    exit;
}else {
    // Si se accede directamente al script sin enviar el formulario
    header("Location: ../registro.html");
    exit;
}
?>
