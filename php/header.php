<header>
    <h1>PI - Pisos & Inmuebles</h1>
    <p id="eslogan">Tu nuevo hogar te espera</p>
    <nav>
        <a href="inicio_registrado.php"><i class="icon-home"></i>Inicio</a>
        <a href="../publicar.html"><i class="icon-upload"></i>Publicar anuncio</a>
        
        <?php
        // Comprobamos si el usaurio esta registrado (variable de sesion 'usuario_id')
        if (isset($_SESSION['usuario_id'])) {
        ?>
            <a href="MenuRegistradoUsu.php"><i class="icon-menu"></i>Menú de Usuario</a>
            <a href="../perfil.html"><img src="../img/perfil.jpg" alt="Foto de Perfil">Perfil</a>
            <a href="salir.php"><i class="icon-logout"></i>Cerrar sesión</a>
        <?php
        } else {
            // Si no esta autenticado mostramos el enlace para que acceda
        ?>
            <a href="login.php"><i class="icon-login"></i>Iniciar Sesión</a>
        <?php
        }
        ?>
    </nav>
</header>