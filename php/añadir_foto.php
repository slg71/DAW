<?php
session_start();

// 1. Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once "conexion_bd.php";

// -------------------------------------------------------------
// Página: añadir_foto.php
// -------------------------------------------------------------

$mysqli = conectarBD();
$errores = [];
$mensaje_exito = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';

// Obtener el ID del anuncio si viene por URL (ej: después de crear anuncio)
$anuncio_id_preseleccionado = isset($_GET['anuncio_id']) ? intval($_GET['anuncio_id']) : 0;
$solo_lectura = ($anuncio_id_preseleccionado > 0);

// --- CARGAR ANUNCIOS DEL USUARIO DE LA BD ---
$anuncios_usuario = [];
$sql = "SELECT IdAnuncio, Titulo FROM anuncios WHERE Usuario = ? ORDER BY IdAnuncio DESC";
$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $anuncios_usuario[] = $fila;
    }
    $stmt->close();
}
$mysqli->close();

$titulo_pagina = "Añadir Foto a Anuncio";
require_once "paginas_Estilo.php";
require_once "header.php";
?>

<main>
    <h2>Añadir Foto a Anuncio</h2>
    
    <?php if ($mensaje_exito): ?>
        
        <section class="exito"><p>
            <?php echo $mensaje_exito; ?>
        </p></section>
    <?php endif; ?>

    <p>Completa el formulario para añadir una nueva foto.</p>

    <!-- El formulario envía los datos a respuesta_foto.php -->
    <form action="respuesta_foto.php" method="post" enctype="multipart/form-data">
        
        <label for="anuncio">Anuncio (*):</label>
        <select id="anuncio" name="anuncio" required <?php echo $solo_lectura ? 'disabled' : ''; ?>>
            <option value="" disabled <?php echo !$solo_lectura ? 'selected' : ''; ?>>-- Seleccione un anuncio --</option>
                
            <?php foreach ($anuncios_usuario as $anuncio): ?>
                <option value="<?php echo $anuncio['IdAnuncio']; ?>" 
                    <?php echo ($anuncio_id_preseleccionado == $anuncio['IdAnuncio']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($anuncio['Titulo']); ?>
                </option>
            <?php endforeach; ?>
        </select>
            
        <?php if ($solo_lectura): ?>
                <!-- Si el select está disabled, necesitamos enviar el valor en un hidden -->
            <input type="hidden" name="anuncio" value="<?php echo $anuncio_id_preseleccionado; ?>">
        <?php endif; ?>
        

        
        <label for="foto">Seleccionar Foto (*):</label>
        <input type="file" id="foto" name="foto" accept="image/*" required>
        
        <label for="titulo_foto">Título de la Foto (*):</label>
        <input type="text" id="titulo_foto" name="titulo_foto" required>
        

        
        <label for="texto_alt">Texto Alternativo (*):</label>
        <input type="text" id="texto_alt" name="texto_alt" required minlength="10">
        <br>
        <small>Mínimo 10 caracteres. No empiece por "foto" o "imagen".</small>
        
        <button type="submit">Añadir Foto</button>
        <button type="button"><a href="mis_anuncios.php">Cancelar</a></bfutton>
        

    </form>
</main>

<?php include "footer.php"; ?>