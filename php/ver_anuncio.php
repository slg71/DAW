<?php
// SE LLEGA DESDE MIS_ANUNCIOS.PHP
// -------------------------------------------------------------
// Página: ver_anuncio.php
// -------------------------------------------------------------


// Simulamos que el usuario ha hecho clic en su propio anuncio
// Se obtiene el ID mediante GET
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Simulación de anuncios publicados por el usuario
$anuncios = array(
    1 => array(
        "titulo" => "Apartamento céntrico renovado",
        "fecha" => "2025-09-15",
        "ciudad" => "Alicante",
        "pais" => "España",
        "precio" => 900,
        "descripcion" => "Bonito apartamento en el centro de Alicante, recién reformado.",
        "imagen" => "./img/piso2.jpg"
    ),
    2 => array(
        "titulo" => "Piso moderno con terraza",
        "fecha" => "2025-09-18",
        "ciudad" => "Valencia",
        "pais" => "España",
        "precio" => 220000,
        "descripcion" => "Luminoso piso moderno con terraza amplia y buenas vistas.",
        "imagen" => "./img/piso1.jpg"
    )
);

// Comprobar si el anuncio existe
if (isset($anuncios[$id])) {
    $a = $anuncios[$id];
} else {
    $a = null;
}

$titulo_pagina = "Ver mi Anuncio";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <?php if ($a != null): ?>
        <section>
            <h2><?php echo htmlspecialchars($a["titulo"]); ?></h2>
            <img src="<?php echo htmlspecialchars($a["imagen"]); ?>" alt="Foto principal del anuncio">
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($a["fecha"]); ?></p>
            <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($a["ciudad"]); ?>, <?php echo htmlspecialchars($a["pais"]); ?></p>
            <p><strong>Precio:</strong> <?php echo number_format($a["precio"], 0, ',', '.'); ?> €</p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($a["descripcion"]); ?></p>
        </section>

        <section id="fotos">
            <h3>Añadir nueva foto al anuncio</h3>
            <form action="subir_foto.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <label for="foto">Seleccionar archivo:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>
                <button type="submit">Subir foto</button>
            </form>
        </section>
    <?php else: ?>
        <p>No se encontró el anuncio solicitado.</p>
    <?php endif; ?>
</main>

<?php
include "footer.php";
?>
