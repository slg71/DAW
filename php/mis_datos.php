<?php
session_start(); // Inicia la sesión
ob_start();

// Comprueba si el usuario está logueado (esta página es privada)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// ---------------------------------------------
// Página: mis_datos.php
// ---------------------------------------------

$titulo_pagina = "Mis Datos"; 
include "paginas_Estilo.php";
include "header.php";

// 1. AÑADIMOS LA CONEXIÓN
include "conexion_bd.php"; 

$datos_usuario = null; // Para guardar la info
$paises = []; // Para el <select>
$mensaje_error = "";

// Pillamos el ID de la sesión (¡NO de $_GET!)
$id_usuario_actual = (int)$_SESSION['usuario_id'];

// 2. CONECTAR Y CONSULTAR
$mysqli = conectarBD();
if ($mysqli) {
    
    // 3. CONSULTA 1: Pillar los datos del usuario actual
    $sql_usuario = "SELECT NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais 
                    FROM usuarios 
                    WHERE IdUsuario = ?";
    
    $stmt_usuario = mysqli_prepare($mysqli, $sql_usuario); // [cite: 1332]
    
    if ($stmt_usuario) {
        mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario_actual); // [cite: 1335]
        mysqli_stmt_execute($stmt_usuario); // [cite: 1337]
        
        $resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
        
        if ($resultado_usuario && mysqli_num_rows($resultado_usuario) == 1) {
            $datos_usuario = mysqli_fetch_assoc($resultado_usuario);
        } else {
            $mensaje_error = "Error: No se pudieron encontrar tus datos.";
        }
        mysqli_stmt_close($stmt_usuario); // [cite: 1355]

    } else {
        $mensaje_error = "Error al preparar la consulta de usuario.";
    }

    // 4. CONSULTA 2: Pillar la lista de países para el <select>
    // (Esto es como en configurar.php, una consulta simple) [cite: 1049]
    $sql_paises = "SELECT IdPais, NomPais FROM paises ORDER BY NomPais";
    $resultado_paises = mysqli_query($mysqli, $sql_paises);
    
    if ($resultado_paises) {
        while ($fila_pais = mysqli_fetch_assoc($resultado_paises)) {
            $paises[] = $fila_pais;
        }
        mysqli_free_result($resultado_paises); // [cite: 1055]
    } else {
        $mensaje_error .= " Error al cargar la lista de países.";
    }

    mysqli_close($mysqli); // [cite: 1057]

} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}

?>

<main>
    <section>
        <h2>Mis Datos de Registro</h2>
        
        <p>Aquí puedes ver tus datos. La modificación se activará en la próxima práctica.</p>

        <?php if ($mensaje_error != ""): ?>
            <p style="color: red;"><?php echo $mensaje_error; ?></p>
        
        <?php elseif ($datos_usuario): // Si tenemos los datos, mostramos el form ?>
        
            <form action="" method="post"> <fieldset>
                    <legend>Información de la cuenta</legend>
                    
                    <label for="nomUsuario">Nombre de usuario:</label>
                    <input type="text" id="nomUsuario" name="nomUsuario" 
                           value="<?php echo htmlspecialchars($datos_usuario['NomUsuario']); ?>" 
                           disabled>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($datos_usuario['Email']); ?>" 
                           disabled>
                           
                    <label for="clave">Contraseña:</label>
                    <input type="password" id="clave" name="clave" 
                           value="********" 
                           disabled>
                </fieldset>

                <fieldset>
                    <legend>Información personal</legend>
                    
                    <label>Sexo:</label>
                    <input type="radio" id="sexoHombre" name="sexo" value="1" 
                           <?php if ($datos_usuario['Sexo'] == 1) echo 'checked'; ?> 
                           disabled>
                    <label for="sexoHombre">Hombre</label>
                    
                    <input type="radio" id="sexoMujer" name="sexo" value="0" 
                           <?php if ($datos_usuario['Sexo'] == 0) echo 'checked'; ?> 
                           disabled>
                    <label for="sexoMujer">Mujer</label>

                    <br><br>

                    <label for="fnacimiento">Fecha de nacimiento:</label>
                    <input type="date" id="fnacimiento" name="fnacimiento" 
                           value="<?php echo htmlspecialchars($datos_usuario['FNacimiento']); ?>" 
                           disabled>

                    <label for="ciudad">Ciudad:</label>
                    <input type="text" id="ciudad" name="ciudad" 
                           value="<?php echo htmlspecialchars($datos_usuario['Ciudad']); ?>" 
                           disabled>
                           
                    <label for="pais">País:</label>
                    <select id="pais" name="pais" disabled>
                        <?php
                        foreach ($paises as $pais) {
                            $id = $pais['IdPais'];
                            $nombre = htmlspecialchars($pais['NomPais']);
                            // Comparamos el IdPais del bucle con el 'Pais' del usuario
                            $seleccionado = ($id == $datos_usuario['Pais']) ? 'selected' : '';
                            
                            echo "<option value='$id' $seleccionado>$nombre</option>";
                        }
                        ?>
                    </select>

                </fieldset>

                <button type="submit" disabled>Modificar (No disponible)</button>
            </form>
            
        <?php endif; ?>
        
    </section>
</main>

<?php
include "footer.php";
ob_end_flush();
?>