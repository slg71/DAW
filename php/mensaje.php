<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: mensaje.php (usuario autenticado)
// -------------------------------------------------------------

// Incluir la conexión a la base de datos
require_once "conexion_bd.php";

$titulo_pagina = "Mensaje (Usuario) - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";

// Conectar a la base de datos
$mysqli = conectarBD();

// Array para almacenar los tipos de mensaje
$tipos_mensaje = array();

if ($mysqli) {
    // Consulta para obtener los tipos de mensaje
    $sentencia = "SELECT IdTMensaje, NomTMensaje FROM TiposMensajes";
    
    if ($resultado = $mysqli->query($sentencia)) {
        // Recorrer los resultados y almacenarlos en el array
        while ($fila = $resultado->fetch_assoc()) {
            $tipos_mensaje[] = $fila;
        }
        
        // Liberar memoria del resultado
        $resultado->close();
    } else {
        echo "<p>Error al obtener los tipos de mensaje: " . $mysqli->error . "</p>";
    }
    
    // Cerrar la conexión
    $mysqli->close();
}
?>
    <main>
        <form action="mensaje_enviado.php" method="post">
            <h2>Enviar mensaje a: usuario_ejemplo</h2>
            
            <label for="tipo_mensaje">Tipo de mensaje:</label>
            <select id="tipo_mensaje" name="tipo_mensaje" required>
                <option value="">Seleccione un tipo de mensaje</option>
                <?php
                // Generar las opciones del select desde la base de datos
                foreach ($tipos_mensaje as $tipo) {
                    echo '<option value="' . $tipo['IdTMensaje'] . '">';
                    echo htmlspecialchars($tipo['NomTMensaje']);
                    echo '</option>';
                }
                ?>
            </select><br><br>

            <label for="mensaje">Escribe tu mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="6" cols="50" 
                      placeholder="Escribe aquí tu mensaje..." 
                      maxlength="4000" required></textarea><br><br>

            <button type="submit">Enviar mensaje</button>
        </form>
    </main>
<?php include "footer.php"; ?>
</body>
</html>