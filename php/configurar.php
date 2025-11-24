<?php
session_start(); // Inicia la sesión
ob_start();

// Comprueba si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// ---------------------------------------------
// Página: configurar.php
// ---------------------------------------------

$titulo_pagina = "Configurar Estilo"; 
include "paginas_Estilo.php";
include "header.php";

// 1. AÑADIMOS LA CONEXIÓN
include "conexion_bd.php"; 
$estilo_actual_id = 0;
$lista_estilos = []; // Array para guardar los estilos
$mensaje_error = "";

// 2. CONECTAR Y CONSULTAR
$mysqli = conectarBD();
if ($mysqli) {
    // obtener estilo del usuario actual
    $id_usuario = $_SESSION['usuario_id'];
    $sql_usuario = "SELECT Estilo FROM usuarios WHERE IdUsuario = ?";
    $stmt = mysqli_prepare($mysqli, $sql_usuario);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $estilo_actual_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // obtener estilos disponibles
    $sql_estilos = "SELECT IdEstilo, Nombre, Descripcion, Fichero FROM estilos";
    $resultado = mysqli_query($mysqli, $sql_estilos);
    
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $lista_estilos[] = $fila;
        }
        mysqli_free_result($resultado);
    } else {
        $mensaje_error = "Error al consultar los estilos: " . mysqli_error($mysqli);
    }
    
    mysqli_close($mysqli);
    // // La consulta para pillar todos los estilos
    // $sql = "SELECT Nombre, Descripcion, Fichero FROM estilos";
    
    // // Como no hay datos del usuario (GET/POST), usamos mysqli_query
    // $resultado = @mysqli_query($mysqli, $sql);
    
    // if (!$resultado) {
    //     $mensaje_error = "Error al consultar los estilos: " . mysqli_error($mysqli);
    // } else {
    //     // Metemos los resultados en el array
    //     while ($fila = mysqli_fetch_assoc($resultado)) {
    //         $lista_estilos[] = $fila;
    //     }
        
    //     // El PDF dice que hay que liberar el resultado
    //     mysqli_free_result($resultado);
    // }
    
    // // Cerramos
    // mysqli_close($mysqli);
    
} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}

?>

<main>
    <section id="bloque">
        <h2>Selecciona tu estilo</h2>
        
        <?php if ($mensaje_error != ""): ?>
            <p class="error-campo"><?php echo $mensaje_error; ?></p>
        <?php else: ?>
            
            <p>Elige cómo quieres visualizar la aplicación web. Tu elección se guardará para futuras visitas.</p>

            <form action="respuesta_configurar.php" method="POST">
                
                <label for="estilo_seleccionado">Estilos disponibles:</label>
                <select name="estilo" id="estilo_seleccionado">
                    <?php foreach ($lista_estilos as $estilo): ?>
                        <option value="<?php echo $estilo['IdEstilo']; ?>" 
                            <?php echo ($estilo['IdEstilo'] == $estilo_actual_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($estilo['Nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <section id="bloque">
                    <ul>
                    <?php foreach ($lista_estilos as $estilo): ?>
                        <li><strong><?php echo htmlspecialchars($estilo['Nombre']); ?>:</strong> <?php echo htmlspecialchars($estilo['Descripcion']); ?></li>
                    <?php endforeach; ?>
                    </ul>
                </section>

                <button type="submit">Guardar Configuración</button>
            </form>

        <?php endif; ?>
        
    </section>
</main>

<?php
include "footer.php";
ob_end_flush();
?>