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
    // Recogemos si el usuario INTENTA que sea principal
    $usuario_pide_principal = isset($_POST['es_principal']) && $_POST['es_principal'] == '1';
    
    $nombre_fichero = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';// Subida de fichero

    // --- VALIDACIONES ---
    if (empty($titulo_foto)) $errores[] = "El título de la foto es obligatorio.";
    if (empty($nombre_fichero)) $errores[] = "Debes seleccionar un fichero de imagen.";
    
    if (empty($texto_alt)) {
        $errores[] = "El texto alternativo es obligatorio.";
    } else {
        if (strlen($texto_alt) < 10) $errores[] = "El texto alternativo debe tener al menos 10 caracteres.";
        if (preg_match('/^(foto|imagen)/i', $texto_alt)) $errores[] = "El texto alternativo no debe empezar por 'foto' o 'imagen'.";
        if (strcasecmp($texto_alt, "Texto alternativo para la imagen") === 0) $errores[] = "No utilices el texto alternativo generado por defecto.";
    }

    // --- LOGICA DE BASE DE DATOS---
    if (empty($errores)) {
        // verificamos que el anuncio existe y pertenece al usuario
        $sql_info = "SELECT IdAnuncio, FPrincipal, Alternativo FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?";//coge la foto 
        $stmt_info = $mysqli->prepare($sql_info);
        
        if ($stmt_info) {
            $stmt_info->bind_param("ii", $anuncio_id, $_SESSION['usuario_id']);
            $stmt_info->execute();
            $resultado = $stmt_info->get_result();
            $datos_anuncio = $resultado->fetch_assoc();
            $stmt_info->close();

            if ($datos_anuncio) {
                // REGLA 1: Si no tiene foto, es principal
                // REGLA 2: Si tiene foto y el usuario marca el checkbox, reemplaza la foto principal
                // REGLA 3: Si tiene foto y el usuario NO marca el checkbox, va a la tablaa de fotos
                
                $es_principal_final = false;
                
                if (empty($datos_anuncio['FPrincipal'])) {
                    $es_principal_final = true; // 1
                } elseif ($usuario_pide_principal) {
                    $es_principal_final = true; // 2 
                } else {
                    $es_principal_final = false; //3
                }

                if ($es_principal_final) {
                    
                    $mysqli->begin_transaction();
                    try {
                        // Si ya habia una foto, la guardamos en la tabla 'fotos' para no perderla
                        if (!empty($datos_anuncio['FPrincipal'])) {
                            $sql_mover = "INSERT INTO fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)";
                            $stmt_mover = $mysqli->prepare($sql_mover);
                            $tit_antiguo = "Antigua foto principal";
                            $foto_antigua = $datos_anuncio['FPrincipal'];
                            $alt_antiguo = $datos_anuncio['Alternativo'];
                            
                            $stmt_mover->bind_param("sssi", $tit_antiguo, $foto_antigua, $alt_antiguo, $anuncio_id);
                            if (!$stmt_mover->execute()) {
                                throw new Exception("Error al mover la foto antigua a secundarios.");
                            }
                            $stmt_mover->close();
                        }

                        // Guardamos la nueva foto en 'anuncios'
                        $sql_upd = "UPDATE anuncios SET FPrincipal = ?, Alternativo = ? WHERE IdAnuncio = ?";
                        $stmt_upd = $mysqli->prepare($sql_upd);
                        $stmt_upd->bind_param("ssi", $nombre_fichero, $texto_alt, $anuncio_id);
                        
                        if (!$stmt_upd->execute()) {
                            throw new Exception("Error al actualizar la foto principal.");
                        }
                        $stmt_upd->close();

                        $mysqli->commit();
                        $exito = true;
                        $mensaje_resultado = "La imagen se ha establecido como FOTO PRINCIPAL del anuncio.";

                    } catch (Exception $e) {
                        $mysqli->rollback();
                        $errores[] = "Error en la operación: " . $e->getMessage();
                    }

                } else {
                    // Insertar en tabla 'fotos'
                    $sql_ins = "INSERT INTO fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)";
                    $stmt_ins = $mysqli->prepare($sql_ins);
                    if ($stmt_ins) {
                        $stmt_ins->bind_param("sssi", $titulo_foto, $nombre_fichero, $texto_alt, $anuncio_id);
                        if ($stmt_ins->execute()) {
                            $exito = true;
                            $mensaje_resultado = "La imagen se ha guardado en la galería secundaria.";
                        } else {
                            $errores[] = "Error al guardar en BD: " . $stmt_ins->error;
                        }
                        $stmt_ins->close();
                    }
                }

            } else {
                $errores[] = "El anuncio no existe o no tienes permisos.";
            }
        } else {
            $errores[] = "Error de conexión BD: " . $mysqli->error;
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