<?php
ob_start();

require_once "sesion_control.php";

$titulo_pagina = "Detalle del anuncio";
include "paginas_Estilo.php";
include "header.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$es_privada = false;

include "mostrar_detalle_anuncio.php";

include "footer.php";
ob_end_flush();
?>