<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: añadir_foto.php
// -------------------------------------------------------------

// Obtener el ID del anuncio si viene por parámetro GET
$anuncio_id = isset($_GET['anuncio_id']) ? intval($_GET['anuncio_id']) : 0;
$solo_lectura = ($anuncio_id > 0); // Si viene de "Mis anuncios" o "Ver anuncio"

// Simulación de anuncios disponibles (en práctica real vendrían de BD)
$anuncios_disponibles = array(
    1 => "Apartamento céntrico renovado",
    2 => "Piso moderno con terraza",
    3 => "Chalet con jardín y piscina"
);

$titulo_pagina = "Añadir Foto a Anuncio";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <h2>Añadir Foto a Anuncio</h2>
    <p>Completa el formulario para añadir una nueva foto a uno de tus anuncios.</p>
    <p>Los campos marcados con un asterisco (*) son obligatorios</p>

    <form action="respuesta_foto.php" method="post" enctype="multipart/form-data" class="form-foto">
        
        <label for="anuncio" class="required">Anuncio</label>
        <select id="anuncio" name="anuncio" required <?php echo $solo_lectura ? 'disabled' : ''; ?>>
            <?php if (!$solo_lectura): ?>
                <option value="">-- Seleccione un anuncio --</option>
            <?php endif; ?>
            
            <?php foreach ($anuncios_disponibles as $id => $titulo): ?>
                <option value="<?php echo $id; ?>" 
                    <?php echo ($anuncio_id == $id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($titulo); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($solo_lectura): ?>
            <!-- Campo oculto para enviar el valor cuando el select está deshabilitado -->
            <input type="hidden" name="anuncio" value="<?php echo $anuncio_id; ?>">
        <?php endif; ?>

        <label for="foto" class="required">Seleccionar Foto</label>
        <input type="file" id="foto" name="foto" accept="image/*" required>
        <small>Formatos aceptados: JPG, PNG, GIF (máx. 5MB)</small>

        <label for="texto_alt" class="required">Texto Alternativo</label>
        <input type="text" id="texto_alt" name="texto_alt" 
               placeholder="Descripción de la imagen" 
               minlength="10" required>
        <small>Mínimo 10 caracteres. Describe el contenido de la imagen.</small>

        <label for="titulo_foto">Título de la Foto</label>
        <input type="text" id="titulo_foto" name="titulo_foto" 
               placeholder="Título descriptivo (opcional)">

        <button type="submit">Añadir Foto</button>
        <button>
            <a href="mis_anuncios.php">Cancelar</a>
        </button>
    </form>

    <section>
        <h3>Información importante</h3>
        <ul>
            <li>El texto alternativo es obligatorio para la accesibilidad web</li>
            <li>Debe tener al menos 10 caracteres</li>
            <li>Describe el contenido de la imagen para personas con discapacidad visual</li>
            <li>En esta práctica no se realiza la subida real del archivo</li>
        </ul>
    </section>
</main>

<?php
include "footer.php";
?>