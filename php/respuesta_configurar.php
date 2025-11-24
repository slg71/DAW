<?php
session_start();

require_once "conexion_bd.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" || isset($_SESSION['usuario_id'])) {

    $id_usuario = $_SESSION['usuario_id'];
    $id_nuevo_estilo = isset($_POST['estilo']) ? (int)$_POST['estilo'] : 1; //estilo.css
    $nombre_fichero_css = "estilo.css"; // Valor por defecto por seguridad

    // ============ Si todo está bien, actualizar bd ============
    $mysqli = conectarBD(); //de conexion_bd.php

    if ($mysqli) {
        // put en bd
        $sql_update = "UPDATE usuarios SET Estilo = ? WHERE IdUsuario = ?";
        $stmt = mysqli_prepare($mysqli, $sql_update);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $id_nuevo_estilo, $id_usuario);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // obtener nombre del css para actualizar la cookie y la vista actual
        $sql_fichero = "SELECT Fichero FROM estilos WHERE IdEstilo = ?";
        $stmt_f = mysqli_prepare($mysqli, $sql_fichero);
        
        if ($stmt_f) {
            mysqli_stmt_bind_param($stmt_f, "i", $id_nuevo_estilo);
            mysqli_stmt_execute($stmt_f);
            mysqli_stmt_bind_result($stmt_f, $nombre_fichero_css);
            mysqli_stmt_fetch($stmt_f);
            mysqli_stmt_close($stmt_f);
        }

        mysqli_close($mysqli);
    }

    //actualizar cookie y vista
    // 90 dias
    setcookie('estilo', $nombre_fichero_css, time() + (90 * 24 * 60 * 60), '/', '', false, true);
    $_COOKIE['estilo'] = $nombre_fichero_css;

    //mostrar página
    $titulo_pagina = "Configuración modificada";
    include "paginas_Estilo.php"; // Ahora cargará el CSS nuevo
    include "header.php";
    ?>

    <main>
        <section id="bloque">
            <h2>Configuración modificada</h2>
            
            <p>El estilo visual ha sido actualizado correctamente.</p>
            <p>Tus preferencias se han guardado en la base de datos.</p>
            
            <br>
            <a href="inicio_registrado.php">Volver al Inicio</a>
            <a href="configurar.php">Volver a Configuración</a>
        </section>
    </main>

    <?php
    include "footer.php";

} else {
    // Si no es POST, redirigimos (sin cambios)
    header("Location: registro.php");
    exit;
}
?>