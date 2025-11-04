<?php
// -------------------------------------------------------------
// Página: mis_anuncios.php
// -------------------------------------------------------------

$titulo_pagina = "Mis Anuncios"; 

include "paginas_Estilo.php";
include "header.php";

// anuncios ficticios 
$anuncios = [
    [
        'foto' => './img/piso2.jpg',
        'titulo' => 'Apartamento céntrico renovado',
        'ciudad' => 'Alicante',
        'pais' => 'España',
        'precio' => '900',
        'fecha' => '2025-09-15',
        'id' => 1
    ],
    [
        'foto' => './img/piso1.jpg',
        'titulo' => 'Piso moderno con terraza',
        'ciudad' => 'Valencia',
        'pais' => 'España',
        'precio' => '220000',
        'fecha' => '2025-09-18',
        'id' => 2
    ],
];
?>

<main id="mis-anuncios">

    <?php
    if (empty($anuncios)) {
        echo '<section><p>Aún no has publicado ningún anuncio.</p><p><a href="crear_anuncio.php">¡Publica tu primer anuncio ahora!</a></p></section>';
    } else {
    ?>

    <section id="listado-anuncios">
        <h2>MIS ANUNCIOS PUBLICADOS</h2>

        <?php foreach ($anuncios as $anuncio): ?>
        
        <article>
            
            <a href="ver_anuncio.php?id=<?php echo $anuncio['id']; ?>">
              <img src="<?php echo htmlspecialchars($anuncio['foto']); ?>" 
                   alt="Foto principal: <?php echo htmlspecialchars($anuncio['titulo']); ?>">
            </a>

            <h3><?php echo htmlspecialchars($anuncio['titulo']); ?></h3>
            <p>Fecha: <?php echo htmlspecialchars($anuncio['fecha']); ?></p>
            <p>Ciudad: <?php echo htmlspecialchars($anuncio['ciudad']); ?></p>
            <p>País: <?php echo htmlspecialchars($anuncio['pais']); ?></p>
            <p>Precio: <?php echo number_format($anuncio['precio'], 0, ',', '.'); ?> €</p>

            <section>
                <a href="añadir_foto.php?anuncio_id=<?php echo $anuncio['id']; ?>">
                    Añadir Foto
                </a>

                <a href="editar_anuncio.php?id=<?php echo $anuncio['id']; ?>">
                    Editar
                </a>
                
                <form action="gestion_anuncios.php" method="post" style="display:inline;">
                    <button type="submit" 
                            name="eliminar" 
                            value="<?php echo $anuncio['id']; ?>" 
                            onclick="return confirm('¿Estás seguro de que quieres eliminar este anuncio?');"
                            class="boton-eliminar">
                        Eliminar
                    </button>
                </form>
            </section>
        </article>
        <?php endforeach; ?>
    </section>
    <?php } ?>
</main>

<?php
include "footer.php";
?>