<?php
session_start();
require_once 'conexion_bd.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mysqli = conectarBD();
$usuario_actual = $_SESSION['usuario_id']; // UsuOrigen
$error = "";

// ---------------------------------------------------------
// PPROCESO DE ENVIO
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $id_anuncio = isset($_POST['id_anuncio']) ? intval($_POST['id_anuncio']) : 0;
    $id_tipo    = isset($_POST['tipo_mensaje']) ? intval($_POST['tipo_mensaje']) : 0;
    $texto      = isset($_POST['texto']) ? trim($_POST['texto']) : "";

    if ($id_anuncio > 0 && $id_tipo > 0 && !empty($texto)) {
        
        // Buscar a destinatario (dueño del anuncio) esto va en 'usudestino'
        $stmt_owner = $mysqli->prepare("SELECT Usuario FROM anuncios WHERE IdAnuncio = ?");
        $stmt_owner->bind_param("i", $id_anuncio);
        $stmt_owner->execute();
        $stmt_owner->bind_result($id_dueno);
        $existe_anuncio = $stmt_owner->fetch();
        $stmt_owner->close();

        if ($existe_anuncio) {
            // Automensaje jaja
            if ($id_dueno == $usuario_actual) {
                $error = "No puedes enviarte mensajes a tu propio anuncio.";
            } else {
                // Insertar mensaje en la BD
                $sql_insert = "INSERT INTO mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino) VALUES (?, ?, ?, ?, ?)";
                $stmt_ins = $mysqli->prepare($sql_insert);
                
                if ($stmt_ins) {
                    $stmt_ins->bind_param("isiii", $id_tipo, $texto, $id_anuncio, $usuario_actual, $id_dueno);
                    
                    if ($stmt_ins->execute()) {
                        header("Location: mismensajes.php");//redirigir a mensajes enviados
                        exit;
                    } else {
                        $error = "Error al guardar en base de datos: " . $mysqli->error;
                    }
                    $stmt_ins->close();
                }
            }
        } else {
            $error = "El anuncio no existe.";
        }
    } else {
        $error = "Por favor, rellena todos los campos.";
    }
}

// ---------------------------------------------------------
// PREPARACIÓN DE LA VISTA
// ---------------------------------------------------------

// Recuperar ID del anuncio
$id_anuncio_view = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : (isset($_POST['id_anuncio']) ? intval($_POST['id_anuncio']) : 0);

if ($id_anuncio_view == 0) {
    header("Location: index.php"); 
    exit;
}

// obtener itutlo del anuncio para mostrarlo en el header del formulario
$titulo_anuncio = "Anuncio desconocido";
$stmt_titulo = $mysqli->prepare("SELECT Titulo FROM anuncios WHERE IdAnuncio = ?");
$stmt_titulo->bind_param("i", $id_anuncio_view);
$stmt_titulo->execute();
$stmt_titulo->bind_result($titulo_bd);
if ($stmt_titulo->fetch()) {
    $titulo_anuncio = $titulo_bd;
}
$stmt_titulo->close();

// Obtener los tipos de mensaje para el select
$tipos_options = [];
$res_tipos = $mysqli->query("SELECT IdTMensaje, NomTMensaje FROM tiposmensajes");
if ($res_tipos) {
    while ($row = $res_tipos->fetch_assoc()) {
        $tipos_options[] = $row;
    }
}

// ---------------------------------------------------------
// ENVIAR MENSAJE HTML
// ---------------------------------------------------------
$titulo_pagina = "Enviar Mensaje";
require_once 'paginas_Estilo.php';
require_once 'header.php';
?>

<main>
    <h2>Contactar con el anunciante</h2>

    <section id="formulario-mensaje" style="max-width: 600px; margin: 0 auto;">
        
        <p>Estás enviando un mensaje sobre: <strong><?php echo htmlspecialchars($titulo_anuncio); ?></strong></p>

        <?php if ($error): ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form action="enviar_mensaje.php" method="POST">
            <input type="hidden" name="id_anuncio" value="<?php echo $id_anuncio_view; ?>">

            <p>
                <label for="tipo_mensaje"><strong>Motivo de la consulta:</strong></label><br>
                <select name="tipo_mensaje" id="tipo_mensaje" required>
                    <?php foreach ($tipos_options as $tipo): ?>
                        <option value="<?php echo $tipo['IdTMensaje']; ?>">
                            <?php echo htmlspecialchars($tipo['NomTMensaje']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="texto"><strong>Mensaje:</strong></label><br>
                <textarea name="texto" id="texto" rows="6" required  placeholder="Hola, estoy interesado en este inmueble..."></textarea>
            </p>

            <p style="text-align: center;">
                <button type="submit" class="boton">Enviar Mensaje</button>
                
                <a href="ver_anuncio.php?id=<?php echo $id_anuncio_view; ?>" class="boton">
                    Cancelar
                </a>
            </p>
        </form>

    </section>
</main>

<?php 
if (file_exists('footer.php')) include 'footer.php'; 
?>