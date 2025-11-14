<?php
// -------------------------------------------------------------
// Página: ver_fotos_privado.php - Con Sesion
// -------------------------------------------------------------

session_start();
ob_start();

// si no esta logueado, redirigir
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

include "funciones_anuncios.php";

// Obtenemos el ID del anuncio de la URL
$id_anuncio = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Obtenemos los datos del anuncio
$anuncio_data = obtener_detalle_y_fotos_anuncio($id_anuncio);

$titulo_pagina = "Mis Fotos del Anuncio";
include "paginas_Estilo.php";
include "header.php";

// Mostramos la galería
mostrar_galeria_fotos($anuncio_data, true); // true = vista privada

include "footer.php";
ob_end_flush();
?>