<?php
session_start();

// 1. Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_pagina = "Resultado de la operación";
require_once "paginas_Estilo.php";
include "header.php";
?>

<main>
    <h2>Resultado de la operación</h2>

    <section style="text-align:center; margin-top:20px;">

        <h3>¡Tu anuncio se ha guardado correctamente!</h3>

        <p style="margin-top:15px;">
            <!-- Botón: Mis Anuncios -->
            <a href="mis_anuncios.php" class="boton">Mis Anuncios</a>

            <!-- Botón: Menú Usuario -->
            <a href="MenuRegistradoUsu.php" class="boton" style="margin-left:10px;">Menú Usuario</a>
        </p>

    </section>
</main>

<?php include "footer.php"; ?>
