<?php
session_start();


if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Página: Crear un anuncio nuevo
// -------------------------------------------------------------

include "conexion_bd.php";
include "funciones_costes.php"; 

function obtener_opciones_bd($tabla, $id_columna, $nombre_columna) {
    $opciones = [];
    $mysqli = conectarBD();

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
        } else {
            error_log("Error al consultar la tabla $tabla: " . $mysqli->error);
        }
        $mysqli->close();
    } else {
        error_log("No se pudo conectar a la BD para obtener opciones de $tabla.");
    }
    return $opciones;
}

// Obtenemos las listas de la base de datos
$tipos_anuncios = obtener_opciones_bd('tiposanuncios', 'IdTAnuncio', 'NomTAnuncio');
$tipos_viviendas = obtener_opciones_bd('tiposviviendas', 'IdTVivienda', 'NomTVivienda');
$paises = obtener_opciones_bd('paises', 'IdPais', 'NomPais');


$titulo_pagina = "Crear Nuevo Anuncio"; 
include "paginas_Estilo.php";
include "header.php";       
?>
    
<main>
    <h2>Publica tu Anuncio</h2>
    
    <form action="mis_anuncios.php" method="POST">
        

        <label for="titulo">Título del anuncio:</label>
        <input type="text" id="titulo" name="titulo" maxlength="80" required>

        <!-- Tipo de Contrato -->
        <label for="contrato">Tipo de Contrato:</label>
        <select id="contrato" name="tipo_contrato" required>
            <option value="" disabled selected>Selecciona un tipo</option>
            <?php foreach ($tipos_anuncios as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo['id']); ?>">
                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="descripcion">Descripción del anuncio:</label>
        <textarea id="descripcion" name="descripcion" rows="10" cols="50" required></textarea>

        <!-- Pais -->
        <label for="pais">País:</label>
        <select id="pais" name="pais" required>
            <option value="" disabled selected>Selecciona un país</option>
            <?php foreach ($paises as $pais): ?>
                <option value="<?php echo htmlspecialchars($pais['id']); ?>">
                    <?php echo htmlspecialchars($pais['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="ciudad">Ciudad:</label>
        <input type="text" id="ciudad" name="ciudad" required>

        <label for="metros">Metros Cuadrados (m²):</label>
        <input type="number" id="metros" name="metros_cuadrados" min="1" required>

        <label for="habitaciones">Número de Habitaciones:</label>
        <input type="number" id="habitaciones" name="num_habitaciones" min="0" required>

        <!-- Tipo de Vivienda -->
        <label for="tipo_vivienda">Tipo de Vivienda:</label>
        <select id="tipo_vivienda" name="tipo_vivienda" required>
            <option value="" disabled selected>Selecciona un tipo</option>
            <?php foreach ($tipos_viviendas as $vivienda): ?>
                <option value="<?php echo htmlspecialchars($vivienda['id']); ?>">
                    <?php echo htmlspecialchars($vivienda['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="precio">Precio (€):</label>
        <input type="number" id="precio" name="precio" min="0" step="0.01" required>

        <button type="submit">Publicar Anuncio</button>
        <button type="reset">Limpiar Formulario</button>
        
    </form>
</main>

<?php
include "footer.php"; 
?>