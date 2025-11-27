<?php
require_once "sesion_control.php";

// -------------------------------------------------------------
// Página: Crear un anuncio nuevo
// -------------------------------------------------------------

require_once "conexion_bd.php";
require_once "validaciones.php"; 

$errores = [];
$mysqli = conectarBD();

// obtener la lista de opciones desde la bd
function obtener_opciones_bd($mysqli, $tabla, $id_columna, $nombre_columna) {
    $opciones = [];
    if ($mysqli) {
        $query = "SELECT $id_columna, $nombre_columna FROM $tabla ORDER BY $nombre_columna ASC";
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $opciones[] = [//guardamos los resultados
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

// inicializar variables del formulario
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
    
    // Recogemos los datos
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

    $errores = validarAnuncio($_POST);//validamos los datos

    // insertamos en la bd
    if (empty($errores)) {
        $sql = "INSERT INTO anuncios (Titulo, Precio, Texto, TAnuncio, TVivienda, Pais, Ciudad, 
                Superficie, NHabitaciones, NBanyos, Planta, Anyo, Usuario, FRegistro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $mysqli->prepare($sql);
        
        // Ajustamos los nulos para la BD
        $sup = ($Superficie !== "") ? $Superficie : null;
        $hab = ($NHabitaciones !== "") ? $NHabitaciones : null;
        $ban = ($NBanyos !== "") ? $NBanyos : null;
        $pla = ($Planta !== "") ? $Planta : null;
        $any = ($Anyo !== "") ? $Anyo : null;
        $usuario_id = $_SESSION['usuario_id'];

        // Tipos: s=string, d=double, i=int
        $stmt->bind_param("sdsiiisdiiiii", 
            $Titulo, $Precio, $Texto, $TAnuncio, $TVivienda, $Pais, $Ciudad,
            $sup, $hab, $ban, $pla, $any, $usuario_id
        );

        if ($stmt->execute()) {
            $nuevo_id = $mysqli->insert_id;
            // Redirigimos a añadir foto
            header("Location: añadir_foto.php?anuncio_id=$nuevo_id&mensaje=Anuncio creado correctamente");
            exit;
        } else {
            $errores['general'] = "Error al insertar en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    }
}
$mysqli->close();

$titulo_pagina = "Crear Nuevo Anuncio"; 
require_once "paginas_Estilo.php";
require_once "header.php";       
?>
    
<main>
    <h2>Publica tu Anuncio</h2>
    
    <?php if (isset($errores['general'])): ?>
        <p><?php echo $errores['general']; ?></p>
    <?php endif; ?>

    <!-- El formulario se envia a si mismo -->
    <form action="crear_anuncio.php" method="POST">
                
        <fieldset>
            <legend>Datos Principales</legend>

            <label for="titulo">Título del anuncio (*):</label>
            <input type="text" id="titulo" name="Titulo" maxlength="255" 
                   value="<?php echo htmlspecialchars($Titulo); ?>" required>
            <?php if(isset($errores['Titulo'])): ?>
                <strong><?php echo $errores['Titulo']; ?></strong>
            <?php endif; ?>

            <label for="precio">Precio (€) (*):</label>
            <input type="number" id="precio" name="Precio" min="0" step="0.01" 
                   value="<?php echo htmlspecialchars($Precio); ?>" required>
            <?php if(isset($errores['Precio'])): ?>
                <strong><?php echo $errores['Precio']; ?></strong>
            <?php endif; ?>
 
            <label for="tipo_anuncio">Tipo de Operación (*):</label>
            <select id="tipo_anuncio" name="TAnuncio" required>
                <option value="" disabled <?php echo empty($TAnuncio) ? 'selected' : ''; ?>>-- Seleccionar --</option>                    <?php foreach ($tipos_anuncios as $tipo): ?>
                    <option value="<?php echo $tipo['id']; ?>" 
                        <?php echo ($TAnuncio == $tipo['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if(isset($errores['TAnuncio'])): ?>
                <strong><?php echo $errores['TAnuncio']; ?></strong>
            <?php endif; ?>
 
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
            
        </fieldset>

        <fieldset>
            <legend>Ubicación</legend>
            
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

        
            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="Ciudad" maxlength="255" 
                    value="<?php echo htmlspecialchars($Ciudad); ?>">
            
        </fieldset>

        <fieldset>
            <legend>Descripción</legend>
            
            <label for="texto">Descripción detallada (*):</label>
            <textarea id="texto" name="Texto" rows="6" required><?php echo htmlspecialchars($Texto); ?></textarea>
            <?php if(isset($errores['Texto'])): ?>
                <strong><?php echo $errores['Texto']; ?></strong>
            <?php endif; ?>
            
        </fieldset>

        <fieldset>
            <legend>Características (Opcional)</legend>
                        
            <label for="superficie">Superficie (m²):</label>
            <input type="number" id="superficie" name="Superficie" min="0" step="0.01" 
                   value="<?php echo htmlspecialchars($Superficie); ?>">
            <?php if(isset($errores['Superficie'])): ?>
                <strong><?php echo $errores['Superficie']; ?></strong>
            <?php endif; ?>
            

            
            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" name="NHabitaciones" min="0" 
                   value="<?php echo htmlspecialchars($NHabitaciones); ?>">
            <?php if(isset($errores['NHabitaciones'])): ?>
                <strong><?php echo $errores['NHabitaciones']; ?></strong>
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