<?php
// 1. Iniciar sesión para que el header funcione bien (mostrar menús adecuados)
session_start();

$titulo_pagina = "Últimos Anuncios Visitados";
include "paginas_Estilo.php";
include "header.php";

// 2. Incluir el fichero con los "datos reales"
require_once("anuncios.inc.php");
?>

<main>
    <h2>Últimos Anuncios Visitados</h2>

    <?php
    $nombre_cookie = 'ultimos_anuncios';
    $lista_ids = [];

    // 3. Leer la cookie
    if (isset($_COOKIE[$nombre_cookie])) {
        $lista_ids = json_decode($_COOKIE[$nombre_cookie], true);
        if (!is_array($lista_ids)) {
            $lista_ids = [];
        }
    }

    // 4. Mostrar listado si hay datos
    if (!empty($lista_ids)) {
        echo '<section id="listado">';

        foreach ($lista_ids as $id_anuncio) {
            $id_anuncio = intval($id_anuncio);

            $datos_anuncio = null;
            if ($id_anuncio > 0) {
                if ($id_anuncio % 2 == 0) {
                    $datos_anuncio = isset($anuncio_par) ? $anuncio_par : null;
                } else {
                    $datos_anuncio = isset($anuncio_impar) ? $anuncio_impar : null;
                }
            }

            // Si por alguna razón no se cargaron los datos, saltamos este ID
            if ($datos_anuncio === null) continue;
            ?>

            <article onclick="location.href='detalle_anuncio.php?id=<?php echo $id_anuncio; ?>'" style="cursor: pointer;">
                
                <img src="<?php echo htmlspecialchars($datos_anuncio["fotos"][0]); ?>" 
                     alt="Foto anuncio <?php echo $id_anuncio; ?>">
                
                <div class="info-anuncio">
                    <h3><?php echo htmlspecialchars($datos_anuncio["titulo"]); ?></h3>
                    
                    <p>
                        <?php echo htmlspecialchars($datos_anuncio["ciudad"]); ?> | 
                        <?php echo number_format($datos_anuncio["precio"], 0, ',', '.'); ?> €
                    </p>
                </div>

                <a href="detalle_anuncio.php?id=<?php echo $id_anuncio; ?>">
                    <button>Ver de nuevo</button>
                </a>

            </article>
            <?php
        }
        echo '</section>';

    } else {
        echo '<section id="bloque">';
        echo '<p>Aún no has visitado ningún anuncio. ¡Explora nuestro catálogo!</p>';
        if (isset($_SESSION['usuario_id'])) {
            echo '<a href="inicio_registrado.php">Ir al Inicio</a>';
        } else {
            echo '<a href="index.php">Ir al Inicio</a>';
        }
        echo '</section>';
    }
    ?>
</main>

<?php
include "footer.php";
?>