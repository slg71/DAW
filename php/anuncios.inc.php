<?php
// ---------------------------------------------
// Fichero: anuncios.inc.php
// Contiene los datos de los dos anuncios de ejemplo
// ---------------------------------------------

$anuncio_par = array(
    "id" => 2,
    "tipo_anuncio" => "Venta",
    "tipo_vivienda" => "Piso",
    "titulo" => "Piso moderno con terraza",
    "precio" => 220000,
    "texto" => "Luminoso piso recién reformado con amplia terraza y vistas al mar.",
    "fecha" => "2025-09-18",
    "ciudad" => "Alicante",
    "pais" => "España",
    "caracteristicas" => array(
        "superficie" => "85 m²",
        "habitaciones" => 3,
        "baños" => 2,
        "planta" => "3ª",
        "anio" => 2020
    ),
    "fotos" => array(
        "./img/piso.jpg",
        "./img/piso.jpg",
        "./img/piso.jpg"
    ),
    "usuario" => "maria"
);

$anuncio_impar = array(
    "id" => 1,
    "tipo_anuncio" => "Alquiler",
    "tipo_vivienda" => "Apartamento",
    "titulo" => "Apartamento céntrico renovado",
    "precio" => 900,
    "texto" => "Un apartamento elegante en el centro de Alicante con buenas vistas.",
    "fecha" => "2025-09-15",
    "ciudad" => "Alicante",
    "pais" => "España",
    "caracteristicas" => array(
        "superficie" => "60 m²",
        "habitaciones" => 2,
        "baños" => 1,
        "planta" => "2ª",
        "anio" => 2018
    ),
    "fotos" => array(
        "./img/piso.jpg",
        "./img/piso.jpg",
        "./img/piso.jpg"
    ),
    "usuario" => "leigh"
);
?>
