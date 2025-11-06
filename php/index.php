<?php
// -------------------------------------------------------------
// Página: index.php (versión pública, sin sesión)
// -------------------------------------------------------------
$titulo_pagina = "Inicio - PI Pisos & Inmuebles";
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
            <img src="../img/piso.jpg" alt="Foto principal">
            <details>
                <summary><h3>Piso renovado en el centro</h3></summary>
                <p>Acceso restringido — Debes iniciar sesión para ver los detalles.</p>
            </details>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto principal">
            <details>
                <summary><h3>Casa moderna con jardín</h3></summary>
                <p>Acceso restringido — Debes iniciar sesión para ver los detalles.</p>
            </details>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto principal">
            <details>
                <summary><h3>Ático con terraza en el centro</h3></summary>
                <p>Acceso restringido — Debes iniciar sesión para ver los detalles.</p>
            </details>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto principal">
            <details>
                <summary><h3>Apartamento junto al mar</h3></summary>
                <p>Acceso restringido — Debes iniciar sesión para ver los detalles.</p>
            </details>
        </article>

        <article>
            <img src="../img/piso.jpg" alt="Foto principal">
            <details>
                <summary><h3>Piso céntrico reformado</h3></summary>
                <p>Acceso restringido — Debes iniciar sesión para ver los detalles.</p>
            </details>
        </article>
    </section>
</main>
<?php include "footer.php"; ?>
</body>
</html>
