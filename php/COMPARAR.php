<?php
ob_start();

require_once "sesion_control.php";

// -------------------------------------------------------------
// Pagina: mis_anuncios.php
// Muestra el listado de anuncios del usuario logueado.
// -------------------------------------------------------------

include "funciones_anuncios.php";
include "funciones_imagenes.php";

// PARAMETRO CONFIGURABLE: Cambia este numero para ver 3, 5, 6.
$anuncios_por_pagina = 5; 
$id_usuario = obtener_id_usuario_numerico($_SESSION['usuario_id']);//en funciones_anuncios

$anuncios_usuario = [];
if ($id_usuario !== null) {
    $anuncios_usuario = obtener_anuncios_usuario($id_usuario);//en funciones_anuncios
}

// PAGINACION
$num_total_anuncios = count($anuncios_usuario);
$total_paginas = ceil($num_total_anuncios  / $anuncios_por_pagina);

// Obtener la pagina actual de la URL
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1; //cogemos la primera pagina po defecto
if ($pagina_actual < 1) $pagina_actual = 1;
if ($pagina_actual > $total_paginas && $total_paginas > 0) $pagina_actual = $total_paginas;

// Calcular el indice de inicio para el trozo de array
$inicio = ($pagina_actual - 1) * $anuncios_por_pagina;
$anuncios_mostrar = array_slice($anuncios_usuario, $inicio, $anuncios_por_pagina);

$titulo_pagina = "Mis Anuncios (" . $num_total_anuncios . ")";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <h2>Mis Anuncios Publicados</h2>
    <p>Mostrando página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?> (Total: <?php echo $num_total_anuncios  ?> anuncios).</p>

    <?php if ($num_total_anuncios > 0): ?>
        <section id="listado">
            <?php foreach ($anuncios_mostrar as $anuncio): ?>
                <?php 
                    //calcular la ruta física del archivo en el servidor
                    $ruta_foto = "../img/" . $anuncio['FPrincipal'];
                ?>
                <article>
                    <a href="ver_anuncio.php?id=<?php echo htmlspecialchars($anuncio['IdAnuncio']); ?>">
                        <img 
                        src="<?php echo generar_miniatura($ruta_foto, 150); ?>" 
                        alt="Miniatura de <?php echo htmlspecialchars($anuncio['Titulo']); ?>"
                        >
                    </a>
                    <h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3>
                    <p><?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                    <p><strong>Tipo:</strong> <?php echo htmlspecialchars($anuncio['NomTAnuncio']); ?> - <?php echo htmlspecialchars($anuncio['NomTVivienda']); ?></p>
                    <p>Publicado el: <?php echo htmlspecialchars($anuncio['FRegistro']); ?></p>

                    <section id="bloque-botones">
                        <button>
                            <!-- Enlace para ver detalle/editar -->
                            <a href="ver_anuncio.php?id=<?php echo htmlspecialchars($anuncio['IdAnuncio']); ?>">Ver/Editar</a>
                        </button>
                                                <button>
                            <!-- Botón para eliminar (requeriría otra página de procesamiento) -->
                            <a href="eliminar_anuncio.php?id=<?php echo htmlspecialchars($anuncio['IdAnuncio']); ?>">Eliminar</a>
                        </button>
                        <button>
                            <!-- Botón para solicitar folleto -->
                            <a href="solicitar_folleto.php?anuncio_id=<?php echo htmlspecialchars($anuncio['IdAnuncio']); ?>">Solicitar Folleto</a>
                        </button>

                        <button>
                           <!--Botón para añadir foto-->
                            <a href="añadir_foto.php?anuncio_id=<?php echo $anuncio['IdAnuncio']; ?>">
                                Añadir Foto
                            </a>
                        </button>
                    </section>
                </article>
            <?php endforeach; ?>
        </section>

        <nav id="paginacion">
            <a href="?p=1" <?php if($pagina_actual == 1) echo 'style="pointer-events:none; opacity:0.5;"'; ?>>|&laquo; Primero</a>
            
            <a href="?p=<?php echo $pagina_actual - 1; ?>" <?php if($pagina_actual == 1) echo 'style="pointer-events:none; opacity:0.5;"'; ?>> &lt; Anterior</a>

            <a href="?p=<?php echo $pagina_actual + 1; ?>" <?php if($pagina_actual == $total_paginas) echo 'style="pointer-events:none; opacity:0.5;"'; ?>>Siguiente &gt; </a>

            <a href="?p=<?php echo $total_paginas; ?>" <?php if($pagina_actual == $total_paginas) echo 'style="pointer-events:none; opacity:0.5;"'; ?>>Último &raquo;|</a>
        </nav>

            <section id="bloque">
            <button>
                <a href="añadir_foto.php">
                    Añadir Foto a Anuncio
                </a>
            </button>
            <button>
                <a href="crear_anuncio.php">
                    Crear anuncio
                </a>
            </button>
        </section>
        <?php else: ?>
        <p>Aún no has publicado ningún anuncio. <a href="crear_anuncio.php">Publica el primero ahora</a>.</p>
    <?php endif; ?>
</main>

<?php
include "footer.php";
ob_end_flush();
?>