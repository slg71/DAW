<header>
    <h1>PI - Pisos & Inmuebles</h1>
    <p id="eslogan">Tu nuevo hogar te espera</p>
    <nav>
        <a href="ultimos_anuncios.php"><i class="icon-search"></i>Últimos anuncios</a>

        <?php
        // Comprobamos si la variable de sesión 'usuario_id' esta 
        if (isset($_SESSION['usuario_id'])) {
        ?>
            <a href="inicio_registrado.php"><i class="icon-home"></i>Inicio</a>
            <a href="../publicar.html"><i class="icon-upload"></i>Publicar anuncio</a>
            <a href="MenuRegistradoUsu.php"><i class="icon-menu"></i>Menú de Usuario</a>
            <a href="../perfil.html"><img src="../img/perfil.jpg" alt="Foto de Perfil">Perfil</a>
        <?php
        } else {
            // Enlaces que solo ve el usuario NO REGISTRADO/PUBLICO
        ?>
            <a href="index.php"><i class="icon-home"></i>Inicio</a>
            <a href="registro.php">Registro</a>
            <a href="login.php"><i class="icon-login"></i>Iniciar Sesión</a>
        <?php
        }
        ?>
    </nav>
</header>