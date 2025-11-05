<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: mismensajes.php (usuario autenticado simulado)
// -------------------------------------------------------------

$titulo_pagina = "Mis Mensajes (Usuario) - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";
?>
    <main>
        <seccion id="mismensajes">
            <h2>Mis mensajes</h2>

            <section id="Enviados">
                <h3 class="tipo_mensaje">Enviados</h3>
                <article>
                    <p class="mensaje">Gracias por tu interés, podemos concretar visita.</p>
                    <p class="detalle">2025-09-29 · yo_mismo</p>
                </article>
            </section>

            <section id="Recibidos">
                <h3 class="tipo_mensaje">Recibidos</h3>
                <article>
                    <p class="mensaje">Hola, estoy interesado en tu anuncio del piso.</p>
                    <p class="detalle">2025-09-28 · usuario123</p>
                </article>
                <article>
                    <p class="mensaje">¿Se puede negociar el precio?</p>
                    <p class="detalle">2025-09-30 · comprador45</p>
                </article>
            </section>
        </seccion>
    </main>

<?php include "footer.php"; ?>
</body>
</html>
