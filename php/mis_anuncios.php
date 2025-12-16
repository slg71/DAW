<?php
ob_start();
require_once "sesion_control.php";

include "funciones_anuncios.php";
include "funciones_imagenes.php";

// PARAMETRO CONFIGURABLE [cite: 22]
$anuncios_por_pagina = 2; 

$id_usuario = obtener_id_usuario_numerico($_SESSION['usuario_id']);
$anuncios_usuario = [];
if ($id_usuario !== null) {
    $anuncios_usuario = obtener_anuncios_usuario($id_usuario);
}

// LÓGICA DE PAGINACIÓN [cite: 30]
$num_total_anuncios = count($anuncios_usuario);
$total_paginas = ceil($num_total_anuncios / $anuncios_por_pagina);

$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
if ($pagina_actual > $total_paginas && $total_paginas > 0) $pagina_actual = $total_paginas;

$inicio = ($pagina_actual - 1) * $anuncios_por_pagina;
// Extraemos solo los anuncios de la página actual [cite: 289]
$anuncios_mostrar = array_slice($anuncios_usuario, $inicio, $anuncios_por_pagina);

$titulo_pagina = "Mis Anuncios (" . $num_total_anuncios . ")";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <h2>Mis Anuncios Publicados</h2>
    <p style="text-align:center;">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></p>

    <?php if ($num_total_anuncios > 0): ?>
        <section id="listado-mis-anuncios">
            <?php foreach ($anuncios_mostrar as $anuncio): ?>
                <?php $ruta_foto = "../img/" . $anuncio['FPrincipal']; ?>
                <article class="tarjeta-anuncio">
                    <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                        <img src="<?php echo generar_miniatura($ruta_foto, 200); ?>" 
                             alt="Miniatura de <?php echo htmlspecialchars($anuncio['Titulo']); ?>">
                    </a>
                    <h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3>
                    <p><strong>Precio:</strong> <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                    
                    <footer class="botones-anuncio">
                        <button><a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver/Editar</a></button>
                        <button><a href="eliminar_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Eliminar</a></button>
                        <button><a href="solicitar_folleto.php?anuncio_id=<?php echo $anuncio['IdAnuncio']; ?>">Folleto</a></button>
                        <button><a href="añadir_foto.php?anuncio_id=<?php echo $anuncio['IdAnuncio']; ?>">+ Foto</a></button>
                    </footer>
                </article>
            <?php endforeach; ?>
        </section>

        <nav class="paginacion-container">
            <a href="?p=1" class="btn-pag <?php if($pagina_actual == 1) echo 'disabled'; ?>">|&laquo; Primero</a>
            <a href="?p=<?php echo $pagina_actual - 1; ?>" class="btn-pag <?php if($pagina_actual == 1) echo 'disabled'; ?>">Anterior</a>
            <a href="?p=<?php echo $pagina_actual + 1; ?>" class="btn-pag <?php if($pagina_actual >= $total_paginas) echo 'disabled'; ?>">Siguiente</a>
            <a href="?p=<?php echo $total_paginas; ?>" class="btn-pag <?php if($pagina_actual >= $total_paginas) echo 'disabled'; ?>">Último &raquo;|</a>
        </nav>

    <?php else: ?>
        <p>No tienes anuncios. <a href="crear_anuncio.php">Crea uno aquí</a>.</p>
    <?php endif; ?>
</main>

<?php include "footer.php"; ob_end_flush(); ?>