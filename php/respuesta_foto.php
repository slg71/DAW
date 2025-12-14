<?php
session_start();
require_once "conexion_bd.php";

// Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$errores = [];
$exito = false;
$mysqli = conectarBD();

// Variables iniciales para el HTML
$titulo_foto = '';
$nombre_fichero = '';
$texto_alt = '';
$anuncio_id = 0;
$mensaje_resultado = "";
$fotos_subidas_info = []; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Recogemos datos del formulario
    $anuncio_id = isset($_POST['anuncio']) ? intval($_POST['anuncio']) : 0;
    $titulo_base = trim($_POST['titulo_foto'] ?? '');
    $texto_alt_base = trim($_POST['texto_alt'] ?? '');
    
    // Variables para el HTML final
    $titulo_foto = $titulo_base;
    $texto_alt = $texto_alt_base;
    
    // ¿El usuario marcó el check de "Portada"?
    $usuario_pide_principal = isset($_POST['es_principal']) && $_POST['es_principal'] == '1';
    
    // 2. Validaciones básicas
    if (empty($titulo_base)) $errores[] = "El título de la foto es obligatorio.";
    if (empty($anuncio_id)) $errores[] = "Debe seleccionar un anuncio.";
    
    // Verificamos el array de fotos
    if (!isset($_FILES['fotos']) || !is_array($_FILES['fotos']['name']) || empty($_FILES['fotos']['name'][0])) {
         $errores[] = "No se han seleccionado fotos.";
    }

    // 3. Procesamiento de imágenes
    if (empty($errores)) {
        
        $contador_exito = 0; // Inicializamos la variable que daba error
        $total_archivos = count($_FILES['fotos']['name']);

        // Bucle para cada foto
        for ($i = 0; $i < $total_archivos; $i++) {
            
            // Extraer info de la foto actual
            $nombre_original = $_FILES['fotos']['name'][$i];
            $tipo_mime       = $_FILES['fotos']['type'][$i];
            $tmp_name        = $_FILES['fotos']['tmp_name'][$i];
            $error_codigo    = $_FILES['fotos']['error'][$i];
            $tamano          = $_FILES['fotos']['size'][$i];

            if ($error_codigo !== UPLOAD_ERR_OK || empty($nombre_original)) {
                continue; 
            }

            // Validaciones tipo y tamaño
            $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
            $tipos_validos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($tipo_mime, $tipos_validos) && !in_array('image/'.$ext, $tipos_validos)) {
                $errores[] = "El archivo '$nombre_original' no es válido.";
                continue;
            }

            // Mover fichero
            // Añadimos $i al nombre para evitar colisiones en la misma subida
            $nombre_nuevo = $anuncio_id . '_' . time() . '_' . uniqid() . '_' . $i . '.' . $ext;
            $ruta_destino = "../img/" . $nombre_nuevo;

            if (move_uploaded_file($tmp_name, $ruta_destino)) {
                
                // --- LÓGICA DE PORTADA ---
                
                // 1. Consultar portada actual
                $fprincipal_actual = null;
                $sql_check = "SELECT FPrincipal FROM anuncios WHERE IdAnuncio = ?";
                $stmt_check = $mysqli->prepare($sql_check);
                $stmt_check->bind_param("i", $anuncio_id);
                $stmt_check->execute();
                $stmt_check->bind_result($fprincipal_actual);
                $stmt_check->fetch();
                $stmt_check->close();

                // 2. Comprobar si existe
                $tiene_principal = !empty($fprincipal_actual) && file_exists("../img/" . $fprincipal_actual);
                
                // 3. Decidir si esta foto será la principal
                $sera_principal = false;
                
                // CASO A: Usuario lo pidió y es la primera foto válida del lote
                if ($usuario_pide_principal && $contador_exito === 0) {
                    $sera_principal = true;
                }
                // CASO B: No había foto principal (asignación automática)
                elseif (!$tiene_principal) {
                    $sera_principal = true;
                }

                $titulo_final = ($total_archivos > 1) ? "$titulo_base (" . ($i + 1) . ")" : $titulo_base;

                if ($sera_principal) {
                    // Actualizar Anuncio (Portada)
                    
                    // Borrar anterior si existía
                    if ($tiene_principal) {
                        unlink("../img/" . $fprincipal_actual);
                    }
                    
                    $sql_upd = "UPDATE anuncios SET FPrincipal = ?, Alternativo = ? WHERE IdAnuncio = ?";
                    $stmt_upd = $mysqli->prepare($sql_upd);
                    $stmt_upd->bind_param("ssi", $nombre_nuevo, $texto_alt, $anuncio_id);
                    
                    if ($stmt_upd->execute()) {
                        $fotos_subidas_info[] = ['archivo' => $nombre_original, 'tipo' => 'Principal'];
                        $contador_exito++;
                        // Al asignar una principal, marcamos que ya tiene para las siguientes vueltas del bucle
                    } else {
                        $errores[] = "Error BD Principal: " . $stmt_upd->error;
                    }
                    $stmt_upd->close();

                } else {
                    // Insertar en Galería
                    $sql_ins = "INSERT INTO fotos (Anuncio, Foto, Titulo, Alternativo) VALUES (?, ?, ?, ?)";
                    $stmt_ins = $mysqli->prepare($sql_ins);
                    $stmt_ins->bind_param("isss", $anuncio_id, $nombre_fichero, $titulo_final, $texto_alt);
                    $sql_ins_corrected = "INSERT INTO fotos (Anuncio, Foto, Titulo, Alternativo) VALUES (?, ?, ?, ?)";
                    $stmt_ins = $mysqli->prepare($sql_ins_corrected);
                    $stmt_ins->bind_param("isss", $anuncio_id, $nombre_nuevo, $titulo_final, $texto_alt);

                    if ($stmt_ins->execute()) {
                        $fotos_subidas_info[] = ['archivo' => $nombre_original, 'tipo' => 'Galería'];
                        $contador_exito++;
                    } else {
                        $errores[] = "Error BD Galería: " . $stmt_ins->error;
                    }
                    $stmt_ins->close();
                }

            } else {
                $errores[] = "Error al mover el archivo $nombre_original";
            }
        } // Fin del for

        if ($contador_exito > 0) {
            $exito = true;
            $mensaje_resultado = "Se han subido $contador_exito fotos correctamente.";
            $nombre_fichero = "($contador_exito archivos procesados)";
        } else {
            if (empty($errores)) {
                $errores[] = "No se pudo subir ninguna foto correctamente.";
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
                    <li><strong>Título Base:</strong> <?php echo htmlspecialchars($titulo_foto); ?></li>
                    <li><strong>Archivos:</strong> <?php echo htmlspecialchars($nombre_fichero); ?></li>
                    <li><strong>Alternativo:</strong> <?php echo htmlspecialchars($texto_alt); ?></li>
                </ul>
                
                <h4>Detalle:</h4>
                <ul>
                    <?php foreach ($fotos_subidas_info as $info): ?>
                        <li>[<?php echo $info['tipo']; ?>] <?php echo htmlspecialchars($info['archivo']); ?></li>
                    <?php endforeach; ?>
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