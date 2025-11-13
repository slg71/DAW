<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: mismensajes.php (usuario autenticado)
// -------------------------------------------------------------

// Incluir la conexión a la base de datos
require_once "conexion_bd.php";

$titulo_pagina = "Mis Mensajes (Usuario) - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";

// Obtener el ID del usuario actual de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Conectar a la base de datos
$mysqli = conectarBD();

if (!$mysqli) {
    echo "<p>Error al conectar con la base de datos.</p>";
    exit;
}
?>
    <main>
        <section id="mismensajes">
            <h2>Mis mensajes</h2>

            <section id="Enviados">
                <h3 class="tipo_mensaje">Mensajes Enviados</h3>
                <?php
                // Consulta para obtener los mensajes ENVIADOS por el usuario
                $sentencia = "SELECT m.Texto, m.FRegistro, u.NomUsuario, tm.NomTMensaje, a.Titulo
                              FROM Mensajes m, Usuarios u, TiposMensajes tm, Anuncios a
                              WHERE m.UsuOrigen = '$usuario_id' 
                              AND m.UsuDestino = u.IdUsuario 
                              AND m.TMensaje = tm.IdTMensaje
                              AND m.Anuncio = a.IdAnuncio
                              ORDER BY m.FRegistro DESC";
                
                $resultado = $mysqli->query($sentencia);
                
                if (!$resultado) {
                    echo "<p>Error al obtener mensajes enviados: " . $mysqli->error . "</p>";
                } else {
                    $total_enviados = $resultado->num_rows;
                    echo "<p><strong>Total: $total_enviados mensajes</strong></p>";
                    
                    if ($total_enviados > 0) {
                        // Recorrer el resultado y mostrar cada mensaje
                        while ($fila = $resultado->fetch_assoc()) {
                            echo '<article>';
                            echo '<p class="tipo"><strong>' . $fila['NomTMensaje'] . '</strong></p>';
                            echo '<p class="anuncio">Anuncio: ' . $fila['Titulo'] . '</p>';
                            echo '<p class="mensaje">' . $fila['Texto'] . '</p>';
                            echo '<p class="detalle">' . $fila['FRegistro'] . ' · Para: ' . $fila['NomUsuario'] . '</p>';
                            echo '</article>';
                        }
                        
                        // Liberar memoria
                        $resultado->free();
                    } else {
                        echo "<p>No has enviado ningún mensaje todavía.</p>";
                    }
                }
                ?>
            </section>

            <section id="Recibidos">
                <h3 class="tipo_mensaje">Mensajes Recibidos</h3>
                <?php
                // Consulta para obtener los mensajes RECIBIDOS por el usuario
                $sentencia = "SELECT m.Texto, m.FRegistro, u.NomUsuario, tm.NomTMensaje, a.Titulo
                              FROM Mensajes m, Usuarios u, TiposMensajes tm, Anuncios a
                              WHERE m.UsuDestino = '$usuario_id' 
                              AND m.UsuOrigen = u.IdUsuario 
                              AND m.TMensaje = tm.IdTMensaje
                              AND m.Anuncio = a.IdAnuncio
                              ORDER BY m.FRegistro DESC";
                
                $resultado = $mysqli->query($sentencia);
                
                if (!$resultado) {
                    echo "<p>Error al obtener mensajes recibidos: " . $mysqli->error . "</p>";
                } else {
                    $total_recibidos = $resultado->num_rows;
                    echo "<p><strong>Total: $total_recibidos mensajes</strong></p>";
                    
                    if ($total_recibidos > 0) {
                        // Recorrer el resultado y mostrar cada mensaje
                        while ($fila = $resultado->fetch_assoc()) {
                            echo '<article>';
                            echo '<p class="tipo"><strong>' . $fila['NomTMensaje'] . '</strong></p>';
                            echo '<p class="anuncio">Anuncio: ' . $fila['Titulo'] . '</p>';
                            echo '<p class="mensaje">' . $fila['Texto'] . '</p>';
                            echo '<p class="detalle">' . $fila['FRegistro'] . ' · De: ' . $fila['NomUsuario'] . '</p>';
                            echo '</article>';
                        }
                        
                        // Liberar memoria
                        $resultado->free();
                    } else {
                        echo "<p>No has recibido ningún mensaje todavía.</p>";
                    }
                }
                
                // Cerrar la conexión
                $mysqli->close();
                ?>
            </section>
        </section>
    </main>

<?php include "footer.php"; ?>
</body>
</html>