<?php
session_start();
ob_start();

// Comprobación de seguridad: debe estar logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Página: mis_anuncios.php
// Muestra el listado de anuncios del usuario logueado.
// -------------------------------------------------------------

include "funciones_anuncios.php";
// La función obtener_id_usuario_numerico y obtener_anuncios_usuario están en funciones_anuncios.php

// 1. Obtener el ID numérico del usuario
$id_usuario = obtener_id_usuario_numerico($_SESSION['usuario_id']);

$anuncios_usuario = [];
if ($id_usuario !== null) {
    // 2. Obtener el listado de anuncios
    $anuncios_usuario = obtener_anuncios_usuario($id_usuario);
}

$num_total_anuncios = count($anuncios_usuario);

$titulo_pagina = "Mis Anuncios (" . $num_total_anuncios . ")";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <h2>Mis Anuncios Publicados</h2>
    
    <p>
        Actualmente tienes <?php echo $num_total_anuncios; ?> anuncio(s) publicado(s).
    </p>

    <?php if ($num_total_anuncios > 0): ?>
        <section id="listado">
            <?php foreach ($anuncios_usuario as $anuncio): ?>
                <article>
                    <a href="ver_anuncio.php?id=<?php echo htmlspecialchars($anuncio['IdAnuncio']); ?>">
                        <img 
                            src="../img/<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" 
                            alt="Foto principal de <?php echo htmlspecialchars($anuncio['Titulo']); ?>"
                        >
                    </a>
                            <h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3>
                            <p><?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($anuncio['NomTAnuncio']); ?> - <?php echo htmlspecialchars($anuncio['NomTVivienda']); ?></p>
                            <p>Publicado el: <?php echo htmlspecialchars($anuncio['FRegistro']); ?></p>

                    <section id="bloque">
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
                            <!--Botón para añadir foto-->
                        <button>
                            <a href="añadir_foto.php?anuncio_id=<?php echo $anuncio['IdAnuncio']; ?>">
                            Añadir Foto
                            </a>
                        </button>
                    </section>
                </article>
            <?php endforeach; ?>
        </section>

        <section id="bloque">
            <button>
                <a href="añadir_foto.php">
                    Añadir Foto a anuncio
                </a>
            </button>
            <button>
                <a href="crear_anuncio.php">
                    Crear anuncio
                </a>
            </button>
        </section>
    <?php else: ?>
        <p>
            Aún no has publicado ningún anuncio. ¡<a href="crear_anuncio.php">Publica el primero ahora</a>!
        </p>
    <?php endif; ?>
</main>

<?php
include "footer.php";
ob_end_flush();
?>