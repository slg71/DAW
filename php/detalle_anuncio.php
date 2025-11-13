<?php
session_start(); // Inicia la sesión
ob_start();

// Comprueba si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// ---------------------------------------------
// Página: detalle.php (Navegación pública)
// ---------------------------------------------

$titulo_pagina = "Detalle del anuncio";
include "paginas_Estilo.php";
include "header.php";

// Obtener ID
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Incluir datos
require_once("anuncios.inc.php");

// Elegir anuncio par/impar
$anuncio = null;
if ($id > 0) {
    if ($id % 2 == 0) {
        $anuncio = isset($anuncio_par) ? $anuncio_par : null;
    } else {
        $anuncio = isset($anuncio_impar) ? $anuncio_impar : null;
    }
}

if ($anuncio !== null) {
    $nombre_cookie = 'ultimos_anuncios';
    $lista_visitados = [];

    // 1. Recuperar lista actual
    if (isset($_COOKIE[$nombre_cookie])) {
        $lista_visitados = json_decode($_COOKIE[$nombre_cookie], true);
        if (!is_array($lista_visitados)) $lista_visitados = [];
    }

    // 2. Actualizar lista (eliminar si existe, poner al principio)
    $lista_visitados = array_diff($lista_visitados, [$id]);
    array_unshift($lista_visitados, $id);
    $lista_visitados = array_slice($lista_visitados, 0, 4);

    // 3. Guardar cookie (1 semana)
    setcookie($nombre_cookie, json_encode($lista_visitados), time() + (7 * 24 * 60 * 60), "/");
}
?>

<main id="anuncio">
    <?php if ($anuncio): ?>
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

                <section>
                    <?php
                    foreach ($anuncio["fotos"] as $foto) {
                        echo "<img src='" . htmlspecialchars($foto) . "' alt='Miniatura'>";
                    }
                    ?>
                </section>

                <p><strong>Anunciante:</strong> <?php echo htmlspecialchars($anuncio["usuario"]); ?></p>

                <form action="mensaje.php" method="get">
                    <button type="submit">Enviar mensaje</button>
                </form>
            </article>
        </section>
    <?php else: ?>
        <section id="bloque">
            <p>El anuncio no existe.</p>
        </section>
    <?php endif; ?>
</main>

<?php
include "footer.php";
ob_end_flush();
?>