<?php
// -------------------------------------------------------------
// Archivo: funciones_costes.php
// Funciones para calcular el coste de folletos publicitarios
// -------------------------------------------------------------

/**
 * Calcula el coste de las paginas segun las tarifas:
 * - < 5 paginas: 2€ por pagina
 * - Entre 5 y 10 páginas: 1,8€ por pagina
 * - > 10 paginas: 1,6€ por pagina
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
        // Mas de 10: primeras 4 a 2€, siguientes 6 a 1.8€, resto a 1.6€
        $coste = (4 * 2.0) + (6 * 1.8) + (($numPaginas - 10) * 1.6);
    }
    
    return $coste;
}

/**
 * Calcula el coste total de un folleto publicitario
 * 
 * Tarifas:
 * - Coste procesamiento y envio: 10€
 * - Paginas: según calcular_coste_paginas()
 * - Color: 0,5€ por foto
 * - Blanco y Negro: 0€
 * - Resolucion ≤ 300 dpi: 0€ por foto
 * - Resolucion > 300 dpi: 0,2€ por foto
 */
function calcular_coste_folleto($numPaginas, $numFotos, $esColor, $resolucion) {
    // Coste base de proceso y envio
    $coste = 10.0;
    // Añadir coste de paginas
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