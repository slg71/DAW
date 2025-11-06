<?php
// 1. Inicia la sesión
session_start();

// 2. Recuperamos los errores de la sesión (si existen)
$errores = [];
if (isset($_SESSION['errores_registro'])) {
    $errores = $_SESSION['errores_registro'];
    unset($_SESSION['errores_registro']);
}

// 3. Recuperamos los datos del formulario de la sesión (si existen)
$datos_previos = [];
if (isset($_SESSION['datos_form_registro'])) {
    $datos_previos = $_SESSION['datos_form_registro'];
    unset($_SESSION['datos_form_registro']);
}

// 4. Asignamos los valores a variables
$usuario = $datos_previos["usuario"] ?? "";
$email   = $datos_previos["email"] ?? "";
$sexo    = $datos_previos["sexo"] ?? "";
$pais    = $datos_previos["pais"] ?? "";
$ciudad  = $datos_previos["ciudad"] ?? "";
$nac     = $datos_previos["nac"] ?? "";

$titulo_pagina = "Registro";
include "paginas_Estilo.php";
include "header.php";
?>

<main id="registro">
    <h2>Registro</h2>

    <form action="respuesta_registro.php" method="post" enctype="multipart/form-data" novalidate>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" 
               value="<?php echo htmlspecialchars($usuario); ?>">
        <?php if (isset($errores["usuario"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["usuario"]); ?></span>
        <?php endif; ?>

        <label for="pwd">Contraseña</label>
        <input type="password" id="pwd" name="pwd">
        <?php if (isset($errores["pwd"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["pwd"]); ?></span>
        <?php endif; ?>

        <label for="pwd2">Repetir Contraseña</label>
        <input type="password" id="pwd2" name="pwd2">
        <?php if (isset($errores["pwd2"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["pwd2"]); ?></span>
        <?php endif; ?>

        <label for="sexo">Sexo</label>
        <select id="sexo" name="sexo">
            <option value="">---</option>
            <option value="hombre" <?php if ($sexo=="hombre") echo "selected"; ?>>Hombre</option>
            <option value="mujer" <?php if ($sexo=="mujer") echo "selected"; ?>>Mujer</option>
            <option value="otro" <?php if ($sexo=="otro") echo "selected"; ?>>Otro</option>
        </select>
        <?php if (isset($errores["sexo"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["sexo"]); ?></span>
        <?php endif; ?>

        <label for="nac">Fecha de nacimiento</label>
        <input type="date" id="nac" name="nac" 
               value="<?php echo htmlspecialchars($nac); ?>">
        <?php if (isset($errores["nac"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["nac"]); ?></span>
        <?php endif; ?>

        <label for="ciudad">Ciudad de residencia</label>
        <input type="text" id="ciudad" name="ciudad" 
               value="<?php echo htmlspecialchars($ciudad); ?>">
        <?php if (isset($errores["ciudad"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["ciudad"]); ?></span>
        <?php endif; ?>

        <label for="pais">País de residencia</label>
        <select id="pais" name="pais">
            <option value="">---</option>
            <option value="espana" <?php if ($pais=="espana") echo "selected"; ?>>España</option>
            <option value="uk" <?php if ($pais=="espana") echo "selected"; ?>>Reino Unido</option>
            <option value="italia" <?php if ($pais=="espana") echo "selected"; ?>>Italia</option>
            <option value="francia" <?php if ($pais=="espana") echo "selected"; ?>>Francia</option>
            <option value="usa" <?php if ($pais=="espana") echo "selected"; ?>>Estados Unidos</option>
            <option value="china" <?php if ($pais=="espana") echo "selected"; ?>>China</option>
            <option value="japon" <?php if ($pais=="espana") echo "selected"; ?>>Japón</option>
            <option value="sk" <?php if ($pais=="espana") echo "selected"; ?>>Corea del Sur</option>
            <option value="india" <?php if ($pais=="espana") echo "selected"; ?>>India</option>
            <option value="australia" <?php if ($pais=="espana") echo "selected"; ?>>Australia</option>
        </select>
        <?php if (isset($errores["pais"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["pais"]); ?></span>
        <?php endif; ?>

        <label for="email">Dirección de email</label>
        <input type="text" id="email" name="email" 
               value="<?php echo htmlspecialchars($email); ?>">
        <?php if (isset($errores["email"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["email"]); ?></span>
        <?php endif; ?>

        <label for="foto">Foto de perfil</label>
        <input type="file" id="foto" name="foto">

        <button type="submit">Confirmar</button>
        <button type="reset">Limpiar</button>
    </form>
</main>

<?php include "footer.php"; ?>
</body>
</html>