<header>
    <h1>PI - Pisos & Inmuebles</h1>
    <p id="eslogan">Tu nuevo hogar te espera</p>
        <nav>
            <a href="ultimos_anuncios.php"><i class="icon-search"></i>Últimos anuncios</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="inicio_registrado.php"><i class="icon-home"></i>Inicio</a>
                <a href="../publicar.html"><i class="icon-upload"></i>Publicar anuncio</a>
                <a href="MenuRegistradoUsu.php"><i class="icon-menu"></i>Menú de Usuario</a>
                <a href="../perfil.html"><img src="../img/perfil.jpg" alt="Foto de Perfil">Perfil</a>
            <?php else: ?>
                <a href="registro.php">Registro</a>
                <a href="login.php">Iniciar Sesión</a>
                <a href="index.php">Inicio</a>
            <?php endif; ?>
        </nav>
</header>