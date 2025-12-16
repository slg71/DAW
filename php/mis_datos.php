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

// 1. AÑADIMOS LAS FUNCIONES Y CONEXIÓN
include "conexion_bd.php"; 
include "funciones_anuncios.php";
include "funciones_imagenes.php";

$datos_usuario = null;
$paises = [];
$mensaje_error = "";

$id_usuario_actual = (int)$_SESSION['usuario_id'];

// 2. CONECTAR Y CONSULTAR DATOS DEL PERFIL
$mysqli = conectarBD();
if ($mysqli) {
    
    if (empty($datos_previos)) {
        $sql_usuario = "SELECT NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais 
                        FROM usuarios WHERE IdUsuario = ?";
        $stmt = mysqli_prepare($mysqli, $sql_usuario);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario_actual);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($fila = mysqli_fetch_assoc($res)) {
                $valor_usuario = $fila['NomUsuario'];
                $valor_email   = $fila['Email'];
                $valor_sexo    = ($fila['Sexo'] == 1) ? 'hombre' : (($fila['Sexo'] == 2) ? 'mujer' : 'otro');
                $valor_nac     = $fila['FNacimiento'];
                $valor_ciudad  = $fila['Ciudad'];
                $valor_pais_id = $fila['Pais'];
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $valor_usuario = $datos_previos['usuario'];
        $valor_email   = $datos_previos['email'];
        $valor_sexo    = $datos_previos['sexo'];
        $valor_nac     = $datos_previos['nac'];
        $valor_ciudad  = $datos_previos['ciudad'];
        $valor_pais_id = $datos_previos['pais'];
    }

    $sql_paises = "SELECT IdPais, NomPais FROM paises ORDER BY NomPais";
    $resultado_paises = mysqli_query($mysqli, $sql_paises);
    while ($fila = mysqli_fetch_assoc($resultado_paises)) {
        $paises[] = $fila;
    }

    // 3. OBTENER TODOS LOS ANUNCIOS DEL USUARIO (SIN PAGINACIÓN)
    $anuncios_completos = obtener_anuncios_usuario($id_usuario_actual);

    mysqli_close($mysqli);

} else {
    $mensaje_error = "No se pudo conectar a la base de datos.";
}

if ($mensaje_error) {
    echo "<main><p class='error-campo'>$mensaje_error</p></main>";
} else {
    // Configuración para formulario_comun.php (Edición de perfil)
    $titulo_formulario = "Modificar Mis Datos";
    $action_url = "respuesta_mis_datos.php"; 
    $es_registro = false;
    $desactivado = ""; 
    ?>

    <main>
        <?php include "formulario_comun.php"; ?>

        <section id="mis-anuncios-perfil">
            <h2 class="titulo-seccion">Mis Anuncios Publicados</h2>
            
            <section id="listado-mis-anuncios"> 
                <?php if (empty($anuncios_completos)): ?>
                    <p>Aún no has publicado ningún anuncio.</p>
                <?php else: ?>
                    <?php foreach ($anuncios_completos as $anuncio): ?>
                        <article class="tarjeta-anuncio">
                            <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                                <?php 
                                    // Reutilizamos la función GD de miniaturas
                                    $ruta_thumb = generar_miniatura($anuncio['FPrincipal'], 800); 
                                ?>
                                <img src="<?php echo $ruta_thumb; ?>" 
                                     alt="Miniatura de <?php echo htmlspecialchars($anuncio['Titulo']); ?>" 
                                     class="img-mis-anuncios">
                            </a>
                            <h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </section>
    </main>

    <?php
}

include "footer.php";
ob_end_flush();
?>