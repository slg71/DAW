<?php
// -------------------------------------------------------------
// Página: perfil.php
// -------------------------------------------------------------

include "sesion_control.php"; // Control central de sesión y cookies
$titulo_pagina = "Perfil"; 
include "paginas_Estilo.php";
include "header.php";

// anuncios ficticios (REEMPLAZA POR BD)
$anuncios = [
    [
        'foto' => '../img/piso.jpg',
        'titulo' => 'Apartamento céntrico renovado',
        'ciudad' => 'Alicante',
        'pais' => 'España',
        'precio' => '900',
        'fecha' => '2025-09-15',
        'id' => 1
    ],
    [
        'foto' => '../img/piso.jpg',
        'titulo' => 'Piso moderno con terraza',
        'ciudad' => 'Valencia',
        'pais' => 'España',
        'precio' => '220000',
        'fecha' => '2025-09-18',
        'id' => 2
    ],
];
?>

<main>
    <section id="bloque">
        <h2>Datos del usuario</h2>
          <?php
          //ACTUALIZAR CON BD
            $fecha = date("D");
            $usuario = ucfirst($_SESSION['usuario_id']);
            //foto

            echo "<p>Nombre de usuario: $usuario</p>";
            echo "<p>Fecha de incorporación: $fecha</p>";
            echo "<p>Foto de perfil: </p>";
        ?>
    </section>
    <?php
    if (empty($anuncios)) {
        echo '<section><p>Aún no has publicado ningún anuncio.</p><p><a href="crear_anuncio.php">¡Publica tu primer anuncio ahora!</a></p></section>';
    } else {
    ?>

    <section id="listado">
        <h2>Mis Anuncios Publicados</h2>

        <?php foreach ($anuncios as $anuncio): ?>
        
        <article>
            
            <a href="ver_anuncio.php?id=<?php echo $anuncio['id']; ?>">
              <img src="<?php echo htmlspecialchars($anuncio['foto']); ?>" 
                   alt="Foto principal: <?php echo htmlspecialchars($anuncio['titulo']); ?>">
            </a>

            <h3><?php echo htmlspecialchars($anuncio['titulo']); ?></h3>
            <p>Fecha: <?php echo htmlspecialchars($anuncio['fecha']); ?></p>
            <p>Precio: <?php echo number_format($anuncio['precio'], 0, ',', '.'); ?> €</p>
        </article>
        <?php endforeach; ?>
    </section>
    <?php } ?>
</main>

<?php
include "footer.php";
?>