<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: mensaje.php (usuario autenticado simulado)
// -------------------------------------------------------------

$titulo_pagina = "Mensaje (Usuario) - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";


?>
    <main>
        <form action="mensaje_enviado.php" method="post">
            <h2>Enviar mensaje a: usuario_ejemplo</h2>
            <label for="tipo_mensaje">Tipo de mensaje:</label>
            <select id="tipo_mensaje" name="tipo_mensaje">
                <option value="informacion">Más información</option>
                <option value="cita">Solicitar una cita</option>
                <option value="oferta">Comunicar una oferta</option>
            </select><br><br>

            <label for="mensaje">Escribe tu mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="6" cols="50" placeholder="Escribe aquí tu mensaje..."></textarea><br><br>

            <button type="submit">Enviar mensaje</button>
        </form>
    </main>
<?php include "footer.php"; ?>
</body>
</html>
