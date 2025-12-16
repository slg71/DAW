<?php
// -------------------------------------------------------------
// Página: ver_fotos_privado.php - Con Sesion
// -------------------------------------------------------------

ob_start();

require_once "sesion_control.php";
require_once "funciones_anuncios.php";
require_once "funciones_imagenes.php"; // Necesario para generar_miniatura()
// Obtenemos el ID del anuncio de la URL
$id_anuncio = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$anuncio_data = obtener_detalle_y_fotos_anuncio($id_anuncio);

// Obtenemos los datos del anuncio
$titulo_pagina = "Mis Fotos del Anuncio";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <?php 
    
    mostrar_galeria_fotos_paginada($anuncio_data, true, 2); // true = vista privada
    ?>
</main>

<?php 
include "footer.php";
ob_end_flush();
?>