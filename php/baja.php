<?php
ob_start();
require_once "sesion_control.php";

$titulo_pagina = "Darme de baja";
include "paginas_Estilo.php";
include "header.php";
require_once "conexion_bd.php";

// Comprobar que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$mysqli = conectarBD();

$html_listado = "";
$total_anuncios = 0;
$total_fotos = 0;

if ($mysqli) {
    // Consulta: traer todos los anuncios y luego contar fotos uno a uno
    $sql = "SELECT IdAnuncio, Titulo FROM anuncios WHERE Usuario = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        
        // Almacenamos el resultado para poder anidar consultas
        mysqli_stmt_store_result($stmt);
        
        mysqli_stmt_bind_result($stmt, $id_anuncio, $titulo_anuncio);
        
        $html_listado .= "<table>";
        $html_listado .= "<tr><th>Título Anuncio</th><th>Nº Fotos</th></tr>";
        
        // Recorremos los resultados
        while (mysqli_stmt_fetch($stmt)) {
            
            // Usamos una conexion NUEVA para evitar conflictos con la consulta principal
            $link_aux = conectarBD(); 
            
            $sql_fotos = "SELECT COUNT(*) as num FROM fotos WHERE Anuncio = $id_anuncio";
            $res_fotos = mysqli_query($link_aux, $sql_fotos);
            
            $num_fotos = 0;
            if ($res_fotos && $fila_fotos = mysqli_fetch_assoc($res_fotos)) {
                $num_fotos = $fila_fotos['num'];
            }
            
            // La foto principal cuenta como 1 extra
            $num_fotos++; 
            
            mysqli_close($link_aux); // Cerramos la conexion auxiliar
            
            // Sumamos totales
            $total_anuncios++;
            $total_fotos += $num_fotos;
            
            // Rellenamos la fila
            $html_listado .= "<tr>";
            $html_listado .= "<td>" . htmlspecialchars($titulo_anuncio) . "</td>";
            $html_listado .= "<td>" . $num_fotos . "</td>";
            $html_listado .= "</tr>";
        }
        $html_listado .= "</table>";
        
        mysqli_stmt_close($stmt);
    }
    mysqli_close($mysqli);
} else {
    echo "<p class='error-campo'>Error de conexión a la base de datos.</p>";
}
?>

<main>
    <section id="bloque">
        <h2>Confirmar Baja del Servicio</h2>
        <p>Lamentamos que quieras marcharte. Por favor, revisa la información que se eliminará permanentemente.</p>
        
        <article>
            <h3>Resumen de datos a eliminar</h3>
            
            <?php if ($total_anuncios > 0): ?>
                <?php echo $html_listado; ?>
            <?php else: ?>
                <p>No tienes anuncios publicados.</p>
            <?php endif; ?>
            
            <br>
            
            <p><strong>Total Anuncios:</strong> <?php echo $total_anuncios; ?></p>
            <p><strong>Total Fotos:</strong> <?php echo $total_fotos; ?></p>
            <p><em>* También se borrarán todos tus mensajes enviados y recibidos.</em></p>
        </article>

        <form action="respuesta_baja.php" method="POST">
            <p class="error-campo">Esta acción no se puede deshacer.</p>
            
            <label for="pwd_confirm">Introduce tu contraseña actual para confirmar:</label>
            <input type="password" id="pwd_confirm" name="pwd_confirm" required>
            
            <button type="submit" name="confirmar_baja">Eliminar cuenta definitivamente</button>
        </form>
        
        <br>
        <button onclick="window.location.href='MenuRegistradoUsu.php'">Cancelar y Volver</button>
    </section>
</main>

<?php
include "footer.php";
ob_end_flush();
?>