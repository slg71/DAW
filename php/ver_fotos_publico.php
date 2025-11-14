<?php
// -------------------------------------------------------------
// Página: ver_fotos_publico.php - SIN Sesion
// -------------------------------------------------------------

session_start();
ob_start();

include "funciones_anuncios.php";

// Obtenemos el ID del anuncio de la URL
$id_anuncio = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Obtenemos los datos del anuncio
$anuncio_data = obtener_detalle_y_fotos_anuncio($id_anuncio);

$titulo_pagina = "Fotos del Anuncio";
include "paginas_Estilo.php";
include "header.php";

mostrar_galeria_fotos($anuncio_data, false); // false = vista publica

include "footer.php";
ob_end_flush();
?>