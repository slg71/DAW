<?php
// -------------------------------------------------------------
// Página: index.php -Publivo- SIN Sesion
// -------------------------------------------------------------
session_start();

$titulo_pagina = "Inicio - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";

//conexion a la base de datos
require_once("conexion_bd.php"); 
$usuario_registrado = isset($_SESSION['usuario_id']);

include "funciones_imagenes.php";
?>

<main>
    <a href="#listado" class="saltar">Saltar al contenido principal</a>

    <?php include "bloque_busqueda.php"; ?>
    
    <?php include "bloque_estadisticas.php"; ?>

    <?php include "bloque_ultimos_anuncios.php"; ?>
</main>
<?php include "footer.php"; ?>
</body>
</html>