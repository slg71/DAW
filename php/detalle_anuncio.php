<?php
ob_start();
require_once "sesion_control.php";

// 1. Recoger el ID
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// 2. Guardar el ID en el historial
if ($id > 0) {
    $nombre_cookie = 'ultimos_anuncios';
    $ids_visitados = [];

    // Si ya existe la cookie, recuperamos los IDs anteriores
    if (isset($_COOKIE[$nombre_cookie])) {
        $ids_visitados = json_decode($_COOKIE[$nombre_cookie], true);
        // Validación básica
        if (!is_array($ids_visitados)) {
            $ids_visitados = [];
        }
    }

    // Si el ID ya estaba en la lista, lo quitamos para volver a ponerlo al final (el más reciente)
    $pos = array_search($id, $ids_visitados);
    if ($pos !== false) {
        unset($ids_visitados[$pos]);
    }

    // Añadimos el ID actual al final de la lista
    $ids_visitados[] = $id;
    
    // Reordenamos índices
    $ids_visitados = array_values($ids_visitados);

    // Limitamos a los últimos 5 visitados para no llenar la cookie
    if (count($ids_visitados) > 5) {
        // Quitamos los más antiguos del principio
        $ids_visitados = array_slice($ids_visitados, -5);
    }

    // Guardamos la cookie actualizada (duración 30 días)
    setcookie($nombre_cookie, json_encode($ids_visitados), time() + (86400 * 30), "/");
}

// 3. Continuamos con la carga normal de la página
$titulo_pagina = "Detalle del anuncio";
include "paginas_Estilo.php";
include "header.php";

$es_privada = false;

include "mostrar_detalle_anuncio.php"; //

include "footer.php";
ob_end_flush();
?>