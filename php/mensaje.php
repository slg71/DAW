<?php
ob_start();
require_once "sesion_control.php";

$titulo_pagina = "Redactar Mensaje";
include "paginas_Estilo.php";
include "header.php";
require_once "conexion_bd.php";

// Comprobar usuario
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Recuperar el ID del anuncio al que estamos respondiendo
$id_anuncio = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$titulo_anuncio = "Desconocido";
$id_destinatario = 0;
$nombre_destinatario = "Usuario";

$mysqli = conectarBD();
$error = "";

if ($mysqli) {
    // Necesitamos saber quién es el dueño del anuncio para enviarle el mensaje a él
    $sql_datos = "SELECT A.Titulo, U.IdUsuario, U.NomUsuario 
                  FROM anuncios A 
                  JOIN usuarios U ON A.Usuario = U.IdUsuario 
                  WHERE A.IdAnuncio = ?";
                  
    $stmt = mysqli_prepare($mysqli, $sql_datos);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $tit, $id_dst, $nom_dst);
        
        if (mysqli_stmt_fetch($stmt)) {
            $titulo_anuncio = $tit;
            $id_destinatario = $id_dst;
            $nombre_destinatario = $nom_dst;
        } else {
            $error = "El anuncio no existe o ha sido borrado.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // También cargamos los tipos de mensaje para el select
    $tipos = [];
    $sql_tipos = "SELECT IdTMensaje, NomTMensaje FROM tiposmensajes";
    $res_tipos = mysqli_query($mysqli, $sql_tipos);
    if ($res_tipos) {
        while($fila = mysqli_fetch_assoc($res_tipos)) {
            $tipos[] = $fila;
        }
    }
    
    mysqli_close($mysqli);
} else {
    $error = "Error de conexión.";
}
?>

<main>
    <section id="bloque">
        <h2>Contactar con el anunciante</h2>
        
        <?php if ($error): ?>
            <p class="error-campo"><?php echo $error; ?></p>
            <a href="index.php">Volver</a>
        <?php else: ?>
        
            <p>Vas a enviar un mensaje a <strong><?php echo htmlspecialchars($nombre_destinatario); ?></strong></p>
            <p>Referente al anuncio: <em><?php echo htmlspecialchars($titulo_anuncio); ?></em></p>

            <form action="mensaje_enviado.php" method="POST">
                <input type="hidden" name="id_anuncio" value="<?php echo $id_anuncio; ?>">
                <input type="hidden" name="id_destinatario" value="<?php echo $id_destinatario; ?>">
                
                <label for="tipo">Motivo del mensaje:</label>
                <select name="tipo" id="tipo" required>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?php echo $t['IdTMensaje']; ?>">
                            <?php echo htmlspecialchars($t['NomTMensaje']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="texto">Tu mensaje:</label>
                <textarea name="texto" id="texto" rows="5" required placeholder="Escribe aquí tu consulta..."></textarea>
                
                <button type="submit">Enviar Mensaje</button>
            </form>
            
        <?php endif; ?>
    </section>
</main>

<?php
include "footer.php";
ob_end_flush();
?>