<?php
// -------------------------------------------------------------
// Página: subir_foto.php
// -------------------------------------------------------------
$title = "Subir foto del anuncio";

// Obtener el id del anuncio desde el formulario
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

// Comprobar si se ha enviado un archivo
$tiene_foto = isset($_FILES["foto"]) && $_FILES["foto"]["name"] != "";

// require_once("cabecera.inc");
// require_once("inicio.inc");
?>

<main id="subida-foto">
    <h2>Resultado de la subida</h2>

    <?php
    if ($id == 0) {
        echo "<p class='error'>No se ha especificado ningún anuncio válido.</p>";
    } elseif (!$tiene_foto) {
        echo "<p class='error'>No se ha seleccionado ninguna imagen para subir.</p>";
    } else {
        // Simulación: mostrar el nombre del archivo subido
        $nombre_fichero = htmlspecialchars($_FILES["foto"]["name"]);
        echo "<p>La foto <strong>$nombre_fichero</strong> se ha añadido correctamente al anuncio con ID <strong>$id</strong>.</p>";
        echo "<p>(Simulación: la imagen no se guarda realmente, solo se muestra el nombre del archivo.)</p>";
    }

    echo "<p><a href='ver_anuncio.php?id=$id'>Volver al anuncio</a></p>";
    ?>
</main>

<?php
// require_once("pie.inc");
?>
