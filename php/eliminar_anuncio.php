<?php
session_start();
require_once 'conexion_bd.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mysqli = conectarBD();

$id_anuncio = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$mensaje = "";
$error = "";

// -------------------------------------------------------------------------
// Comprobar que el anuncio existe y PERTENECE al usuario
// -------------------------------------------------------------------------

$sql_check = "SELECT IdAnuncio, Titulo FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?";
$stmt_check = $mysqli->prepare($sql_check);

if ($stmt_check) {
    $stmt_check->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
    $stmt_check->execute();
    $stmt_check->bind_result($id_encontrado, $titulo_anuncio);
    $existe = $stmt_check->fetch();
    $stmt_check->close();
} else {
    die("Error en la preparación de la consulta: " . $mysqli->error);
}

if (!$existe) {
    header("Location: mis_anuncios.php?error=no_encontrado");// Si no existe redirigir a mis anuncios
    exit;
}

// -------------------------------------------------------------------------
// PROCESO DE BORRADO
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmado']) && $_POST['confirmado'] == 'si') {
    
    // 1. OBTENER NOMBRES DE FICHERO ASOCIADOS AL ANUNCIO (ANTES DE BORRAR LA BD)
    $ficheros_a_borrar = [];
    $directorio_fotos = "../img/"; 

    // a) Obtener la Foto Principal
    $sql_principal = "SELECT FPrincipal FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?";
    $stmt_p = $mysqli->prepare($sql_principal);
    if ($stmt_p) {
        $stmt_p->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
        $stmt_p->execute();
        $stmt_p->bind_result($fprincipal);
        if ($stmt_p->fetch() && !empty($fprincipal)) {
            $ficheros_a_borrar[] = $fprincipal;
        }
        $stmt_p->close();
    }
    
    // b) Obtener las Fotos Secundarias
    $sql_secundarias = "SELECT Foto FROM fotos WHERE Anuncio = ?";
    $stmt_s = $mysqli->prepare($sql_secundarias);
    if ($stmt_s) {
        $stmt_s->bind_param("i", $id_anuncio);
        $stmt_s->execute();
        $res_s = $stmt_s->get_result();
        while ($fila = $res_s->fetch_assoc()) {
            if (!empty($fila['Foto'])) {
                $ficheros_a_borrar[] = $fila['Foto'];
            }
        }
        $stmt_s->close();
    }
    
    // 2. BORRAR REGISTROS DE BASE DE DATOS
    
    // Borrar las fotos asociadas (DB)
    $stmt_fotos = $mysqli->prepare("DELETE FROM fotos WHERE Anuncio = ?");
    $stmt_fotos->bind_param("i", $id_anuncio);
    $stmt_fotos->execute();
    $stmt_fotos->close();

    // Borrar mensajes asociados
    $stmt_msj = $mysqli->prepare("DELETE FROM mensajes WHERE Anuncio = ?");
    $stmt_msj->bind_param("i", $id_anuncio);
    $stmt_msj->execute();
    $stmt_msj->close();

    // Borrar solicitudes asociadas
    $stmt_sol = $mysqli->prepare("DELETE FROM solicitudes WHERE Anuncio = ?");
    $stmt_sol->bind_param("i", $id_anuncio);
    $stmt_sol->execute();
    $stmt_sol->close();

    // Borrar el anuncio
    $stmt_borrar = $mysqli->prepare("DELETE FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
    $stmt_borrar->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
    
    if ($stmt_borrar->execute()) {
        $mensaje = "El anuncio se ha eliminado correctamente.";

        // 3. BORRAR FICHEROS FISICOS solo si el borrado de DB fue exitoso
        foreach ($ficheros_a_borrar as $nombre_fichero) {
            $ruta = $directorio_fotos . $nombre_fichero;
            if (file_exists($ruta)) {
                unlink($ruta); // Eliminar la img fisica
            }
        }

    } else {
        $error = "Error al intentar eliminar el anuncio: " . $mysqli->error;
    }
    $stmt_borrar->close();
}

// -------------------------------------------------------------------------
//ELIMINAR ANUNCIO HTML
// -------------------------------------------------------------------------
$titulo_pagina = "Eliminar Anuncio";
require_once 'paginas_Estilo.php';
require_once 'header.php';
?>

<main>
    <h2>Eliminar Anuncio</h2>

    <?php if ($mensaje): ?>
        <h3>Borrado realizado</h3>
        <p><?php echo $mensaje; ?></p>
        <p>El anuncio ya no aparecerá en el listado.</p>
        <br>
        <button>
            <a href="mis_anuncios.php" class="boton">Volver a mis anuncios</a>
        </button>

    <?php elseif ($error): ?>
        <h3>Error</h3>
        <p><?php echo $error; ?></p>
        <button>
            <a href="mis_anuncios.php" class="boton">Volver a mis anuncios</a>
        </button>

    <?php else: ?>
        <form action="eliminar_anuncio.php" method="post">
            <h2>Confirmar eliminación</h2>

            <p>Estás a punto de eliminar el anuncio: <strong><?php echo htmlspecialchars($titulo_anuncio); ?></strong>. El anuncio se eliminará permanentemente. ¿Estás seguro que deseas eliminarlo?</p>
            
            <input type="hidden" name="id" value="<?php echo $id_anuncio; ?>">
            <input type="hidden" name="confirmado" value="si">

            <button type="submit" class="btn-aceptar">
                Aceptar
            </button>

            <a href="mis_anuncios.php" class="boton">
                Cancelar
            </a>
        </form>
    <?php endif; ?>

</main>

<?php 
require_once 'footer.php'; 
?>