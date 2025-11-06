<?php
$titulo_pagina = "Iniciar Sesión";
include "paginas_Estilo.php";
include "header_publico.php";

// // Capturar posibles errores individuales
// $error = $_GET["error"] ?? "";
// $errores = [];

// switch ($error) {
//     case "empty":
//         $errores["usuario"] = "El campo 'Usuario' no puede estar vacío.";
//         $errores["pwd"] = "El campo 'Contraseña' no puede estar vacío.";
//         break;
//     case "usuario_vacio":
//         $errores["usuario"] = "El campo 'Usuario' no puede estar vacío.";
//         break;
//     case "pwd_vacio":
//         $errores["pwd"] = "El campo 'Contraseña' no puede estar vacío.";
//         break;
//     case "incorrect":
//         $errores["usuario"] = "Usuario o contraseña incorrectos.";
//         $errores["pwd"] = "Usuario o contraseña incorrectos.";
//         break;
// }

// 1. Inicia la sesión al principio de la página
session_start();

?>

<main id="login">
    <h2>Iniciar sesión</h2>

    <form action="control_acceso.php" method="post" novalidate>
        
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($_GET['usuario'] ?? ''); ?>">

        <label for="pwd">Contraseña</label>
        <input type="password" id="pwd" name="pwd">

        <?php if (isset($_SESSION['mensaje_error_login'])): ?>
            <span class="error-campo"><?php
                // 3. Muestra el mensaje
                echo "<span class='error-campo'>";
                echo $_SESSION['mensaje_error_login'];
                echo "</span>";
                
                // 4. Bórralo de la sesión para que no vuelva a salir
                // Esto es lo que lo convierte en "flashdata"
                unset($_SESSION['mensaje_error_login']); 
            ?></span>
        <?php endif; ?>
        <button type="submit">Confirmar</button>
    </form>
</main>

<?php include "footer.php"; ?>
</body>
</html>
