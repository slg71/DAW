<?php
require_once "sesion_control.php";

// -------------------------------------------------------------
// Página: mensaje_enviado.php
// -------------------------------------------------------------

// Comprobar si se ha enviado el formulario por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Incluir la conexión a la base de datos
    require_once "conexion_bd.php";

    // Recoger y limpiar valores
    $tipo_mensaje_id = trim($_POST["tipo_mensaje"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    // Validación básica
    if ($tipo_mensaje_id == "" || $mensaje == "") {
        // Si hay error, redirigir de vuelta al formulario original
        header("Location: mensaje.php?error=empty");
        exit;
    }

    // Validar que el tipo de mensaje sea un número entero
    if (!filter_var($tipo_mensaje_id, FILTER_VALIDATE_INT)) {
        header("Location: mensaje.php?error=invalid");
        exit;
    }

    // Validar longitud máxima del mensaje
    if (strlen($mensaje) > 4000) {
        header("Location: mensaje.php?error=toolong");
        exit;
    }

    // Conectar a la base de datos
    $mysqli = conectarBD();
    
    $nombre_tipo_mensaje = "";
    
    if ($mysqli) {
        // Verificar que el tipo de mensaje existe en la base de datos
        $sentencia = "SELECT NomTMensaje FROM TiposMensajes WHERE IdTMensaje = ?";
        
        if ($stmt = $mysqli->prepare($sentencia)) {
            // Vincular parámetros
            $stmt->bind_param("i", $tipo_mensaje_id);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Vincular resultado
            $stmt->bind_result($nombre_tipo_mensaje);
            
            // Obtener el resultado
            if (!$stmt->fetch()) {
                // Si el tipo de mensaje no existe
                $stmt->close();
                $mysqli->close();
                header("Location: mensaje.php?error=invalid_type");
                exit;
            }
            
            $stmt->close();
        }
        
        $mysqli->close();
    } else {
        // Error de conexión
        header("Location: mensaje.php?error=db");
        exit;
    }

    $title = "Mensaje enviado al anunciante";
    include "paginas_Estilo.php";
    include "header.php";
    ?>

    <main id="bloque">
        <h1>¡Mensaje enviado con éxito!</h1>
        <section>
            <h2>Detalles del mensaje</h2>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($nombre_tipo_mensaje); ?></p>
            <p><strong>Mensaje:</strong> <?php echo nl2br(htmlspecialchars($mensaje)); ?></p>
            <p><a href="mismensajes.php">Ver mis mensajes</a></p>
        </section>
    </main>

    <?php
    include "footer.php";

} else {
    // Si se accede directamente sin enviar el formulario
    header("Location: mensaje.php");
    exit;
}
?>