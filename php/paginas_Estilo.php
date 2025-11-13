<?php
// CARGAR el estilo desde COOKIE
$estilo_usuario = $_COOKIE['estilo'] ?? 'estilo.css';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Mi Sitio Web'); ?></title>

    <!-- Cargar el estilo principal desde la cookie -->
    <link rel="stylesheet" href="../css/<?php echo htmlspecialchars($estilo_usuario); ?>">
    
    <!-- Estilos específicos que siempre se cargan -->
    <link rel="stylesheet" type="text/css" href="../css/impreso.css" media="print" /> 
    <link rel="stylesheet" href="../css/fontello.css"> 
    
    <?php
    // Lista de todos los estilos disponibles
    $estilos_disponibles = [
        'estilo.css' => 'Estilo por defecto',
        'contraste.css' => 'Estilo de alto contraste',
        'noche.css' => 'Estilo modo noche',
        'letra_y_contraste.css' => 'Alto contraste y letra grande',
        'letra_grande.css' => 'Aumentar Letra'
    ];
    
    // Cargar los demás estilos como alternos (solo si no es el estilo activo)
    foreach ($estilos_disponibles as $archivo => $titulo) {
        if ($archivo !== $estilo_usuario) {
            echo '<link rel="alternate stylesheet" type="text/css" href="../css/' . htmlspecialchars($archivo) . '" title="' . htmlspecialchars($titulo) . '" />' . "\n    ";
        }
    }
    ?>
</head>
<body>