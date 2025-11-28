<?php
require_once "sesion_control.php"; // Se asume que esto inicia session_start()

// -------------------------------------------------------------
// Página: Crear un anuncio nuevo
// -------------------------------------------------------------

require_once "conexion_bd.php";
require_once "validaciones.php"; 

$errores = [];
$mysqli = conectarBD();

// --- FUNCIONES ---
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

// Cargar listas desde la BD
$tipos_anuncios = obtener_opciones_bd($mysqli, 'tiposanuncios', 'IdTAnuncio', 'NomTAnuncio');
$tipos_viviendas = obtener_opciones_bd($mysqli, 'tiposviviendas', 'IdTVivienda', 'NomTVivienda');
$paises = obtener_opciones_bd($mysqli, 'paises', 'IdPais', 'NomPais');

// 1. Inicializar variables vacías por defecto
$Titulo = '';
$Precio = '';
$TAnuncio = '';
$TVivienda = '';
$Pais = '';
$Ciudad = '';
$Texto = '';
$Superficie = '';
$NHabitaciones = '';
$NBanyos = '';
$Planta = '';
$Anyo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recogemos datos
    $datos_form = [
        'Titulo' => trim($_POST['Titulo'] ?? ''),
        'Precio' => $_POST['Precio'] ?? '',
        'TAnuncio' => $_POST['TAnuncio'] ?? '',
        'TVivienda' => $_POST['TVivienda'] ?? '',
        'Pais' => $_POST['Pais'] ?? '',
        'Ciudad' => trim($_POST['Ciudad'] ?? ''),
        'Texto' => trim($_POST['Texto'] ?? ''),
        'Superficie' => $_POST['Superficie'] ?? null,
        'NHabitaciones' => $_POST['NHabitaciones'] ?? null,
        'NBanyos' => $_POST['NBanyos'] ?? null,
        'Planta' => $_POST['Planta'] ?? null,
        'Anyo' => $_POST['Anyo'] ?? null
    ];

    // Validamos
    $errores = validarAnuncio($_POST);

    if (!empty($errores)) {
        // si hay errores, guardar en sesion y redirigir
        
        $_SESSION['errores_anuncio'] = $errores;     // Guardamos el array de errores
        $_SESSION['datos_anuncio'] = $datos_form;    // Guardamos lo que escribio el usu
        
        // Redirigimos a la misma página (GET)
        header("Location: crear_anuncio.php");
        exit; 

    } else {
        // -insertar en BD si no hay errores
        
        $sql = "INSERT INTO anuncios (Titulo, Precio, Texto, TAnuncio, TVivienda, Pais, Ciudad, 
                Superficie, NHabitaciones, NBanyos, Planta, Anyo, Usuario, FRegistro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $mysqli->prepare($sql);
        
        // Ajustamos nulos
        $sup = ($datos_form['Superficie'] !== "") ? $datos_form['Superficie'] : null;
        $hab = ($datos_form['NHabitaciones'] !== "") ? $datos_form['NHabitaciones'] : null;
        $ban = ($datos_form['NBanyos'] !== "") ? $datos_form['NBanyos'] : null;
        $pla = ($datos_form['Planta'] !== "") ? $datos_form['Planta'] : null;
        $any = ($datos_form['Anyo'] !== "") ? $datos_form['Anyo'] : null;
        $usuario_id = $_SESSION['usuario_id'];

        $stmt->bind_param("sdsiiisdiiiii", 
            $datos_form['Titulo'], $datos_form['Precio'], $datos_form['Texto'], 
            $datos_form['TAnuncio'], $datos_form['TVivienda'], $datos_form['Pais'], 
            $datos_form['Ciudad'], $sup, $hab, $ban, $pla, $any, $usuario_id
        );

        if ($stmt->execute()) {
            $nuevo_id = $mysqli->insert_id;
            header("Location: añadir_foto.php?anuncio_id=$nuevo_id&mensaje=Anuncio creado correctamente");
            exit;
        } else {
            // Error de base de datos (este es raro, pero lo manejamos igual)
            $_SESSION['errores_anuncio'] = ['general' => "Error BD: " . $stmt->error];
            $_SESSION['datos_anuncio'] = $datos_form;
            header("Location: crear_anuncio.php");
            exit;
        }
        $stmt->close();
    }
}

// recuperar errores y datos antiguos si existen
if (isset($_SESSION['errores_anuncio'])) {
    $errores = $_SESSION['errores_anuncio'];
    
    // Recuperar los datos antiguos para rellenar los inputs
    if (isset($_SESSION['datos_anuncio'])) {
        $datos = $_SESSION['datos_anuncio'];
        $Titulo = $datos['Titulo'];
        $Precio = $datos['Precio'];
        $TAnuncio = $datos['TAnuncio'];
        $TVivienda = $datos['TVivienda'];
        $Pais = $datos['Pais'];
        $Ciudad = $datos['Ciudad'];
        $Texto = $datos['Texto'];
        $Superficie = $datos['Superficie'];
        $NHabitaciones = $datos['NHabitaciones'];
        $NBanyos = $datos['NBanyos'];
        $Planta = $datos['Planta'];
        $Anyo = $datos['Anyo'];
    }

    // Borrar la seson los errores desaparecen
    unset($_SESSION['errores_anuncio']);
    unset($_SESSION['datos_anuncio']);
}

$mysqli->close();

$titulo_pagina = "Crear Nuevo Anuncio"; 
require_once "paginas_Estilo.php";
require_once "header.php";       
?>
    
<main>
    <h2>Publica tu Anuncio</h2>
    
    <?php if (isset($errores['general'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $errores['general']; ?></p>
    <?php endif; ?>

    <form action="crear_anuncio.php" method="POST" novalidate>
                
        <fieldset>
            <legend>Datos Principales</legend>

            <label for="titulo">Título del anuncio (*):</label>
            <input type="text" id="titulo" name="Titulo" maxlength="255" 
                   value="<?php echo htmlspecialchars($Titulo); ?>">
            
            <?php if(isset($errores['Titulo'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['Titulo']; ?>
                </div>
            <?php endif; ?>

            <label for="precio">Precio (€) (*):</label>
            <input type="number" id="precio" name="Precio" min="0" step="0.01" 
                   value="<?php echo htmlspecialchars($Precio); ?>">
            
            <?php if(isset($errores['Precio'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['Precio']; ?>
                </div>
            <?php endif; ?>
 
            <label for="tipo_anuncio">Tipo de Operación (*):</label>
            <select id="tipo_anuncio" name="TAnuncio">
                <option value="" disabled <?php echo empty($TAnuncio) ? 'selected' : ''; ?>>-- Seleccionar --</option>
                <?php foreach ($tipos_anuncios as $tipo): ?>
                    <option value="<?php echo $tipo['id']; ?>" 
                        <?php echo ($TAnuncio == $tipo['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php if(isset($errores['TAnuncio'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['TAnuncio']; ?>
                </div>
            <?php endif; ?>
 
            <label for="tipo_vivienda">Tipo de Vivienda (*):</label>
            <select id="tipo_vivienda" name="TVivienda">
                <option value="" disabled <?php echo empty($TVivienda) ? 'selected' : ''; ?>>-- Seleccionar --</option>
                <?php foreach ($tipos_viviendas as $vivienda): ?>
                    <option value="<?php echo $vivienda['id']; ?>" 
                        <?php echo ($TVivienda == $vivienda['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($vivienda['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php if(isset($errores['TVivienda'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['TVivienda']; ?>
                </div>
            <?php endif; ?>
            
        </fieldset>

        <fieldset>
            <legend>Ubicación</legend>
            
            <label for="pais">País (*):</label>
            <select id="pais" name="Pais">
                <option value="" disabled <?php echo empty($Pais) ? 'selected' : ''; ?>>-- Seleccionar --</option>
                <?php foreach ($paises as $p): ?>
                    <option value="<?php echo $p['id']; ?>" 
                        <?php echo ($Pais == $p['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php if(isset($errores['Pais'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['Pais']; ?>
                </div>
            <?php endif; ?>
        
            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="Ciudad" maxlength="255" 
                    value="<?php echo htmlspecialchars($Ciudad); ?>">
            </fieldset>

        <fieldset>
            <legend>Descripción</legend>
            
            <label for="texto">Descripción detallada (*):</label>
            <textarea id="texto" name="Texto" rows="6"><?php echo htmlspecialchars($Texto); ?></textarea>
            
            <?php if(isset($errores['Texto'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['Texto']; ?>
                </div>
            <?php endif; ?>
            
        </fieldset>

        <fieldset>
            <legend>Características (Opcional)</legend>
            
            <label for="superficie">Superficie (m²):</label>
            <input type="number" id="superficie" name="Superficie" min="0" step="0.01" 
                   value="<?php echo htmlspecialchars($Superficie); ?>">
            <?php if(isset($errores['Superficie'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['Superficie']; ?>
                </div>
            <?php endif; ?>
            
            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" name="NHabitaciones" min="0" 
                   value="<?php echo htmlspecialchars($NHabitaciones); ?>">
            <?php if(isset($errores['NHabitaciones'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                    <?php echo $errores['NHabitaciones']; ?>
                </div>
            <?php endif; ?>
  
            <label for="banyos">Baños:</label>
            <input type="number" id="banyos" name="NBanyos" min="0" 
                   value="<?php echo htmlspecialchars($NBanyos); ?>">
            
            <label for="planta">Planta:</label>
            <input type="number" id="planta" name="Planta" 
                    value="<?php echo htmlspecialchars($Planta); ?>">

            <label for="anyo">Año Construcción:</label>
            <input type="number" id="anyo" name="Anyo" min="1900" max="2099" 
                   value="<?php echo htmlspecialchars($Anyo); ?>">
            
        </fieldset>
        
        <p class="botones-form">
            <button type="submit">Publicar Anuncio</button>
            <button type="reset">Limpiar Formulario</button>
        </p>
        
    </form>
</main>

<?php
include "footer.php"; 
?>