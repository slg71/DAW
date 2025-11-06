<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: inicio_registrado.php (usuario autenticado simulado)
// -------------------------------------------------------------
$titulo_pagina = "Inicio (Usuario) - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";
?>

<main>
    <a href="#listado" class="saltar">Saltar al contenido principal</a>

    <section id="busqueda">
        <h2>Búsqueda rápida</h2>
        <form action="resultado.php" method="get">
            <fieldset>
                <legend>Datos de búsqueda</legend>
                <input type="text" id="buscar" name="buscar" placeholder="Ciudad...">
            </fieldset>
            <button type="submit"><i class="icon-search"></i>Buscar</button>
        </form>
    </section>

    <section id="listado">
        <h2>Anuncios recientes</h2>

        <article>
            <img src="../img/piso.jpg" alt="Foto anuncio 1">
            <h3><a href="detalle_anuncio.php?id=1">Piso renovado en el centro</a></h3>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto anuncio 2">
            <h3><a href="detalle_anuncio.php?id=2">Casa con jardín privado</a></h3>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto anuncio 3">
            <h3><a href="detalle_anuncio.php?id=3">Apartamento moderno con vistas</a></h3>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto anuncio 4">
            <h3><a href="detalle_anuncio.php?id=4">Ático con terraza</a></h3>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto anuncio 5">
            <h3><a href="detalle_anuncio.php?id=5">Piso céntrico reformado</a></h3>
        </article>
    </section>
</main>

<?php include "footer.php"; ?>
</body>
</html>
