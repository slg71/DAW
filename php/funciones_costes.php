<?php
function calcular_coste_paginas($numPaginas) {
    $coste = 0.0;
    
    if ($numPaginas <= 4) {
        $coste = $numPaginas * 2.0;
    } elseif ($numPaginas <= 10) {
        $coste = (4 * 2.0) + (($numPaginas - 4) * 1.8);
    } else { 
        $coste = (4 * 2.0) + (6 * 1.8) + (($numPaginas - 10) * 1.6);
    }
    
    return $coste;
}

function calcular_coste_folleto($numPaginas, $numFotos, $esColor, $resolucion) {
    $coste = 10.0; // Base
    $coste += calcular_coste_paginas($numPaginas);
    if ($esColor) {
        $coste += 0.5 * $numFotos;
    }
    if ($resolucion > 300) {
        $coste += 0.2 * $numFotos;
    }
    return round($coste, 2); 
}
?>
