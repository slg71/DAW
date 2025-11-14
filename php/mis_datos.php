<?php
ob_start();

require_once "sesion_control.php";

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
    
    // 3. CONSULTA 1: Pillar los datos del usuario actual
    $sql_usuario = "SELECT NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais 
                    FROM usuarios 
                    WHERE IdUsuario = ?";
    
    $stmt_usuario = mysqli_prepare($mysqli, $sql_usuario);
    
    if ($stmt_usuario) {
        mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario_actual);
        mysqli_stmt_execute($stmt_usuario);
        
        $resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
        
        if ($resultado_usuario && mysqli_num_rows($resultado_usuario) == 1) {
            $datos_usuario = mysqli_fetch_assoc($resultado_usuario);
        } else {
            $mensaje_error = "Error: No se pudieron encontrar tus datos.";
        }
        mysqli_stmt_close($stmt_usuario);

    } else {
        $mensaje_error = "Error al preparar la consulta de usuario.";
    }

    // 4. CONSULTA 2: Pillar la lista de países para el <select>
    $sql_paises = "SELECT IdPais, NomPais FROM paises ORDER BY NomPais";
    $resultado_paises = mysqli_query($mysqli, $sql_paises);
    
    if ($resultado_paises) {
        while ($fila_pais = mysqli_fetch_assoc($resultado_paises)) {
            $paises[] = $fila_pais; // Rellenamos $paises
        }
        mysqli_free_result($resultado_paises);
    } else {
        $mensaje_error .= " Error al cargar la lista de países.";
    }

    mysqli_close($mysqli);

} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}

if ($mensaje_error != ""): ?>
    <main>
        <p class="error-campo"><?php echo $mensaje_error; ?></p>
    </main>
<?php elseif ($datos_usuario): 
    
    // Definir las variables que 'formulario_comun.php' espera
    $titulo_formulario = "Mis Datos de Registro";
    $action_url = ""; // El form original no tenía action
    $es_registro = false;
    $desactivado = "disabled"; // En "mis datos" todo está deshabilitado
    $errores = []; // No hay errores aquí

    // Mapear los datos de la BD a los nombres de variables del formulario
    $valor_usuario = $datos_usuario['NomUsuario'] ?? "";
    $valor_email   = $datos_usuario['Email'] ?? "";
    $valor_nac     = $datos_usuario['FNacimiento'] ?? "";
    $valor_ciudad  = $datos_usuario['Ciudad'] ?? "";
    $valor_pais_id = $datos_usuario['Pais'] ?? ""; // Es el IdPais

    // El formulario usa "hombre"/"mujer", pero la BD usa 1/0. Hay que convertirlo.
    $valor_sexo = ""; // Valor por defecto
    if (isset($datos_usuario['Sexo'])) {
        if ($datos_usuario['Sexo'] == 1) {
            $valor_sexo = "hombre";
        } elseif ($datos_usuario['Sexo'] == 0) {
            $valor_sexo = "mujer";
        }
    }

    // 5. Incluir el formulario común
    include "formulario_comun.php";

endif; 

include "footer.php";
ob_end_flush();
?>