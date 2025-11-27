<?php
session_start();
require_once "conexion_bd.php";

// 1. Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mysqli = conectarBD();

// Recoger parámetros (funciona tanto para GET como para POST)
// 'id_foto' = 0 indica que es la foto principal en la tabla anuncios
// 'id_foto' > 0 indica que es una foto de la tabla fotos
$id_anuncio = isset($_REQUEST['id_anuncio']) ? intval($_REQUEST['id_anuncio']) : 0;
$id_foto = isset($_REQUEST['id_foto']) ? intval($_REQUEST['id_foto']) : -1;
$confirmado = isset($_POST['confirmar']) ? true : false;

$mensaje_resultado = "";
$tipo_resultado = ""; // 'exito' o 'error'
$mostrar_formulario = true;
$nombre_fichero_visual = ""; // Para mostrar en la confirmación

// Verificar que el anuncio pertenece al usuario
$sql_check = "SELECT Titulo, FPrincipal FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
$stmt_check->execute();
$res_check = $stmt_check->get_result();
$datos_anuncio = $res_check->fetch_assoc();
$stmt_check->close();

if (!$datos_anuncio) {
    $mensaje_resultado = "Error: El anuncio no existe o no tienes permisos sobre él.";
    $tipo_resultado = "error";
    $mostrar_formulario = false;
}

// Lógica para obtener el nombre de la foto a borrar para previsualizarla
if ($mostrar_formulario && !$confirmado) {
    if ($id_foto == 0) {
        // Es la principal
        $nombre_fichero_visual = $datos_anuncio['FPrincipal'];
    } else {
        // Es una secundaria, buscar en tabla fotos
        $sql_f = "SELECT Foto FROM fotos WHERE IdFoto = ? AND Anuncio = ?";
        $stmt_f = $mysqli->prepare($sql_f);
        $stmt_f->bind_param("ii", $id_foto, $id_anuncio);
        $stmt_f->execute();
        $stmt_f->bind_result($nombre_fichero_visual);
        if (!$stmt_f->fetch()) {
            $mensaje_resultado = "Error: La foto seleccionada no existe.";
            $tipo_resultado = "error";
            $mostrar_formulario = false;
        }
        $stmt_f->close();
    }
}

// -------------------------------------------------------------
// PROCESO DE BORRADO (POST)
// -------------------------------------------------------------
if ($mostrar_formulario && $confirmado && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $mysqli->begin_transaction();
    try {
        if ($id_foto == 0) {
            // ==========================================
            // BORRAR FOTO PRINCIPAL
            // ==========================================
            
            // 1. Borrar el fichero físico actual (opcional, pero recomendado)
            if (!empty($datos_anuncio['FPrincipal'])) {
                $ruta = "img/" . $datos_anuncio['FPrincipal'];
                if (file_exists($ruta)) { unlink($ruta); }
            }

            // 2. Buscar si hay reemplazo en la tabla 'fotos'
            $sql_reemplazo = "SELECT IdFoto, Foto, Alternativo FROM fotos WHERE Anuncio = ? ORDER BY IdFoto ASC LIMIT 1";
            $stmt_reemplazo = $mysqli->prepare($sql_reemplazo);
            $stmt_reemplazo->bind_param("i", $id_anuncio);
            $stmt_reemplazo->execute();
            $res_reemplazo = $stmt_reemplazo->get_result();
            $foto_reemplazo = $res_reemplazo->fetch_assoc();
            $stmt_reemplazo->close();

            if ($foto_reemplazo) {
                // CASO A: Hay reemplazo.
                // Movemos la secundaria a principal
                $sql_upd = "UPDATE anuncios SET FPrincipal = ?, Alternativo = ? WHERE IdAnuncio = ?";
                $stmt_upd = $mysqli->prepare($sql_upd);
                $stmt_upd->bind_param("ssi", $foto_reemplazo['Foto'], $foto_reemplazo['Alternativo'], $id_anuncio);
                $stmt_upd->execute();
                $stmt_upd->close();

                // Borramos la secundaria que acabamos de promover
                $sql_del_r = "DELETE FROM fotos WHERE IdFoto = ?";
                $stmt_del_r = $mysqli->prepare($sql_del_r);
                $stmt_del_r->bind_param("i", $foto_reemplazo['IdFoto']);
                $stmt_del_r->execute();
                $stmt_del_r->close();

                $mensaje_resultado = "La foto principal ha sido eliminada y reemplazada por la siguiente foto de la galería.";
            
            } else {
                // CASO B: No hay reemplazo.
                // Dejamos el anuncio sin foto (NULL)
                $sql_upd = "UPDATE anuncios SET FPrincipal = NULL, Alternativo = NULL WHERE IdAnuncio = ?";
                $stmt_upd = $mysqli->prepare($sql_upd);
                // null necesita bind especial o variables
                $nulo = null;
                $stmt_upd->bind_param("ssi", $nulo, $nulo, $id_anuncio);
                $stmt_upd->execute();
                $stmt_upd->close();

                $mensaje_resultado = "Este anuncio no tiene fotos. Añade una foto a este anuncio en la pagina 'Añadir Foto'.";
            }

        } else {
            // ==========================================
            // BORRAR FOTO SECUNDARIA
            // ==========================================
            
            // Obtener nombre para borrar fichero fisico
            $sql_get = "SELECT Foto FROM fotos WHERE IdFoto = ?";
            $stmt_get = $mysqli->prepare($sql_get);
            $stmt_get->bind_param("i", $id_foto);
            $stmt_get->execute();
            $stmt_get->bind_result($fichero_borrar);
            $stmt_get->fetch();
            $stmt_get->close();

            if ($fichero_borrar) {
                $ruta = "img/" . $fichero_borrar;
                if (file_exists($ruta)) { unlink($ruta); }
            }

            // Borrar registro
            $sql_del = "DELETE FROM fotos WHERE IdFoto = ? AND Anuncio = ?";
            $stmt_del = $mysqli->prepare($sql_del);
            $stmt_del->bind_param("ii", $id_foto, $id_anuncio);
            $stmt_del->execute();
            $stmt_del->close();

            $mensaje_resultado = "La foto ha sido eliminada correctamente.";
        }

        $mysqli->commit();
        $tipo_resultado = "exito";
        $mostrar_formulario = false; // Ya no mostramos el form, solo el mensaje

    } catch (Exception $e) {
        $mysqli->rollback();
        $mensaje_resultado = "Ocurrió un error al intentar eliminar la foto: " . $e->getMessage();
        $tipo_resultado = "error";
        $mostrar_formulario = false;
    }
}

$mysqli->close();

$titulo_pagina = "Eliminar Foto";
require_once "paginas_Estilo.php";
require_once "header.php";
?>

<main>
    <h2>Eliminar Foto</h2>

    <?php if ($mostrar_formulario): ?>
        <!-- PÁGINA DE CONFIRMACIÓN -->
        <section class="advertencia">
            
            
            <?php if ($id_foto == 0): ?>
                <p><em>Nota: Esta es la <strong>Foto Principal</strong>. Si la eliminas, se intentará reemplazar automáticamente con otra foto de tu galería.</em></p>
            <?php endif; ?>
            
            <form action="eliminar_foto.php" method="post">
                <p><strong>¿Estás seguro de que quieres eliminar esta foto?</strong></p>
                <p>Esta operación es irreversible.</p>
                <input type="hidden" name="id_anuncio" value="<?php echo $id_anuncio; ?>">
                <input type="hidden" name="id_foto" value="<?php echo $id_foto; ?>">
                <input type="hidden" name="confirmar" value="1">
                
                <button type="submit">Sí, eliminar foto</button>
                <button type="button">
                    <a href="ver_fotos_privado.php?id=<?php echo $id_anuncio; ?>">Cancelar</a>
                </button>
            </form>
        </section>

    <?php else: ?>
        <!-- RESULTADO DE LA OPERACIÓN -->
        <section class="<?php echo $tipo_resultado; ?>">
            <h3>Resultado</h3>
            <p><?php echo htmlspecialchars($mensaje_resultado); ?></p>
            
            <p>
                <button>
                    <a href="ver_fotos_privado.php?id=<?php echo $id_anuncio; ?>">Volver a la galería</a>
                </button>
                
                <!-- Si el mensaje es el específico de que no hay fotos, mostramos botón de añadir -->
                <?php if (strpos($mensaje_resultado, 'Añade una foto') !== false): ?>
                    <button>
                        <a href="añadir_foto.php?anuncio_id=<?php echo $id_anuncio; ?>">Añadir Foto</a>
                    </button>
                <?php endif; ?>
            </p>
        </section>
    <?php endif; ?>

</main>

<?php require_once "footer.php"; ?>