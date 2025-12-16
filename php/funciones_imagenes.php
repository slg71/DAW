<?php
// ============================================
// funciones para crear gráficos y miniaturas
// ============================================

// ============================================
// crea el gráfico de barras de los ultimos 7 dias
// paramtreos= array

function generar_grafico($datos_dias) {
    //configurar lienzo
    $ancho = 600;
    $alto = 300;
    $margen = 40;

    //crear lienzo negro
    $imagen = imagecreatetruecolor($ancho, $alto);

    //definir colores
    $color_fondo = imagecolorallocate($imagen, 240, 240, 240);// Gris clarito
    $color_barra = imagecolorallocate($imagen, 100, 149, 237);// Azul bonito
    $color_texto = imagecolorallocate($imagen, 50, 50, 50);// Gris oscuro para letras
    $color_ejes = imagecolorallocate($imagen, 0, 0, 0);// Negro

    //pinto el fondo
    imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $color_fondo);

    //dibujar ejes X,Y
    //Y
    imageline($imagen, $margen, $margen, $margen, $alto - $margen, $color_ejes);
    //X
    imageline($imagen, $margen, $alto - $margen, $ancho - $margen, $alto - $margen, $color_ejes);

    //dibujar las barras
    $num_barras=count($datos_dias);
    $ancho_barra = ($ancho - (2*$margen)) / $num_barras-10;//-10 es un hueco entre las barras
    $max_valor = max($datos_dias) > 0 ? max($datos_dias) : 1;//evito que se divida por 0
    $x = $margen + 10;

    foreach($datos_dias as $fecha => $cantidad) {
        //calculo la altura proporcional
        $altura_barra = ($cantidad / $max_valor) * ($alto - (2 * $margen)); //lo ultimo siendo la altura disponible

        //coordenadas del rectangulo
        $x1 = $x;
        $y1 = ($alto - $margen) - $altura_barra;//Y crece hacia abajo, así que restamos
        $x2 = $x + $ancho_barra;
        $y2 = $alto - $margen - 1; //Justo encima del eje X

        //dibujar barra
        imagefilledrectangle($imagen, $x1, $y1, $x2, $y2, $color_barra);

        //escribir cantidad encima de barra
        imagestring($imagen, 3, $x1 + 5, $y1 - 15, $cantidad, $color_texto);

        //escrbir fecha debajo
        imagestring($imagen, 2, $x1, $alto - $margen + 5, substr($fecha, 0, 5), $color_texto);

        //mover X para la siguiente barra
        $x += $ancho_barra + 10;
    }

    //salida en base64
    ob_start();//capturar la salida
    imagepng($imagen);//generar el PNG
    $datos_imagen = ob_get_contents();//guardar en variable
    ob_end_clean();//limpiar el buffer
    
    imagedestroy($imagen); //liberar memoria RAM
    
    return 'data:image/png;base64,' . base64_encode($datos_imagen);
}

// ============================================


// ============================================
//convertir una imagen grande en miniatura

// function generar_miniatura($ruta_imagen, $ancho_deseado = 150) {
//     //si no existe, se sale
//     if (!file_exists($ruta_imagen)) {
//         return ""; 
//     }

//     //obtener info de la imagen
//     $info = getimagesize($ruta_imagen);
//     $ancho_orig = $info[0];
//     $alto_orig = $info[1];
//     $tipo_mime = $info['mime'];

//     //crear lienzo original segun el tipo (jpg o png)
//     if ($tipo_mime == 'image/jpeg') {
//         $img_origen = imagecreatefromjpeg($ruta_imagen);
//     } elseif ($tipo_mime == 'image/png') {
//         $img_origen = imagecreatefrompng($ruta_imagen);
//     } else {
//         return ""; // Si no es jpg/png, se sale
//     }

//     //calcular altura proporcional
//     $alto_deseado = ($alto_orig / $ancho_orig) * $ancho_deseado;

//     //crear lienzo vacio para la miniatura
//     $img_destino = imagecreatetruecolor($ancho_deseado, $alto_deseado);

//     //redimensionar     destino         origne  dst_x,y,src_x,y     dst_w       dst_h           src_w       src_h
//     imagecopyresampled($img_destino, $img_origen, 0, 0, 0, 0, $ancho_deseado, $alto_deseado, $ancho_orig, $alto_orig);

//     //volcar a base64
//     ob_start();
//     imagejpeg($img_destino);//sacar siempre como JPG que ocupa menos
//     $datos = ob_get_contents();
//     ob_end_clean();
    
//     //limpiar
//     imagedestroy($img_origen);
//     imagedestroy($img_destino);
    
//     return 'data:image/jpeg;base64,' . base64_encode($datos);
// }
// 


// funciones_imagenes.php

/**
 * Genera una miniatura física en el servidor si no existe.
 * @param string $nombre_archivo Nombre del archivo (ej: 'casa.jpg')
 * @param int $ancho_deseado Por defecto 800px según enunciado
 * @return string Ruta para usar en el atributo src de <img>
 */
// funciones_imagenes.php

/**
 * Genera una miniatura física en el servidor y devuelve su ruta.
 */
function generar_miniatura($nombre_archivo, $ancho_deseado = 800) {
    $dir_original = "../img/";
    $dir_miniaturas = "../img/miniaturas/";
    $ruta_original = $dir_original . $nombre_archivo;
    
    // Si no hay nombre de archivo o no existe la original, salimos
    if (empty($nombre_archivo) || !file_exists($ruta_original)) {
        return "../img/default.jpg"; 
    }

    // Nombre único para la miniatura basado en el ancho
    $nombre_miniatura = "thumb_" . $ancho_deseado . "_" . $nombre_archivo;
    $ruta_destino_fisica = $dir_miniaturas . $nombre_miniatura;
    $ruta_retorno_web = "../img/miniaturas/" . $nombre_miniatura;

    // Si ya existe, no la procesamos de nuevo (ahorro de recursos)
    if (file_exists($ruta_destino_fisica)) {
        return $ruta_retorno_web;
    }

    // Crear la carpeta si no existe
    if (!is_dir($dir_miniaturas)) {
        mkdir($dir_miniaturas, 0755, true);
    }

    // Procesamiento con GD
    $info = getimagesize($ruta_original);
    $ancho_orig = $info[0];
    $alto_orig = $info[1];
    $tipo_mime = $info['mime'];

    // Cargar imagen según tipo
    if ($tipo_mime == 'image/jpeg') {
        $img_origen = imagecreatefromjpeg($ruta_original);
    } elseif ($tipo_mime == 'image/png') {
        $img_origen = imagecreatefrompng($ruta_original);
    } else {
        return "../img/" . $nombre_archivo; // Fallback a la original si no es compatible
    }

    // Calcular proporción original para el redimensionado
    $alto_deseado = ($alto_orig / $ancho_orig) * $ancho_deseado;

    // Crear lienzo y reescalar con alta calidad (imagecopyresampled)
    $img_destino = imagecreatetruecolor($ancho_deseado, $alto_deseado);
    
    // Preservar transparencia para PNGs
    if ($tipo_mime == 'image/png') {
        imagealphablending($img_destino, false);
        imagesavealpha($img_destino, true);
    }

    imagecopyresampled($img_destino, $img_origen, 0, 0, 0, 0, $ancho_deseado, $alto_deseado, $ancho_orig, $alto_orig);

    // Guardar físicamente como JPEG para optimizar peso
    imagejpeg($img_destino, $ruta_destino_fisica, 80); 

    // Liberar memoria RAM
    imagedestroy($img_origen);
    imagedestroy($img_destino);

    return $ruta_retorno_web;
}
?>