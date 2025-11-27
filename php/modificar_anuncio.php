<?php
session_start();

// 1. Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Pagina: Modificar anuncio
// -------------------------------------------------------------

require_once "conexion_bd.php";
require_once "validaciones.php";

$errores = [];
$mensaje = "";
$mysqli = conectarBD();

function obtener_opciones_bd($mysqli, $tabla, $id_columna, $nombre_columna) {
    $opciones = [];
    if ($mysqli) {
        $query = "SELECT $id_columna, $nombre_columna FROM $tabla ORDER BY $nombre_columna ASC";
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $opciones[] = [
                    'id' => $row[$id_columna],
                    'nombre' => $row[$nombre_columna]
                ];
            }
            $result->free();
        }
    }
    return $opciones;
}

// Cargar listas auxiliares
$tipos_anuncios = obtener_opciones_bd($mysqli, 'tiposanuncios', 'IdTAnuncio', 'NomTAnuncio');
$tipos_viviendas = obtener_opciones_bd($mysqli, 'tiposviviendas', 'IdTVivienda', 'NomTVivienda');
$paises = obtener_opciones_bd($mysqli, 'paises', 'IdPais', 'NomPais');

$id_anuncio = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

// Verificar que el anuncio existe y pertenece al usuario
if ($id_anuncio > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
    $stmt->bind_param("ii", $id_anuncio, $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos_actuales = $resultado->fetch_assoc();
    $stmt->close();

    if (!$datos_actuales) {
        // El anuncio no existe
        header("Location: mis_anuncios.php?error=no_encontrado");
        exit;
    }
} else {
    header("Location: mis_anuncios.php");
    exit;
}

// --- Inicialización de variables ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
} else {
    // Carga inicial desde BD
    $Titulo = $datos_actuales['Titulo'];
    $Precio = $datos_actuales['Precio'];
    $TAnuncio = $datos_actuales['TAnuncio'];
    $TVivienda = $datos_actuales['TVivienda'];
    $Pais = $datos_actuales['Pais'];
    $Ciudad = $datos_actuales['Ciudad'];
    $Texto = $datos_actuales['Texto'];
    $Superficie = $datos_actuales['Superficie'];
    $NHabitaciones = $datos_actuales['NHabitaciones'];
    $NBanyos = $datos_actuales['NBanyos'];
    $Planta = $datos_actuales['Planta'];
    $Anyo = $datos_actuales['Anyo'];
}

// --- PROCESADO DEL FORMULARIO (UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errores = validarAnuncio($_POST);//en validaciones

    //actualizar en la BD si no hay errores
    if (empty($errores)) {
        $sql = "UPDATE anuncios SET 
                    Titulo = ?, Precio = ?, Texto = ?, TAnuncio = ?, TVivienda = ?, 
                    Pais = ?, Ciudad = ?, Superficie = ?, NHabitaciones = ?, 
                    NBanyos = ?, Planta = ?, Anyo = ?
                WHERE IdAnuncio = ? AND Usuario = ?";
        
        $stmt = $mysqli->prepare($sql);
        
        // Ajustamos los nulos
        $sup = ($Superficie !== "") ? $Superficie : null;
        $hab = ($NHabitaciones !== "") ? $NHabitaciones : null;
        $ban = ($NBanyos !== "") ? $NBanyos : null;
        $pla = ($Planta !== "") ? $Planta : null;
        $any = ($Anyo !== "") ? $Anyo : null;
 
        $stmt->bind_param("sdsiiisdiiiiii", 
            $Titulo, $Precio, $Texto, $TAnuncio, $TVivienda, $Pais, $Ciudad,
            $sup, $hab, $ban, $pla, $any, 
            $id_anuncio, $_SESSION['usuario_id']
        );

        if ($stmt->execute()) {
            $mensaje = "Modificación realizada, tu anuncio ha sido actualizado.";
        } else {
            $errores['general'] = "Error al actualizar en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    }
}
$mysqli->close();

$titulo_pagina = "Modificar Anuncio"; 
require_once "paginas_Estilo.php";
require_once "header.php";       
?>
    
<main>
    <h2>Modificar Anuncio</h2>

    <?php if (isset($errores['general'])): ?>
        <p><?php echo $errores['general']; ?></p>
    <?php endif; ?>

    <!-- El form envia el ID como hidden o en la URL -->
    <form action="respuesta_anuncios.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id_anuncio; ?>">
        
        <fieldset>
            <legend>Datos Principales</legend>

            <p>
                <label for="titulo">Título del anuncio (*):</label>
                <input type="text" id="titulo" name="Titulo" maxlength="255" 
                       value="<?php echo htmlspecialchars($Titulo); ?>" required>
                <?php if(isset($errores['Titulo'])): ?>
                    <strong><?php echo $errores['Titulo']; ?></strong>
                <?php endif; ?>
            </p>

            <p>
                <label for="precio">Precio (€) (*):</label>
                <input type="number" id="precio" name="Precio" min="0" step="0.01" 
                       value="<?php echo htmlspecialchars($Precio); ?>" required>
                <?php if(isset($errores['Precio'])): ?>
                    <strong><?php echo $errores['Precio']; ?></strong>
                <?php endif; ?>
            </p>

            <p>
                <label for="tipo_anuncio">Tipo de Operación (*):</label>
                <select id="tipo_anuncio" name="TAnuncio" required>
                    <option value="" disabled <?php echo empty($TAnuncio) ? 'selected' : ''; ?>>-- Seleccionar --</option>
                    <?php foreach ($tipos_anuncios as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>" 
                            <?php echo ($TAnuncio == $tipo['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(isset($errores['TAnuncio'])): ?>
                    <strong><?php echo $errores['TAnuncio']; ?></strong>
                <?php endif; ?>
            </p>

            <p>
                <label for="tipo_vivienda">Tipo de Vivienda (*):</label>
                <select id="tipo_vivienda" name="TVivienda" required>
                    <option value="" disabled <?php echo empty($TVivienda) ? 'selected' : ''; ?>>-- Seleccionar --</option>
                    <?php foreach ($tipos_viviendas as $vivienda): ?>
                        <option value="<?php echo $vivienda['id']; ?>" 
                            <?php echo ($TVivienda == $vivienda['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($vivienda['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(isset($errores['TVivienda'])): ?>
                    <strong><?php echo $errores['TVivienda']; ?></strong>
                <?php endif; ?>
            </p>
        </fieldset>

        <fieldset>
            <legend>Ubicación</legend>
            <p>
                <label for="pais">País (*):</label>
                <select id="pais" name="Pais" required>
                    <option value="" disabled <?php echo empty($Pais) ? 'selected' : ''; ?>>-- Seleccionar --</option>
                    <?php foreach ($paises as $p): ?>
                        <option value="<?php echo $p['id']; ?>" 
                            <?php echo ($Pais == $p['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(isset($errores['Pais'])): ?>
                    <strong><?php echo $errores['Pais']; ?></strong>
                <?php endif; ?>
            </p>

            <p>
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="Ciudad" maxlength="255" 
                       value="<?php echo htmlspecialchars($Ciudad); ?>">
            </p>
        </fieldset>

        <fieldset>
            <legend>Descripción</legend>
            <p>
                <label for="texto">Descripción detallada (*):</label>
                <textarea id="texto" name="Texto" rows="6" required><?php echo htmlspecialchars($Texto); ?></textarea>
                <?php if(isset($errores['Texto'])): ?>
                    <strong><?php echo $errores['Texto']; ?></strong>
                <?php endif; ?>
            </p>
        </fieldset>

        <fieldset>
            <legend>Características (Opcional)</legend>
            
            <p>
                <label for="superficie">Superficie (m²):</label>
                <input type="number" id="superficie" name="Superficie" min="0" step="0.01" 
                       value="<?php echo htmlspecialchars($Superficie); ?>">
                <?php if(isset($errores['Superficie'])): ?>
                    <strong><?php echo $errores['Superficie']; ?></strong>
                <?php endif; ?>
            </p>

            <p>
                <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="NHabitaciones" min="0" 
                       value="<?php echo htmlspecialchars($NHabitaciones); ?>">
                <?php if(isset($errores['NHabitaciones'])): ?>
                    <strong><?php echo $errores['NHabitaciones']; ?></strong>
                <?php endif; ?>
            </p>

            <p>
                <label for="banyos">Baños:</label>
                <input type="number" id="banyos" name="NBanyos" min="0" 
                       value="<?php echo htmlspecialchars($NBanyos); ?>">
            </p>

            <p>
                <label for="planta">Planta:</label>
                <input type="number" id="planta" name="Planta" 
                       value="<?php echo htmlspecialchars($Planta); ?>">
            </p>

            <p>
                <label for="anyo">Año Construcción:</label>
                <input type="number" id="anyo" name="Anyo" min="1900" max="2099" 
                       value="<?php echo htmlspecialchars($Anyo); ?>">
            </p>
        </fieldset>
        
        <p class="botones-form">
            <button type="submit">Guardar Cambios</button>
            <button type="button"><a href="mis_anuncios.php">Cancelar</a></button>
        </p>
        
    </form>
</main>

<?php
include "footer.php"; 
?>