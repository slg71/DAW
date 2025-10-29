<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //pillo los valores
    $tipo_mensaje = trim($_POST["tipo_mensaje"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    // Tipos válidos
    $tipos_validos = ["informacion", "cita", "oferta"];

    // Validar
    if (!in_array($tipo_mensaje, $tipos_validos) || $mensaje == "") {
        // Redirigir de vuelta con error
        header("Location: ../mensaje.html?error=empty");
        exit;
    }

//plantilla
// require_once "cabecera.php";
// require_once "inicio.php";

    echo "<h1>¡Mensaje Enviado con éxito!</h1>";
    echo "<h2>Detalles del mensaje</h2>";
    echo "<p>Tipo: " . htmlspecialchars($tipo_mensaje) . "</p>";
    echo "<p> Mensaje: " . htmlspecialchars($mensaje) . "</p>";

    //plantilla
// require_once "pie.php";
    exit;
}else {
    // Si se accede directamente al script sin enviar el formulario
    header("Location: ../mensaje.html");
    exit;
}
?>

<!-- el html original:
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar mensaje</title>
    <link rel="stylesheet" href="./css/estilo.css">
    <link rel="alternate stylesheet" type="text/css" href="./css/contraste.css" title="Estilo de alto contraste" />
    <link rel="stylesheet" type="text/css" href="./css/impreso.css" media="print" /> 
    <link rel="alternate stylesheet" href="./css/noche.css" title="Estilo modo noche"/>
    <link rel="alternate stylesheet" type="text/css" href="./css/letra_y_contraste.css" title="Alto contraste y letra grande" />
    <link rel="alternate stylesheet" type="text/css" href="./css/letra_grande.css" title="Aumentar Letra" />
    <link rel="stylesheet" href="./css/fontello.css"> <!-- link que vincula la carpeta paraponer iconos -->
</head>
<body>
    <a href="#bloque" class="saltar">Saltar al contenido principal</a>

    <header>
        <h1>PI - Pisos & Inmuebles</h1>
        <p id="eslogan">Tu nuevo hogar te espera</p>

        <nav>
            <a href="index_registrado.html">Inicio</a>
            <a href="publicar.html">Publicar anuncio</a>
            <a href="MenuRegistradoUsu.html">Menú de Usuario</a>
        </nav>
    </header>

    <main>
        <h1>¡Mensaje Enviado con éxito!</h1>

        <section id="bloque">
            <h2>Detalles del mensaje</h2>
            <p id="tipo_mensaje">Tipo:</p>
            <p id="mensaje">Mensaje:</p>

            <a href="mismensajes.html">Ver mis mensajes</a>
        </section>
    </main>

    <footer>
        <p>© Leigh Garett & Maria Luisa Roca, Grupo 3 de Prácticas</p>
    </footer>
</body>
</html> -->