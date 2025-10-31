<?php
$titulo_pagina = "Anuncio";
include = "paginas_Estilo.php";
include "header.php";
?>

        <nav>
            <a href="index_registrado.html">Inicio</a>
            <a href="publicar.html">Publicar anuncio</a>
            <a href="MenuRegistradoUsu.html">Menú de Usuario</a>
        </nav>
    </header>

    <main>
        <section id="anuncio">
            <h2>Piso renovado en el centro</h2>
            <article>
                <img src="./img/piso.jpg" alt="Foto principal">
                <h3>900€</h3><p>15/09/2025</p>
                <p>Un piso elegante en el centro de Alicante con buenas vistas y recién renovado.</p>
                <p>Alicante, España</p>
                <p>2 habitaciones, 1 baño, tercera planta</p>
                <img src="./img/piso.jpg" alt="Miniatura">
                <img src="./img/piso.jpg" alt="Miniatura">
                <img src="./img/piso.jpg" alt="Miniatura">
                <img src="./img/piso.jpg" alt="Miniatura">
                <img src="./img/piso.jpg" alt="Miniatura">
            
                <form action="mensaje.html" method="get">
                    <button type="submit">Mensaje</button>
                </form>            
            </article>
        </section>
    </main>

<?php
include "footer.php";
?>
    <script src="./js/micodigo.js"></script>
</body>
</html>