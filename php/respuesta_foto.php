<?php
session_start();
require_once "conexion_bd.php";

// 1. Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$errores = [];
$exito = false;
$mysqli = conectarBD();

// Inicializar variables para mostrar en el HTML
$titulo_foto = '';
$nombre_fichero = '';
$texto_alt = '';
$anuncio_id = 0;

// Recoger datos del POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anuncio_id = isset($_POST['anuncio']) ? intval($_POST['anuncio']) : 0;
    $titulo_foto = trim($_POST['titulo_foto'] ?? '');
    $texto_alt = trim($_POST['texto_alt'] ?? '');
    
    // Recoger nombre del fichero 
    // (Se asume subida manual según enunciado, pero guardamos el nombre para la BD)
    $nombre_fichero = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';

    // ---------------------------------------------------------
    // VALIDACIONES
    // ---------------------------------------------------------

    // titulo obligatorio
    if (empty($titulo_foto)) {
        $errores[] = "El título de la foto es obligatorio.";
    }

    // img obligatorio
    if (empty($nombre_fichero)) {
        $errores[] = "Debes seleccionar un fichero de imagen.";
    }

    // Validaciones de la descripcion
    if (empty($texto_alt)) {
        $errores[] = "El texto alternativo es obligatorio.";
    } else {
        if (strlen($texto_alt) < 10) {
            $errores[] = "El texto alternativo debe tener al menos 10 caracteres.";
        }
        if (preg_match('/^(foto|imagen)/i', $texto_alt)) {//no empezar por foto o imagen
            $errores[] = "El texto alternativo no debe empezar por 'foto' o 'imagen' (es redundante).";
        }
        if (strcasecmp($texto_alt, "Texto alternativo para la imagen") === 0) {
            $errores[] = "No utilices el texto alternativo generado por defecto.";
        }
    }

    // ---------------------------------------------------------
    // INSERCION EN BD
    // ---------------------------------------------------------
    if (empty($errores)) {
        // Verificar que el anuncio pertenece al usuario 
        // Usamos nombres de columnas de la BD: IdAnuncio, Usuario
        $check_sql = "SELECT IdAnuncio FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?";
        $stmt_check = $mysqli->prepare($check_sql);
        
        if ($stmt_check) {
            $stmt_check->bind_param("ii", $anuncio_id, $_SESSION['usuario_id']);
            $stmt_check->execute();
            
            if ($stmt_check->fetch()) {
                $stmt_check->close();

                // Insertar la foto
                // Tabla: fotos (Titulo, Foto, Alternativo, Anuncio)
                $sql_insert = "INSERT INTO fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)";
                $stmt_insert = $mysqli->prepare($sql_insert);
                
                if ($stmt_insert) {
                    $stmt_insert->bind_param("sssi", $titulo_foto, $nombre_fichero, $texto_alt, $anuncio_id);

                    if ($stmt_insert->execute()) {
                        $exito = true;
                    } else {
                        $errores[] = "Error al insertar en la base de datos: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                } else {
                    $errores[] = "Error al preparar la inserción: " . $mysqli->error;
                }
            } else {
                $errores[] = "El anuncio seleccionado no existe o no te pertenece.";
            }
        } else {
            $errores[] = "Error en la verificación de seguridad: " . $mysqli->error;
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
            <!-- EXITO -->
            <section class ="exito">
                <h3>¡Foto añadida con éxito!</h3>
                <p>Se ha registrado la foto correctamente en el sistema.</p>
                <ul>
                    <a><strong>Título:</strong> <?php echo htmlspecialchars($titulo_foto); ?></a><br>
                    <a><strong>Archivo:</strong> <?php echo htmlspecialchars($nombre_fichero); ?></a><br>
                    <a><strong>Texto Alternativo:</strong> <?php echo htmlspecialchars($texto_alt); ?></a>
                </ul>
                <button class="ver">                
                    <a href="ver_anuncio.php?id=<?php echo $anuncio_id; ?>">Ver el anuncio</a>
                </button>
                <button class="ver">      
                    <a href="mis_anuncios.php">Volver a mis anuncios</a>
                </button>
            </section>

        <?php else: ?>
            <!-- MENSAJE DE ERROR -->
            <section>
                <h3>Ha ocurrido un error</h3>
                <p>No se ha podido guardar la foto debido a los siguientes problemas:</p>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button>
                    <a href="javascript:history.back()">Volver al formulario</a>
                </button>
            </section>
        <?php endif; ?>

    </section>
</main>

<?php require_once "footer.php"; ?>