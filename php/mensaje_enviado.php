<?php
session_start();
require_once "conexion_bd.php";

// Control de acceso básico
if (!isset($_SESSION['usuario_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit;
}

$titulo_pagina = "Mensaje Enviado";
include "paginas_Estilo.php";
include "header.php";

// Recogemos datos del formulario
$id_origen = $_SESSION['usuario_id'];
$id_destino = isset($_POST['id_destinatario']) ? (int)$_POST['id_destinatario'] : 0;
$id_anuncio = isset($_POST['id_anuncio']) ? (int)$_POST['id_anuncio'] : 0;
$tipo_msg = isset($_POST['tipo']) ? (int)$_POST['tipo'] : 1;
$texto = isset($_POST['texto']) ? trim($_POST['texto']) : "";

$exito = false;
$mensaje_resultado = "";

if ($texto == "" || $id_destino == 0) {
    $mensaje_resultado = "Error: Faltan datos obligatorios o el destinatario no es válido.";
} else {
    $mysqli = conectarBD();
    
    if ($mysqli) {
        //IdMensaje, TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro
        // FRegistro se pone solo (default current_timestamp)
        
        $sql = "INSERT INTO mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($mysqli, $sql);
        
        if ($stmt) {
            // Bind de parámetros: i (int), s (string), i (int), i (int), i (int) -> "isiii"
            mysqli_stmt_bind_param($stmt, "isiii", $tipo_msg, $texto, $id_anuncio, $id_origen, $id_destino);
            
            if (mysqli_stmt_execute($stmt)) {
                $exito = true;
            } else {
                $mensaje_resultado = "Error al insertar en la BD: " . mysqli_error($mysqli);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $mensaje_resultado = "Error en la preparación de la sentencia.";
        }
        
        mysqli_close($mysqli);
    } else {
        $mensaje_resultado = "No se pudo conectar a la base de datos.";
    }
}
?>

<main>
    <section id="bloque">
        <?php if ($exito): ?>
            <h2>¡Mensaje enviado con éxito!</h2>
            <p>Tu mensaje ha sido entregado correctamente al anunciante.</p>
            <p>Tipo de mensaje: <?php echo $tipo_msg; ?></p>
            <p>Texto: "<?php echo htmlspecialchars($texto); ?>"</p>
            
            <br>
            <a href="mismensajes.php">Ir a mis mensajes</a>
            <br>
            <a href="ver_anuncio.php?id=<?php echo $id_anuncio; ?>">Volver al anuncio</a>
            
        <?php else: ?>
            <h2>Error al enviar</h2>
            <p class="error-campo"><?php echo $mensaje_resultado; ?></p>
            <br>
            <button onclick="window.history.back()">Volver a intentar</button>
        <?php endif; ?>
    </section>
</main>

<?php
include "footer.php";
?>