<?php
// -------------------------------------------------------------
// Página: ver_anuncio.php
// -------------------------------------------------------------

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
        "imagen" => "./img/piso2.jpg",
        "tipo_anuncio" => "Alquiler",
        "tipo_vivienda" => "Apartamento",
        "superficie" => 75,
        "habitaciones" => 2,
        "banos" => 1,
        "planta" => 3,
        "ano_construccion" => 2020
    ),
    2 => array(
        "titulo" => "Piso moderno con terraza",
        "fecha" => "2025-09-18",
        "ciudad" => "Valencia",
        "pais" => "España",
        "precio" => 220000,
        "descripcion" => "Luminoso piso moderno con terraza amplia y buenas vistas.",
        "imagen" => "./img/piso1.jpg",
        "tipo_anuncio" => "Venta",
        "tipo_vivienda" => "Piso",
        "superficie" => 95,
        "habitaciones" => 3,
        "banos" => 2,
        "planta" => 5,
        "ano_construccion" => 2018
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

<main id="bloque">
    <?php if ($a != null): ?>
        <section class="detalle-anuncio">
            <h2><?php echo htmlspecialchars($a["titulo"]); ?></h2>
            
            <img src="<?php echo htmlspecialchars($a["imagen"]); ?>" 
                 alt="Foto principal del anuncio: <?php echo htmlspecialchars($a["titulo"]); ?>">
            
            <div class="info-basica">
                <p><strong>Tipo de anuncio:</strong> <?php echo htmlspecialchars($a["tipo_anuncio"]); ?></p>
                <p><strong>Tipo de vivienda:</strong> <?php echo htmlspecialchars($a["tipo_vivienda"]); ?></p>
                <p><strong>Fecha de publicación:</strong> <?php echo htmlspecialchars($a["fecha"]); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($a["ciudad"]); ?>, <?php echo htmlspecialchars($a["pais"]); ?></p>
                <p><strong>Precio:</strong> <?php echo number_format($a["precio"], 0, ',', '.'); ?> €</p>
            </div>

            <div class="caracteristicas">
                <h3>Características</h3>
                <ul>
                    <li><strong>Superficie:</strong> <?php echo $a["superficie"]; ?> m²</li>
                    <li><strong>Habitaciones:</strong> <?php echo $a["habitaciones"]; ?></li>
                    <li><strong>Baños:</strong> <?php echo $a["banos"]; ?></li>
                    <li><strong>Planta:</strong> <?php echo $a["planta"]; ?></li>
                    <li><strong>Año de construcción:</strong> <?php echo $a["ano_construccion"]; ?></li>
                </ul>
            </div>

            <div class="descripcion">
                <h3>Descripción</h3>
                <p><?php echo htmlspecialchars($a["descripcion"]); ?></p>
            </div>
        </section>

        <section class="acciones-anuncio">
            <h3>Gestionar Fotos</h3>
            <p>Puedes añadir más fotos a este anuncio para hacerlo más atractivo.</p>
            
            <a href="añadir_foto.php?anuncio_id=<?php echo $id; ?>" class="boton-primario">
                Añadir Foto al Anuncio
            </a>
            
            <a href="mis_anuncios.php" class="boton-secundario">
                Volver a Mis Anuncios
            </a>
        </section>
        
        <section class="galeria-miniaturas">
            <h3>Fotos del anuncio</h3>
            <p><em>(En esta práctica aún no se implementa la visualización de fotos adicionales)</em></p>
        </section>
        
    <?php else: ?>
        <p class="error">No se encontró el anuncio solicitado.</p>
        <a href="mis_anuncios.php">Volver a Mis Anuncios</a>
    <?php endif; ?>
</main>

<?php
include "footer.php";
?>