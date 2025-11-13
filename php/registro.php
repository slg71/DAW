<?php
// 1. Inicia la sesión
session_start();

// 2. Recuperamos los "flash data"
$flash_data = $_SESSION['_flash'] ?? [];
unset($_SESSION['_flash']);

// 3. Extraemos los datos
$errores = $flash_data['errors'] ?? [];
$datos_previos = $flash_data['old_input'] ?? [];

// 4. Asignamos los valores a variables
$valor_usuario = $datos_previos["usuario"] ?? "";
$valor_email   = $datos_previos["email"] ?? "";
$valor_sexo    = $datos_previos["sexo"] ?? "";
$valor_pais_id = $datos_previos["pais"] ?? ""; // El ID del país
$valor_ciudad  = $datos_previos["ciudad"] ?? "";
$valor_nac     = $datos_previos["nac"] ?? "";

// 5. Incluir la conexión a la base de datos
require_once "conexion_bd.php";

$titulo_pagina = "Registro";
include "paginas_Estilo.php";
include "header.php";

// Conectar a la base de datos
$mysqli = conectarBD();

// Array para almacenar los paises
$paises = array();

if ($mysqli) {
    // Consulta para obtener los paises
    $sentencia = "SELECT IdPais, NomPais FROM Paises";
    
    if ($resultado = $mysqli->query($sentencia)) {
        while ($fila = $resultado->fetch_assoc()) {
            $paises[] = $fila;
        }
        $resultado->close();
    } else {
        echo "<p>Error al obtener los paises: " . $mysqli->error . "</p>";
    }
    
    $mysqli->close();
}

// Definir las variables que 'formulario_comun.php' espera

$titulo_formulario = "Registro";
$action_url = "respuesta_registro.php";
$es_registro = true;
$desactivado = ""; // En registro, no se desactiva nada

// 6. Incluir el formulario común
include "formulario_comun.php";

include "footer.php";
?>
</body>
</html>