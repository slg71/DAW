<?php

// // Capturar posibles errores individuales
// $error = $_GET["error"] ?? "";
// $errores = [];
// ... (código comentado, se omite por brevedad)

// 1. Inicia la sesión al principio de la página
session_start();

$titulo_pagina = "Iniciar Sesión";
include "paginas_Estilo.php";
include "header.php";
?>

<main id="login">
    <h2>Iniciar sesión</h2>

    <form action="control_acceso.php" method="post" novalidate>
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" 
            value="<?php echo htmlspecialchars($_GET['usuario'] ?? ($_COOKIE['recordar_usuario'] ?? '')); ?>">

        <label for="pwd">Contraseña</label>
        <input type="password" id="pwd" name="pwd" 
            value="<?php echo htmlspecialchars($_COOKIE['recordar_pass'] ?? ''); ?>">

        <label class="checkbox-recordar">Recordarme
            <input type="checkbox" name="recordar" <?php echo isset($_COOKIE['recordar_usuario']) ? 'checked' : ''; ?>>
        </label>

        <?php 
        // Lógica de visualización de errores (resuelto el conflicto)
        if (isset($_SESSION['mensaje_error_login'])): 
        ?>
            <span class="error-campo">
                <?php
                echo $_SESSION['mensaje_error_login'];
                // 4. Bórralo de la sesión para que no vuelva a salir en la próxima carga
                unset($_SESSION['mensaje_error_login']); 
                ?>
            </span>
        <?php endif; ?>

        <form action="control_acceso.php" method="post" novalidate class="form-login">
            <button type="submit">Confirmar</button> 
        </form>
    </form>

</main>

<?php include "footer.php"; ?>
</body>
</html>