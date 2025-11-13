<?php
session_start();
ob_start();

// Comprobación de seguridad: si no está logueado, redirigir
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Página: ver_fotos_privado.php
// Privada (requiere sesión)
// Muestra las fotos de un anuncio específico.
// -------------------------------------------------------------

// Incluimos las funciones de la BD para el anuncio y la galería
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