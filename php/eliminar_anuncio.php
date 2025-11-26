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
    // Asumimos que $_SESSION['usuario_id'] contiene el ID del usuario logueado
    $stmt_check->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
    $stmt_check->execute();
    $stmt_check->bind_result($id_encontrado, $titulo_anuncio);
    $existe = $stmt_check->fetch();
    $stmt_check->close();
} else {
    die("Error en la preparación de la consulta: " . $mysqli->error);
}

if (!$existe) {
    // Si no existe o no es tuyo, redirigir a mis anuncios
    header("Location: mis_anuncios.php?error=no_encontrado");
    exit;
}

// -------------------------------------------------------------------------
// PROCESO DE BORRADO (Solo si se recibe confirmacion POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmado']) && $_POST['confirmado'] == 'si') {
    
    // Borrar las fotos asociadas (Tabla 'fotos', columna FK 'Anuncio')
    $stmt_fotos = $mysqli->prepare("DELETE FROM fotos WHERE Anuncio = ?");
    $stmt_fotos->bind_param("i", $id_anuncio);
    $stmt_fotos->execute();
    $stmt_fotos->close();

    // Borrar mensajes asociados (Tabla 'mensajes', columna FK 'Anuncio')
    $stmt_msj = $mysqli->prepare("DELETE FROM mensajes WHERE Anuncio = ?");
    $stmt_msj->bind_param("i", $id_anuncio);
    $stmt_msj->execute();
    $stmt_msj->close();

    // Borrar solicitudes asociadas (Tabla 'solicitudes', columna FK 'Anuncio')
    $stmt_sol = $mysqli->prepare("DELETE FROM solicitudes WHERE Anuncio = ?");
    $stmt_sol->bind_param("i", $id_anuncio);
    $stmt_sol->execute();
    $stmt_sol->close();

    // Borrar el anuncio (Tabla 'anuncios', PK 'IdAnuncio')
    $stmt_borrar = $mysqli->prepare("DELETE FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
    $stmt_borrar->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
    
    if ($stmt_borrar->execute()) {
        $mensaje = "El anuncio se ha eliminado correctamente.";
    } else {
        $error = "Error al intentar eliminar el anuncio: " . $mysqli->error;
    }
    $stmt_borrar->close();
}

// -------------------------------------------------------------------------
//VISTA HTML
// -------------------------------------------------------------------------
$titulo_pagina = "Eliminar Anuncio";
if (file_exists('paginas_Estilo.php')) include 'paginas_Estilo.php';
if (file_exists('header.php')) include 'header.php';
?>

<main>
    <h2>Eliminar Anuncio</h2>

    <?php if ($mensaje): ?>
        <!-- PANTALLA DE ÉXITO -->
        
        <h3>Borrado realizado</h3>
        <p><?php echo $mensaje; ?></p>
        <p>El anuncio ya no aparecerá en el listado.</p>
        <br>
        <button>
            <a href="mis_anuncios.php" class="boton">Volver a mis anuncios</a>
        </button>

    <?php elseif ($error): ?>
        <!-- PANTALLA DE ERROR -->
        <h3>Error</h3>
        <p><?php echo $error; ?></p>
        <button>
            <a href="mis_anuncios.php" class="boton">Volver a mis anuncios</a>
        </button>

    <?php else: ?>
        <!-- PANTALLA DE CONFIRMACIÓN -->
        
        <form action="eliminar_anuncio.php" method="post">
            <h2>Confirmar eliminación</h2>

            <p>Estás a punto de eliminar el anuncio:' <strong><?php echo htmlspecialchars($titulo_anuncio); ?></strong>'. El anuncio se eliminará permanentemente ¿Estás seguro que deseas eliminarlo? </p>
            
            <input type="hidden" name="id" value="<?php echo $id_anuncio; ?>">
            <input type="hidden" name="confirmado" value="si">

                <!-- Botones -->
            <button type="submit" class="btn-aceptar">
                <a href="mis_anuncios.php">
                    Aceptar
                </a>    
            </button>

            <button>
                <a href="mis_anuncios.php">
                    Cancelar
                </a>
            </button>
        </form>
    <?php endif; ?>

</main>

<?php 
if (file_exists('footer.php')) include 'footer.php'; 
?>