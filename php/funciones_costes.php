<?php
// -------------------------------------------------------------
// Archivo: funciones_costes.php
// Funciones para calcular el coste de folletos publicitarios
// -------------------------------------------------------------

/**
 * Calcula el coste de las páginas según las tarifas:
 * - < 5 páginas: 2€ por página
 * - Entre 5 y 10 páginas: 1,8€ por página
 * - > 10 páginas: 1,6€ por página
 * 
 * @param int $numPaginas Número de páginas del folleto
 * @return float Coste total de las páginas
 */
function calcular_coste_paginas($numPaginas) {
    $coste = 0.0;
    
    if ($numPaginas < 5) {
        // Menos de 5 páginas: todas a 2€
        $coste = $numPaginas * 2.0;
    } elseif ($numPaginas <= 10) {
        // Entre 5 y 10: primeras 4 a 2€, resto a 1.8€
        $coste = (4 * 2.0) + (($numPaginas - 4) * 1.8);
    } else { 
        // Más de 10: primeras 4 a 2€, siguientes 6 a 1.8€, resto a 1.6€
        $coste = (4 * 2.0) + (6 * 1.8) + (($numPaginas - 10) * 1.6);
    }
    
    return $coste;
}

/**
 * Calcula el coste total de un folleto publicitario
 * 
 * Tarifas:
 * - Coste procesamiento y envío: 10€
 * - Páginas: según calcular_coste_paginas()
 * - Color: 0,5€ por foto
 * - Blanco y Negro: 0€
 * - Resolución ≤ 300 dpi: 0€ por foto
 * - Resolución > 300 dpi: 0,2€ por foto
 * 
 * @param int $numPaginas Número de páginas
 * @param int $numFotos Número de fotos
 * @param bool $esColor true si es a color, false si es blanco y negro
 * @param int $resolucion Resolución en dpi
 * @return float Coste total del folleto
 */
function calcular_coste_folleto($numPaginas, $numFotos, $esColor, $resolucion) {
    // Coste base: procesamiento y envío
    $coste = 10.0;
    
    // Añadir coste de páginas
    $coste += calcular_coste_paginas($numPaginas);
    
    // Añadir coste de color (0.5€ por foto si es color)
    if ($esColor) {
        $coste += 0.5 * $numFotos;
    }
    
    // Añadir coste de resolución alta (0.2€ por foto si > 300 dpi)
    if ($resolucion > 300) {
        $coste += 0.2 * $numFotos;
    }
    
    return round($coste, 2); 
}
?>