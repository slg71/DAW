<?php
include "sesion_control.php"; // Control central de sesión y cookies
$titulo_pagina = "Menú Usuario - Pisos & Inmuebles";
include "paginas_Estilo.php";
?>

<main id="usuariomenu">
  <?php

    $hora = date("H");
    $usuario = ucfirst($_SESSION['usuario_nombre']);

    if ($hora >= 6 && $hora <= 11) {
        echo "<h2>Buenos días, $usuario.</h2>";
    } elseif ($hora >= 12 && $hora <= 15) {
        echo "<h2>Hola, $usuario.</h2>";
    } elseif ($hora >= 16 && $hora <= 19) {
        echo "<h2>Buenas tardes, $usuario.</h2>";
    } else {
        echo "<h2>Buenas noches, $usuario.</h2>";
    }

    // Usamos $ultima_visita que ya viene formateada desde sesion_control.php
    echo "<p>$ultima_visita</p>";
  ?>

    <h3>Opciones</h3>
    <nav aria-label="Menú principal de usuario">
        <a href="inicio_registrado.php"><i class="icon-home"></i>Inicio</a>
        <a href="configurar.php"><i class="icon-eye"></i>Cambiar estilo</a>
        <a href="mis_anuncios.php"><i class="icon-eye"></i>Visualizar mis anuncios</a>
        <a href="crear_anuncio.php"><i class="icon-plus-outline"></i>Crear un anuncio nuevo</a>
        <a href="mismensajes.php"><i class="icon-mail"></i>Ver mensajes enviados y recibidos</a>
        <a href="solicitar_folleto.php"><i class="icon-doc"></i>Solicitar folleto publicitario impreso</a>
        <a href="baja.php"><i class="icon-logout"></i>Darme de baja</a>
        <a href="salir.php"><i class="icon-logout"></i>Salir</a>
    </nav>
</main>

<?php include "footer.php"; ?>
</body>
</html>
