<?php
// -------------------------------------------------------------
// Página: mis_anuncios.php
// -------------------------------------------------------------


$titulo_pagina = "Mis Anuncios"; 

include "paginas_Estilo.php";
include "header.php";
?>


    <?php
    // anuncios ficticios 
    $anuncios = [
         [
            'foto' => '../img/piso.jpg',
            'titulo' => 'Piso renovado en el centro',
            'ciudad' => 'Alicante',
            'pais' => 'España',
            'precio' => '900€',
            'fecha' => '2025-10-30',
            'id' => 1
        ],
        [
            'foto' => '../img/piso.jpg',
            'titulo' => 'Chalet con jardín y piscina',
            'ciudad' => 'Mutxamel',
            'pais' => 'España',
            'precio' => '250.000€',
            'fecha' => '2025-10-25',
            'id' => 2
        ],
    ];
    ?>

<main id="mis-anuncios">

    <?php
    if (empty($anuncios)) { // Si no hay anuncios...
        echo '<section><p>Aún no has publicado ningún anuncio.</p><p><a href="crear_anuncio.php">¡Publica tu primer anuncio ahora!</a></p></section>';
    } else {
    ?>

    <section id="listado-anuncios">
        <h2>MIS ANUNCIOS PUBLICADOS</h2>

        <?php foreach ($anuncios as $anuncio): ?>
        
        <article>
            <a href="anuncio.php?id=<?php echo $anuncio['id']; ?>">
                <img src="<?php echo htmlspecialchars($anuncio['foto']); ?>" alt="Foto principal: <?php echo htmlspecialchars($anuncio['titulo']); ?>">
            </a>

            <h3><?php echo htmlspecialchars($anuncio['titulo']); ?></h3>
            <p>Fecha: <?php echo htmlspecialchars($anuncio['fecha']); ?></p>

            <p>Ciudad: <?php echo htmlspecialchars($anuncio['ciudad']); ?></p>
            <p>País: <?php echo htmlspecialchars($anuncio['pais']); ?></p>

            <p>Precio: <?php echo htmlspecialchars($anuncio['precio']); ?></p>

            <div class="acciones-anuncio">
                
                <!-- NUEVO: Enlace para añadir foto. Pasa el ID del anuncio como parámetro. -->
                <a href="form_subir_foto.php?anuncio_id=<?php echo $anuncio['id']; ?>" class="boton-agregar-foto">
                    Añadir Foto
                </a>

                <!-- Enlace para editar el anuncio (asumiendo que existe editar_anuncio.php) -->
                <a href="editar_anuncio.php?id=<?php echo $anuncio['id']; ?>" class="boton-editar">
                    Editar
                </a>
                <!-- Formulario para Eliminar -->
                 <form action="gestion_anuncios.php" method="post" style="display:inline;">
                    <button type="submit" 
                            name="eliminar" 
                            value="<?php echo $anuncio['id']; ?>" 
                            onclick="return confirm('¿Estás seguro de que quieres eliminar este anuncio? Esta acción es irreversible.');"
                            class="boton-eliminar">
                        Eliminar
                    </button>
                </form>
            </div>
        </article>
        <?php endforeach; ?>
    </section>
    <?php }?>
</main>

<?php
include "footer.php";
?>