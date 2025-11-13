<?php
session_start();
ob_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Página: ver_anuncio.php (Vista PRIVADA del propietario)
// -------------------------------------------------------------

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$anuncios = array(
    1 => array("titulo" => "Apartamento céntrico renovado", "precio" => 900, "imagen" => "./img/piso2.jpg", "tipo_anuncio" => "Alquiler", "tipo_vivienda" => "Apartamento", "fecha" => "2025-09-15", "ciudad" => "Alicante", "pais" => "España", "descripcion" => "Bonito apartamento...", "superficie" => 75, "habitaciones" => 2, "banos" => 1, "planta" => 3, "ano_construccion" => 2020),
    2 => array("titulo" => "Piso moderno con terraza", "precio" => 220000, "imagen" => "./img/piso1.jpg", "tipo_anuncio" => "Venta", "tipo_vivienda" => "Piso", "fecha" => "2025-09-18", "ciudad" => "Valencia", "pais" => "España", "descripcion" => "Luminoso piso...", "superficie" => 95, "habitaciones" => 3, "banos" => 2, "planta" => 5, "ano_construccion" => 2018)
);

$a = isset($anuncios[$id]) ? $anuncios[$id] : null;

$titulo_pagina = "Ver mi Anuncio";
include "paginas_Estilo.php";
include "header.php";
?>

<main id="bloque">
    <?php if ($a != null): ?>
        <section class="detalle-anuncio">
            <h2><?php echo htmlspecialchars($a["titulo"]); ?></h2>
            <img src="<?php echo htmlspecialchars($a["imagen"]); ?>" alt="Foto principal">
            
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($a["tipo_anuncio"]); ?> / <?php echo htmlspecialchars($a["tipo_vivienda"]); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($a["ciudad"]); ?>, <?php echo htmlspecialchars($a["pais"]); ?></p>
                <p><strong>Precio:</strong> <?php echo number_format($a["precio"], 0, ',', '.'); ?> €</p>

                <h3>Características</h3>
                <ul>
                    <li>Superficie: <?php echo $a["superficie"]; ?> m²</li>
                    <li>Habitaciones: <?php echo $a["habitaciones"]; ?></li>
                    <li>Baños: <?php echo $a["banos"]; ?></li>
                </ul>



                <h3>Descripción</h3>
                <p><?php echo htmlspecialchars($a["descripcion"]); ?></p>

        </section>

        <section id="bloque">
            <h2>Gestionar Anuncio</h2>
            <button>
                <a href="añadir_foto.php?anuncio_id=<?php echo $id; ?>">Añadir Foto</a>
            </button>
            <button>
                <a href="mis_anuncios.php">Volver a Mis Anuncios</a>
            </button>
        </section>
        
    <?php else: ?>
        <p class="error">No se encontró el anuncio solicitado.</p>
        <a href="mis_anuncios.php"><button>Volver</button></a>
    <?php endif; ?>
</main>

<?php
include "footer.php";
ob_end_flush();
?>