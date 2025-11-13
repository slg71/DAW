<?php
// 1. Inicia la sesión
session_start();

// 2. Recuperamos los "flash data" (la estructura secundaria)
// Obtenemos la estructura completa que guardamos en el otro archivo
$flash_data = $_SESSION['_flash'] ?? [];

// 3. Borramos la estructura completa con UN SOLO unset()
unset($_SESSION['_flash']);

// 4. Extraemos los datos de la estructura que acabamos de recuperar
$errores = $flash_data['errors'] ?? [];
$datos_previos = $flash_data['old_input'] ?? [];

// 5. Asignamos los valores a variables (igual que antes, pero desde $datos_previos)
$usuario = $datos_previos["usuario"] ?? "";
$email   = $datos_previos["email"] ?? "";
$sexo    = $datos_previos["sexo"] ?? "";
$pais    = $datos_previos["pais"] ?? "";
$ciudad  = $datos_previos["ciudad"] ?? "";
$nac     = $datos_previos["nac"] ?? "";

// Incluir la conexión a la base de datos
require_once "conexion_bd.php";

$titulo_pagina = "Registro";
include "paginas_Estilo.php";
include "header.php";

// Conectar a la base de datos
$mysqli = conectarBD();

// Array para almacenar los paises
$paises = array();

if ($mysqli) {
    // Consulta para obtener los tipos de mensaje
    $sentencia = "SELECT IdPais, NomPais FROM Paises";
    
    if ($resultado = $mysqli->query($sentencia)) {
        // Recorrer los resultados y almacenarlos en el array
        while ($fila = $resultado->fetch_assoc()) {
            $paises[] = $fila;
        }
        
        // Liberar memoria del resultado
        $resultado->close();
    } else {
        echo "<p>Error al obtener los paises: " . $mysqli->error . "</p>";
    }
    
    // Cerrar la conexión
    $mysqli->close();
}
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
            <?php
            // Generar las opciones del select desde la base de datos
            foreach ($paises as $tipo) {
                echo '<option value="' . $tipo['IdPais'] . '">';
                echo htmlspecialchars($tipo['NomPais']);
                echo '</option>';
            }
            ?>
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

<?php include "footer.php";?>
</body>
</html>