<?php
session_start();
ob_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_pagina = "Ver mi Anuncio";
include "paginas_Estilo.php";
include "header.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$es_privada = true;

include "mostrar_detalle_anuncio.php";

include "footer.php";
ob_end_flush();
?>