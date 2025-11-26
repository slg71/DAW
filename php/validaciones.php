<?php
// ==========================================================
// validaciones.php — Lógica de validación de datos
// ==========================================================

function validarAnuncio($datos) {
    $errores = [];

    if (empty($datos['Titulo'])) {
        $errores['Titulo'] = "El título es obligatorio.";
    }

    if (empty($datos['Texto'])) {
        $errores['Texto'] = "La descripción del anuncio es obligatoria.";
    }

    // que sea positivo
    if (empty($datos['Precio']) || !is_numeric($datos['Precio']) || $datos['Precio'] < 0) {
        $errores['Precio'] = "El precio debe ser un número válido positivo.";
    }

    if (empty($datos['TAnuncio'])) {
        $errores['TAnuncio'] = "Selecciona un tipo de anuncio (Venta/Alquiler).";
    }
    if (empty($datos['TVivienda'])) {
        $errores['TVivienda'] = "Selecciona un tipo de vivienda.";
    }
    if (empty($datos['Pais'])) {
        $errores['Pais'] = "Debes seleccionar un país.";
    }

    if (!empty($datos['Superficie']) && (!is_numeric($datos['Superficie']) || $datos['Superficie'] < 0)) {
        $errores['Superficie'] = "La superficie debe ser un número válido.";
    }
    
    if (isset($datos['NHabitaciones']) && $datos['NHabitaciones'] !== "" && (!is_numeric($datos['NHabitaciones']) || $datos['NHabitaciones'] < 0)) {
        $errores['NHabitaciones'] = "El nº de habitaciones no es válido.";
    }

    return $errores;
}
?>