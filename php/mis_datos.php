<?php
ob_start();

require_once "sesion_control.php";

// Recuperar datos flash si hubo error al guardar
$flash_data = $_SESSION['_flash'] ?? [];
unset($_SESSION['_flash']);

$errores = $flash_data['errors'] ?? [];
$datos_previos = $flash_data['old_input'] ?? [];

$titulo_pagina = "Mis Datos"; 
include "paginas_Estilo.php";
include "header.php";

// 1. AÑADIMOS LA CONEXIÓN
include "conexion_bd.php"; 

$datos_usuario = null;
$paises = [];
$mensaje_error = "";

$id_usuario_actual = (int)$_SESSION['usuario_id'];

// 2. CONECTAR Y CONSULTAR
$mysqli = conectarBD();
if ($mysqli) {
    
    // 3. Obtener datos del usuario si no hay datos previos de un error
    if (empty($datos_previos)) {
        $sql_usuario = "SELECT NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais 
                        FROM usuarios WHERE IdUsuario = ?";
        $stmt = mysqli_prepare($mysqli, $sql_usuario);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario_actual);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $valor_usuario = $row['NomUsuario'];
                $valor_email   = $row['Email'];
                $valor_nac     = $row['FNacimiento'];
                $valor_ciudad  = $row['Ciudad'];
                $valor_pais_id = $row['Pais'];
                // Convertir sexo numérico a texto para el select
                $valor_sexo = ($row['Sexo'] == 1) ? 'hombre' : (($row['Sexo'] == 0) ? 'mujer' : 'otro');
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        // Si volvemos de un error, rellenamos con lo que el usuario escribió
        $valor_usuario = $datos_previos['usuario'];
        $valor_email   = $datos_previos['email'];
        $valor_sexo    = $datos_previos['sexo'];
        $valor_nac     = $datos_previos['nac'];
        $valor_ciudad  = $datos_previos['ciudad'];
        $valor_pais_id = $datos_previos['pais'];
    }

    // 2. Cargar lista de países
    $sql_paises = "SELECT IdPais, NomPais FROM paises ORDER BY NomPais";
    $resultado_paises = mysqli_query($mysqli, $sql_paises);
    while ($fila = mysqli_fetch_assoc($resultado_paises)) {
        $paises[] = $fila;
    }
    mysqli_close($mysqli);

} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}

if ($mensaje_error) {
    echo "<main><p class='error-campo'>$mensaje_error</p></main>";
} else {
    // Configuración para formulario_comun.php
    $titulo_formulario = "Modificar Mis Datos";
    $action_url = "respuesta_mis_datos.php"; // Página nueva que crearemos
    $es_registro = false; // Indica que NO es registro nuevo
    $desactivado = ""; // Habilitamos los campos para editar
    
    // Feedback visual de éxito
    if (isset($_GET['ok'])) {
        echo "<main id='bloque'><p style='color:white; font-weight:bold;'>¡Datos actualizados correctamente!</p></main>";
    }

    include "formulario_comun.php";
}

include "footer.php";
ob_end_flush();
?>