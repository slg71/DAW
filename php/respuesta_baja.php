<?php
session_start();
require_once "conexion_bd.php";

// si no hay sesión o no viene del formulario, fuera
if (!isset($_SESSION['usuario_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit;
}

$titulo_pagina = "Baja Procesada";
include "paginas_Estilo.php";
include "header.php";

$pwd_confirm = $_POST['pwd_confirm'] ?? '';
$id_usuario = $_SESSION['usuario_id'];
$borrado_ok = false;
$mensaje_error = "";

$mysqli = conectarBD();

if ($mysqli) {
    //Validar la contraseña actual antes de borrar nada
    $sql_user = "SELECT Clave FROM usuarios WHERE IdUsuario = ?";
    $stmt = mysqli_prepare($mysqli, $sql_user);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hash_guardado);
    
    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt); //Cerramos para poder ejecutar otras queries
        
        if (password_verify($pwd_confirm, $hash_guardado)) {
            //Borrar en orden para no romper Foreign Keys
            
            //Borrar MENSAJES (Donde soy origen o destino)
            $sql_borrar_msgs = "DELETE FROM mensajes WHERE UsuOrigen = ? OR UsuDestino = ?";
            $stmt_m = mysqli_prepare($mysqli, $sql_borrar_msgs);
            mysqli_stmt_bind_param($stmt_m, "ii", $id_usuario, $id_usuario);
            mysqli_stmt_execute($stmt_m);
            mysqli_stmt_close($stmt_m);
            
            //Borrar FOTOS de mis anuncios
            $sql_borrar_fotos = "DELETE FROM fotos WHERE Anuncio IN (SELECT IdAnuncio FROM anuncios WHERE Usuario = ?)";
            $stmt_f = mysqli_prepare($mysqli, $sql_borrar_fotos);
            mysqli_stmt_bind_param($stmt_f, "i", $id_usuario);
            mysqli_stmt_execute($stmt_f);
            mysqli_stmt_close($stmt_f);

            //Borrar SOLICITUDES de folletos asociadas a mis anuncios
            $sql_borrar_soli = "DELETE FROM solicitudes WHERE Anuncio IN (SELECT IdAnuncio FROM anuncios WHERE Usuario = ?)";
            $stmt_s = mysqli_prepare($mysqli, $sql_borrar_soli);
            mysqli_stmt_bind_param($stmt_s, "i", $id_usuario);
            mysqli_stmt_execute($stmt_s);
            mysqli_stmt_close($stmt_s);
            
            //Borrar mis ANUNCIOS
            $sql_borrar_anuncios = "DELETE FROM anuncios WHERE Usuario = ?";
            $stmt_a = mysqli_prepare($mysqli, $sql_borrar_anuncios);
            mysqli_stmt_bind_param($stmt_a, "i", $id_usuario);
            mysqli_stmt_execute($stmt_a);
            mysqli_stmt_close($stmt_a);
            
            //borrar el USUARIO
            $sql_borrar_yo = "DELETE FROM usuarios WHERE IdUsuario = ?";
            $stmt_u = mysqli_prepare($mysqli, $sql_borrar_yo);
            mysqli_stmt_bind_param($stmt_u, "i", $id_usuario);
            
            if (mysqli_stmt_execute($stmt_u)) {
                $borrado_ok = true;
                
                // Destruir sesión y borrar cookies
                $_SESSION = array();
                if (isset($_COOKIE[session_name()])) {
                    setcookie(session_name(), '', time()-42000, '/');
                }
                // Borrar cookies de "recordarme" si existen
                if (isset($_COOKIE['recordar_usuario'])) setcookie('recordar_usuario', '', time()-3600, '/');
                if (isset($_COOKIE['recordar_pass'])) setcookie('recordar_pass', '', time()-3600, '/');
                
                session_destroy();
            } else {
                $mensaje_error = "Error SQL al borrar usuario: " . mysqli_error($mysqli);
            }
            mysqli_stmt_close($stmt_u);
            
        } else {
            $mensaje_error = "La contraseña introducida no es correcta.";
        }
    } else {
        $mensaje_error = "Usuario no encontrado en la base de datos.";
        mysqli_stmt_close($stmt);
    }
    mysqli_close($mysqli);
} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}
?>

<main>
    <section id="bloque">
        <?php if ($borrado_ok): ?>
            <h2>Borrado realizado</h2>
            <p>Tu cuenta y todos tus datos han sido eliminados correctamente de nuestro sistema.</p>
            <p>Esperamos verte pronto de nuevo.</p>
            <br>
            <a href="index.php">Ir a la Página Principal</a>
        <?php else: ?>
            <h2>Error en la baja</h2>
            <p class="error-campo"><?php echo $mensaje_error; ?></p>
            <br>
            <a href="baja.php">Volver a intentar</a>
        <?php endif; ?>
    </section>
</main>

<?php
include "footer.php";
?>