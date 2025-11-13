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

$lista_estilos = []; // Array para guardar los estilos
$mensaje_error = "";

// 2. CONECTAR Y CONSULTAR
$mysqli = conectarBD();
if ($mysqli) {
    
    // La consulta para pillar todos los estilos
    $sql = "SELECT Nombre, Descripcion, Fichero FROM estilos";
    
    // Como no hay datos del usuario (GET/POST), usamos mysqli_query
    $resultado = @mysqli_query($mysqli, $sql);
    
    if (!$resultado) {
        $mensaje_error = "Error al consultar los estilos: " . mysqli_error($mysqli);
    } else {
        // Metemos los resultados en el array
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $lista_estilos[] = $fila;
        }
        
        // El PDF dice que hay que liberar el resultado
        mysqli_free_result($resultado);
    }
    
    // Cerramos
    mysqli_close($mysqli);
    
} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}

?>

<main>
    <section>
        <h2>Listado de Estilos</h2>
        
        <?php
        if ($mensaje_error != "") {
            echo "<p style='color: red;'>$mensaje_error</p>";
        
        } elseif (count($lista_estilos) == 0) {
            echo "<p>No hay estilos disponibles en la base de datos.</p>";
        
        } else {
            // El PDF dice que cambiarlo es en otra práctica (pág 9) 
            echo "<p>Aquí puedes ver los estilos. En la próxima práctica podrás seleccionar el que quieras.</p>";
            
            echo "<ul>";
            
            // 3. PINTAMOS LA LISTA
            foreach ($lista_estilos as $estilo) {
                echo "<li style='margin-bottom: 15px;'>";
                echo "<strong>Nombre: " . htmlspecialchars($estilo['Nombre']) . "</strong>";
                echo "<p style='margin: 0;'>" . htmlspecialchars($estilo['Descripcion']) . "</p>";
                echo "<em style='font-size: 0.9em;'>(Archivo: " . htmlspecialchars($estilo['Fichero']) . ")</em>";
                echo "</li>";
            }
            
            echo "</ul>";
        }
        ?>
        
    </section>
</main>

<?php
include "footer.php";
ob_end_flush();
?>