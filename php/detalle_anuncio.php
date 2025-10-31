<?php
// ---------------------------------------------
// Página: detalle.php
// ---------------------------------------------

// Título de la página
$titulo_pagina = "Detalle del anuncio";
include = "paginas_Estilo.php";
include "header.php";

// Obtener ID del anuncio desde la URL
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Incluir fichero con los datos de los anuncios
require_once("anuncios.inc.php");

// Elegir anuncio según si el ID es par o impar
if ($id % 2 == 0) {
    $anuncio = $anuncio_par;
} else {
    $anuncio = $anuncio_impar;
}


include = "paginas_Estilo.php";
include "header.php";
?>

<main id="anuncio">
    <section>
        <h2><?php echo htmlspecialchars($anuncio["titulo"]); ?></h2>
        <article>
            <img src="<?php echo htmlspecialchars($anuncio["fotos"][0]); ?>" alt="Foto principal">

            <h3><?php echo number_format($anuncio["precio"], 0, ',', '.'); ?> €</h3>
            <p><strong>Tipo de anuncio:</strong> <?php echo htmlspecialchars($anuncio["tipo_anuncio"]); ?></p>
            <p><strong>Tipo de vivienda:</strong> <?php echo htmlspecialchars($anuncio["tipo_vivienda"]); ?></p>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($anuncio["fecha"]); ?></p>
            <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($anuncio["ciudad"]) . ", " . htmlspecialchars($anuncio["pais"]); ?></p>

            <p><?php echo htmlspecialchars($anuncio["texto"]); ?></p>

            <h4>Características</h4>
            <ul>
                <li><strong>Superficie:</strong> <?php echo htmlspecialchars($anuncio["caracteristicas"]["superficie"]); ?></li>
                <li><strong>Habitaciones:</strong> <?php echo htmlspecialchars($anuncio["caracteristicas"]["habitaciones"]); ?></li>
                <li><strong>Baños:</strong> <?php echo htmlspecialchars($anuncio["caracteristicas"]["baños"]); ?></li>
                <li><strong>Planta:</strong> <?php echo htmlspecialchars($anuncio["caracteristicas"]["planta"]); ?></li>
                <li><strong>Año de construcción:</strong> <?php echo htmlspecialchars($anuncio["caracteristicas"]["anio"]); ?></li>
            </ul>

            <div class="miniaturas">
                <?php
                foreach ($anuncio["fotos"] as $foto) {
                    echo "<img src='" . htmlspecialchars($foto) . "' alt='Miniatura'>";
                }
                ?>
            </div>

            <p><strong>Anunciante:</strong> <?php echo htmlspecialchars($anuncio["usuario"]); ?></p>

            <form action="../mensaje.html" method="get">
                <button type="submit">Enviar mensaje</button>
            </form>
        </article>
    </section>
</main>

<?php

include "footer.php";

?>
