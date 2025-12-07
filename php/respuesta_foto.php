<?php
session_start();
require_once "conexion_bd.php";

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$errores = [];
$exito = false;
$mysqli = conectarBD();

// Variables iniciales
$titulo_foto = '';
$nombre_fichero = '';
$texto_alt = '';
$anuncio_id = 0;
$mensaje_resultado = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anuncio_id = isset($_POST['anuncio']) ? intval($_POST['anuncio']) : 0;
    $titulo_foto = trim($_POST['titulo_foto'] ?? '');
    $texto_alt = trim($_POST['texto_alt'] ?? '');
    $usuario_pide_principal = isset($_POST['es_principal']) && $_POST['es_principal'] == '1';
    
    // Subida de fichero
    $fichero_info = $_FILES['foto'] ?? null;
    $nombre_fichero_original = $fichero_info['name'] ?? '';

    if (empty($titulo_foto)) $errores[] = "El título de la foto es obligatorio.";
    if (empty($anuncio_id)) $errores[] = "Debe seleccionar un anuncio.";
    if ($fichero_info === null || empty($nombre_fichero_original)) {
        $errores[] = "Debes seleccionar un fichero de imagen.";
    }
    
    if (empty($texto_alt)) {
        $texto_alt = $titulo_foto;
    }

    if (empty($errores)) {
        
        if ($fichero_info['error'] !== UPLOAD_ERR_OK) {
            $errores[] = "Error al subir el archivo (Código: {$fichero_info['error']}).";
        }

        $max_size = 5 * 1024 * 1024; 
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $ext_original = strtolower(pathinfo($fichero_info['name'], PATHINFO_EXTENSION));
        $mime_type = $fichero_info['type']; 

        if ($fichero_info['size'] > $max_size) {
            $errores[] = "El tamaño del archivo excede el límite permitido (5MB).";
        }
        if (!in_array($mime_type, $tipos_permitidos) && !in_array('image/'.$ext_original, $tipos_permitidos)) {
            $errores[] = "Tipo de archivo no permitido. Solo se permiten JPEG, PNG, WebP o GIF.";
        }
    }

    // estrategia de colision y subida de a la carpeta img
    if (empty($errores)) {
        
        // Generar nombre de fichero unico: IdAnuncio_Timestamp_Hash.ext
        $nombre_base = $anuncio_id . '_' . time() . '_' . uniqid();
        $nombre_fichero = $nombre_base . '.' . $ext_original;
        $ruta_destino = "../img/" . $nombre_fichero; 
        
        if (!move_uploaded_file($fichero_info['tmp_name'], $ruta_destino)) {
            $errores[] = "Error al mover el fichero al directorio de destino. Compruebe permisos de la carpeta '../img/'.";
        }
    }


    // --- INSERCIÓN EN BASE DE DATOS ---
    if (empty($errores)) {
        
        $fprincipal_actual = null;
        $sql_check_principal = "SELECT FPrincipal FROM anuncios WHERE IdAnuncio = ?";
        $stmt_check_p = $mysqli->prepare($sql_check_principal);
        $stmt_check_p->bind_param("i", $anuncio_id);
        $stmt_check_p->execute();
        $stmt_check_p->bind_result($fprincipal_actual);
        $stmt_check_p->fetch();
        $stmt_check_p->close();

        $tiene_principal_actual = !empty($fprincipal_actual);
        if ($usuario_pide_principal || !$tiene_principal_actual) {
            
            if ($tiene_principal_actual) {
                $ruta_antigua = "../img/" . $fprincipal_actual;
                if (file_exists($ruta_antigua)) {
                    unlink($ruta_antigua);
                }
            }
            
            // Actualizar anuncios
            $sql_update = "UPDATE anuncios SET FPrincipal = ?, Alternativo = ? WHERE IdAnuncio = ?";
            $stmt_update = $mysqli->prepare($sql_update);
            $stmt_update->bind_param("ssi", $nombre_fichero, $texto_alt, $anuncio_id);
            
            if (!$stmt_update->execute()) {
                $errores[] = "Error al asignar como foto principal: " . $stmt_update->error;
            }
            $stmt_update->close();
            
            $mensaje_resultado = "La foto se ha subido y establecido como foto principal del anuncio.";
            
        } else {
            // Insertar en fotos
            $sql_insert = "INSERT INTO fotos (Anuncio, Foto, Titulo, Alternativo) VALUES (?, ?, ?, ?)";
            $stmt_insert = $mysqli->prepare($sql_insert);
            $stmt_insert->bind_param("isss", $anuncio_id, $nombre_fichero, $titulo_foto, $texto_alt);
            
            if (!$stmt_insert->execute()) {
                $errores[] = "Error al insertar la foto secundaria: " . $stmt_insert->error;
            }
            $stmt_insert->close();
            
            $mensaje_resultado = "La foto se ha subido correctamente a la galería secundaria.";
        }

        $exito = empty($errores);
        
        if (!$exito) {
            if (file_exists($ruta_destino)) {
                unlink($ruta_destino);
            }
        }
    }
}
$mysqli->close();

$titulo_pagina = "Confirmación Foto";
require_once "paginas_Estilo.php";
require_once "header.php";
?>

<main>
    <section>
        <h2>Estado de la operación</h2>

        <?php if ($exito): ?>
            <section class ="exito">
                <h3>¡Operación Exitosa!</h3>
                <p><?php echo $mensaje_resultado; ?></p>
                <ul>
                    <li><strong>Título:</strong> <?php echo htmlspecialchars($titulo_foto); ?></li>
                    <li><strong>Archivo:</strong> <?php echo htmlspecialchars($nombre_fichero); ?></li>
                    <li><strong>Alternativo:</strong> <?php echo htmlspecialchars($texto_alt); ?></li>
                </ul>
                
                <button class="ver"><a href="ver_anuncio.php?id=<?php echo $anuncio_id; ?>">Ver Anuncio</a></button>
                <button class="ver"><a href="mis_anuncios.php">Volver a mis anuncios</a></button>
                
            </section>
        <?php else: ?>
            <section class="error">
                <h3>Ha ocurrido un error</h3>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button><a href="javascript:history.back()">Volver al formulario</a></button>
            </section>
        <?php endif; ?>
    </section>
</main>

<?php require_once "footer.php"; ?>