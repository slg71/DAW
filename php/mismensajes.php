<?php
require_once "sesion_control.php";

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
                $sentencia_sql = "SELECT m.Texto, m.FRegistro, u.NomUsuario, tm.NomTMensaje, a.Titulo
                    FROM Mensajes m
                    JOIN Usuarios u ON m.UsuDestino = u.IdUsuario
                    JOIN TiposMensajes tm ON m.TMensaje = tm.IdTMensaje
                    JOIN Anuncios a ON m.Anuncio = a.IdAnuncio
                    WHERE m.UsuOrigen = ?
                    ORDER BY m.FRegistro DESC";
                $sentencia = $mysqli->prepare($sentencia_sql);
                $sentencia->bind_param("i", $usuario_id); // "i" porque IdUsuario es un entero
                $sentencia->execute();
                $resultado = $sentencia->get_result();
                                
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
                $sentencia->close();
                ?>
            </section>

            <section id="Recibidos">
                <h3 class="tipo_mensaje">Mensajes Recibidos</h3>
                <?php
                // Consulta para obtener los mensajes RECIBIDOS por el usuario
                $sentencia_sql = "SELECT m.Texto, m.FRegistro, u.NomUsuario, tm.NomTMensaje, a.Titulo
                    FROM Mensajes m
                    JOIN Usuarios u ON m.UsuOrigen = u.IdUsuario
                    JOIN TiposMensajes tm ON m.TMensaje = tm.IdTMensaje
                    JOIN Anuncios a ON m.Anuncio = a.IdAnuncio
                    WHERE m.UsuDestino = ?
                    ORDER BY m.FRegistro DESC";
                $sentencia = $mysqli->prepare($sentencia_sql);
                $sentencia->bind_param("i", $usuario_id);
                $sentencia->execute();
                $resultado = $sentencia->get_result();
                            
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
                $sentencia->close();
                
                // Cerrar la conexión
                $mysqli->close();
                ?>
            </section>
        </section>
    </main>

<?php include "footer.php"; ?>
</body>
</html>