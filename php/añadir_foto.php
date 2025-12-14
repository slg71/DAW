<?php
session_start();

// control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once "conexion_bd.php";

// -------------------------------------------------------------
// Pagina: añadir_foto.php 
// -------------------------------------------------------------

$mysqli = conectarBD();
$mensaje_exito = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';

// Obtener el id del anuncio si viene por URL
$anuncio_id_preseleccionado = isset($_GET['anuncio_id']) ? intval($_GET['anuncio_id']) : 0;
$solo_lectura = ($anuncio_id_preseleccionado > 0);
$tiene_foto_principal = false; 

// --- CARGAR ANUNCIOS DEL USUARIO ---
$anuncios_usuario = [];
$sql = "SELECT IdAnuncio, Titulo, FPrincipal FROM anuncios WHERE Usuario = ? ORDER BY IdAnuncio DESC";
$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $anuncios_usuario[] = $fila;
        
        // Si hay un anuncio preseleccionado, comprobamos si tiene foto
        if ($solo_lectura && $fila['IdAnuncio'] == $anuncio_id_preseleccionado) {
            if (!empty($fila['FPrincipal'])) {
                $tiene_foto_principal = true;
            }
        }
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
        <section class="exito"><p><?php echo $mensaje_exito; ?></p></section>
    <?php endif; ?>

    <p>Completa el formulario para añadir una nueva foto.</p>
    <form action="respuesta_foto.php" method="post" enctype="multipart/form-data">
        
        <fieldset>
            <legend>Selección del Anuncio</legend>
            <label for="anuncio">Anuncio (*):</label>
            <select id="anuncio" name="anuncio" required <?php echo $solo_lectura ? 'disabled' : ''; ?>>
                <option value="" disabled <?php echo !$solo_lectura ? 'selected' : ''; ?>>-- Seleccione un anuncio --</option>
                    
                <?php foreach ($anuncios_usuario as $anuncio): ?>
                    <option value="<?php echo $anuncio['IdAnuncio']; ?>" 
                        <?php echo ($anuncio_id_preseleccionado == $anuncio['IdAnuncio']) ? 'selected' : ''; ?>> <!--recorre los anuncios para encontrar el preseleccionado -->
                        <?php echo htmlspecialchars($anuncio['Titulo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php if ($solo_lectura): ?><!--no deja cambiar el desplegable -->
                <input type="hidden" name="anuncio" value="<?php echo $anuncio_id_preseleccionado; ?>">
            <?php endif; ?>
        </fieldset>
        
        <fieldset>
            <legend>Datos de la imagen</legend>
            
            <label for="fotos">Seleccionar Fotos (Puedes elegir varias):</label>
            <input type="file" id="fotos" name="fotos[]" accept="image/*" multiple required>
            
            <label for="titulo_foto">Título de la Foto (*):</label>
            <input type="text" id="titulo_foto" name="titulo_foto" required>

            <label for="texto_alt">Texto Alternativo (*):</label>
            <input type="text" id="texto_alt" name="texto_alt" required minlength="10">
            <br>
            <small>Mínimo 10 caracteres. No empiece por "foto" o "imagen".</small>
            
        </fieldset>

        <fieldset>
            <legend>Opciones de Portada</legend>
            
            <?php if ($solo_lectura): ?>
                <?php if ($tiene_foto_principal): ?>
                    
                    <label>
                        <input type="checkbox" name="es_principal" value="1">
                        Establecer como nueva foto principal (reemplazará a la actual).
                    </label>
                    
                    <small>Este anuncio ya tiene una foto de portada. Si no marcas esta casilla, la foto se añadirá a la galería secundaria.</small>
                <?php else: ?>
                    <p><strong>Aviso:</strong> Este anuncio no tiene foto principal.</p> 
                    <p>Esta imagen se asignará automáticamente como portada.</p>
                    <input type="hidden" name="es_principal" value="1">
                <?php endif; ?>

            <?php else: ?>
                <label>
                    <input type="checkbox" name="es_principal" value="1">
                    Establecer como foto principal.
                </label>
                <small>
                    <strong>Nota:</strong> Si el anuncio seleccionado NO tiene foto principal todavía, 
                    esta imagen se asignará automáticamente como portada aunque no marques la casilla.
                    Si YA tiene portada, márcala solo si quieres sustituirla.
                </small>
            <?php endif; ?>
        </fieldset>

        <button type="submit">Añadir Foto</button>
        <button type="button"><a href="mis_anuncios.php">Cancelar</a></button>
    </form>
</main>

<?php require_once "footer.php"; ?>