<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once "conexion_bd.php";
require_once "validaciones.php";

$titulo_pagina = "Resultado de la operación";
require_once "paginas_Estilo.php";
include "header.php";

$errores = [];
$Titulo = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mysqli = conectarBD();
    
    // Recogemos el ID (0 si es nuevo, >0 si es modificar)
    $id_anuncio = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    // Recogida de datos
    $Titulo = trim($_POST['Titulo'] ?? '');
    $Precio = $_POST['Precio'] ?? '';
    $TAnuncio = $_POST['TAnuncio'] ?? '';
    $TVivienda = $_POST['TVivienda'] ?? '';
    $Pais = $_POST['Pais'] ?? '';
    $Ciudad = trim($_POST['Ciudad'] ?? '');
    $Texto = trim($_POST['Texto'] ?? '');
    
    // Opcionales
    $Superficie = $_POST['Superficie'] ?? null;
    $NHabitaciones = $_POST['NHabitaciones'] ?? null;
    $NBanyos = $_POST['NBanyos'] ?? null;
    $Planta = $_POST['Planta'] ?? null;
    $Anyo = $_POST['Anyo'] ?? null;

    // Ajuste de nulos para la BD
    $sup = ($Superficie !== "") ? $Superficie : null;
    $hab = ($NHabitaciones !== "") ? $NHabitaciones : null;
    $ban = ($NBanyos !== "") ? $NBanyos : null;
    $pla = ($Planta !== "") ? $Planta : null;
    $any = ($Anyo !== "") ? $Anyo : null;

    $errores = validarAnuncio($_POST);//validaciones.php

    if (empty($errores)) {
        
        if ($id_anuncio > 0) {
            // =================================================
            // MODIFICAR
            // =================================================
            
            //verificamos que es el dueño del anuncio
            $stmt_check = $mysqli->prepare("SELECT IdAnuncio FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
            $stmt_check->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
            $stmt_check->execute();
            if (!$stmt_check->fetch()) {
                $errores['General'] = "No tienes permiso para modificar este anuncio o no existe.";
            }
            $stmt_check->close();

            if (empty($errores)) {
                $sql = "UPDATE anuncios SET 
                            Titulo = ?, Precio = ?, Texto = ?, TAnuncio = ?, TVivienda = ?, 
                            Pais = ?, Ciudad = ?, Superficie = ?, NHabitaciones = ?, 
                            NBanyos = ?, Planta = ?, Anyo = ?
                        WHERE IdAnuncio = ? AND Usuario = ?";
                
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sdsiiisdiiiiii", 
                    $Titulo, $Precio, $Texto, $TAnuncio, $TVivienda, $Pais, $Ciudad,
                    $sup, $hab, $ban, $pla, $any, 
                    $id_anuncio, $_SESSION['usuario_id']
                );

                if (!$stmt->execute()) {
                    $errores['BD'] = "Error al actualizar: " . $stmt->error;
                }
                $stmt->close();
            }

        } else {
            // =================================================
            // CREAR
            // =================================================

            $sql = "INSERT INTO anuncios (Titulo, Precio, Texto, TAnuncio, TVivienda, Pais, Ciudad, 
                    Superficie, NHabitaciones, NBanyos, Planta, Anyo, Usuario, FRegistro) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sdsiiisdiiiii", 
                $Titulo, $Precio, $Texto, $TAnuncio, $TVivienda, $Pais, $Ciudad,
                $sup, $hab, $ban, $pla, $any, $_SESSION['usuario_id']
            );

            if (!$stmt->execute()) {
                $errores['BD'] = "Error al insertar: " . $stmt->error;
            }
            $stmt->close();
        }

        // Si no hay errores anuncios_exito.php
        if (empty($errores)) {
            $mysqli->close();
            header("Location: anuncios_exito.php");
            exit;
        }
    }

    $mysqli->close();

} else {
    // Si intentan entrar directamente sin POST
    header("Location: mis_anuncios.php");
    exit;
}
?>

<main>
    <h2>Se han encontrado errores</h2>
    <section>
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="javascript:history.back()" class="boton">Volver al formulario</a>
    </section>
</main>

<?php include "footer.php"; ?>
