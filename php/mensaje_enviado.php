<?php
// -------------------------------------------------------------
// Página: mensaje_enviado.php
// -------------------------------------------------------------


// Comprobar si se ha enviado el formulario por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger y limpiar valores
    $tipo_mensaje = trim($_POST["tipo_mensaje"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    // Tipos válidos
    $tipos_validos = ["informacion", "cita", "oferta"];

    // Validación básica
    if (!in_array($tipo_mensaje, $tipos_validos) || $mensaje == "") {
        // Si hay error, redirigir de vuelta al formulario original
        header("Location: ../mensaje.html?error=empty");
        exit;
    }

    $title = "Mensaje enviado al anunciante";
    include = "paginas_Estilo.php";
    include "header.php";
    ?>

    <main id="bloque">
        <h1>¡Mensaje enviado con éxito!</h1>
        <section>
            <h2>Detalles del mensaje</h2>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($tipo_mensaje); ?></p>
            <p><strong>Mensaje:</strong> <?php echo htmlspecialchars($mensaje); ?></p>
            <p><a href="../mismensajes.html">Ver mis mensajes</a></p>
        </section>
    </main>

    <?php
    include "footer.php";

} else {
    // Si se accede directamente sin enviar el formulario
    header("Location: ../mensaje.html");
    exit;
}
?>
