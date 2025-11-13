<?php
// -------------------------------------------------------------
// Página: resultado.php (AHORA CON BD)
// -------------------------------------------------------------
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

include "conexion_bd.php";

function obtener_opciones_bd($tabla, $id_columna, $nombre_columna) {
    $opciones = [];
    $mysqli = conectarBD();
    if (!$mysqli) return $opciones;

    $query = "SELECT $id_columna, $nombre_columna FROM $tabla ORDER BY $nombre_columna";
    
    if ($resultado = $mysqli->query($query)) {
        while ($fila = $resultado->fetch_assoc()) {
            $opciones[] = [
                'id' => $fila[$id_columna],
                'nombre' => $fila[$nombre_columna]
            ];
        }
        $resultado->free();
    } else {
        error_log("Error al consultar la tabla $tabla: " . $mysqli->error);
    }
    $mysqli->close();
    return $opciones;
}


// --- OBTENCION DE DATOS DEL FORMULARIO ---
$tipos_anuncios = obtener_opciones_bd("tiposanuncios", "IdTAnuncio", "NomTAnuncio");
$tipos_viviendas = obtener_opciones_bd("tiposviviendas", "IdTVivienda", "NomTVivienda");
$paises_bd = obtener_opciones_bd("paises", "IdPais", "NomPais");

$titulo_pagina = "Resultado de Búsqueda";

// Obtener parámetros GET
$buscar = isset($_GET["buscar"]) ? trim($_GET["buscar"]) : "";

$tipo_anuncio = isset($_GET["tipo_anuncio"]) ? trim($_GET["tipo_anuncio"]) : "";
$tipo_vivienda = isset($_GET["tipo_vivienda"]) ? trim($_GET["tipo_vivienda"]) : "";
$ciudad = isset($_GET["ciudad"]) ? trim($_GET["ciudad"]) : "";
$pais_seleccionado = isset($_GET["pais"]) ? trim($_GET["pais"]) : ""; // Renombrado para evitar conflicto
$precio_min = isset($_GET["precio_min"]) ? trim($_GET["precio_min"]) : "";
$precio_max = isset($_GET["precio_max"]) ? trim($_GET["precio_max"]) : "";
$fecha_publicacion = isset($_GET["fecha_publicacion"]) ? trim($_GET["fecha_publicacion"]) : "";

// -------------------------------------------------------------
// MATAMOS LOS DATOS SIMULADOS Y PREPARAMOS LA CONSULTA SQL
// -------------------------------------------------------------

$resultados = array(); // Array para los anuncios de la BD
$mensaje_error = "";
$mysqli = conectarBD();

if ($mysqli) {
    
    // 2. Preparamos la consulta SQL base
    $sql_base = "SELECT 
                    A.IdAnuncio, A.Titulo, A.FRegistro, A.Ciudad, A.Precio, A.FPrincipal, 
                    P.NomPais 
                 FROM anuncios AS A
                 LEFT JOIN paises AS P ON A.Pais = P.IdPais
                 LEFT JOIN tiposanuncios AS TA ON A.TAnuncio = TA.IdTAnuncio
                 LEFT JOIN tiposviviendas AS TV ON A.TVivienda = TV.IdTVivienda
                 ";

    $condiciones_sql = []; // Array para los WHERE
    $params_valores = []; // Array para los valores (?)
    $params_tipos = ""; // String para los tipos (s, i, d)

    // 3. Montamos los filtros (el WHERE)
    
    // Búsqueda rápida
    if ($buscar != "") {
        $condiciones_sql[] = "A.Ciudad LIKE ?";
        $params_tipos .= "s";
        $params_valores[] = "%" . $buscar . "%";
    }

    // Filtros avanzados del formulario
    if ($ciudad != "") {
        $condiciones_sql[] = "A.Ciudad LIKE ?";
        $params_tipos .= "s";
        $params_valores[] = "%" . $ciudad . "%";
    }
    
    // El País ahora viene con su ID numérico
    if ($pais_seleccionado != "" && is_numeric($pais_seleccionado)) {
        $condiciones_sql[] = "A.Pais = ?";
        $params_tipos .= "i";
        $params_valores[] = (int)$pais_seleccionado;
    }
    
    // El Tipo Anuncio (Venta/Alquiler) viene con su nombre, se mantiene el filtro por nombre
    if ($tipo_anuncio != "") {
        $condiciones_sql[] = "TA.NomTAnuncio = ?";
        $params_tipos .= "s";
        $params_valores[] = $tipo_anuncio;
    }
    
    // El Tipo Vivienda ahora viene con su ID numérico
    if ($tipo_vivienda != "" && is_numeric($tipo_vivienda)) {
        $condiciones_sql[] = "A.TVivienda = ?";
        $params_tipos .= "i";
        $params_valores[] = (int)$tipo_vivienda;
    }

    if ($precio_min != "") {
        $condiciones_sql[] = "A.Precio >= ?";
        $params_tipos .= "d"; // 'd' de decimal (o double)
        $params_valores[] = $precio_min;
    }
    if ($precio_max != "") {
        $condiciones_sql[] = "A.Precio <= ?";
        $params_tipos .= "d";
        $params_valores[] = $precio_max;
    }
    if ($fecha_publicacion != "") {
        $condiciones_sql[] = "DATE(A.FRegistro) >= ?"; // Fecha IGUAL O POSTERIOR
        $params_tipos .= "s";
        $params_valores[] = $fecha_publicacion;
    }

    // 4. Montamos la consulta final
    $sql_final = $sql_base;
    if (count($condiciones_sql) > 0) {
        $sql_final .= " WHERE " . implode(" AND ", $condiciones_sql);
    }
    $sql_final .= " ORDER BY A.FRegistro DESC"; 

    // 5. Preparamos y ejecutamos
    $stmt = mysqli_prepare($mysqli, $sql_final);
    
    if ($stmt) {
        // Solo hacemos bind si hay parámetros
        if (count($params_valores) > 0) {
            mysqli_stmt_bind_param($stmt, $params_tipos, ...$params_valores);
        }
        
        mysqli_stmt_execute($stmt);
        $resultado_query = mysqli_stmt_get_result($stmt);
        
        // Pillamos los resultados
        while ($fila = mysqli_fetch_assoc($resultado_query)) {
            $resultados[] = $fila; // Llenamos el array
        }
        
        if (count($resultados) == 0) {
            $mensaje_error = "No se encontraron anuncios que coincidan con tu búsqueda.";
        }
        
        mysqli_stmt_close($stmt);

    } else {
        $mensaje_error = "Error al preparar la consulta SQL.";
    }
    mysqli_close($mysqli);
} else {
    $mensaje_error = "Error de conexión a la base de datos.";
}

// -------------------------------------------------------------
// Incluir plantillas
// -------------------------------------------------------------

include "paginas_Estilo.php";
include "header.php";
?>

<main class="resultados">
    <section id="listado">
        <h2>Resultado de Búsqueda</h2>

        <?php
        // Mostrar mensaje si no hay resultados
        if ($mensaje_error != "") {
            echo "<p class='error'>" . htmlspecialchars($mensaje_error) . "</p>\n";
        }

        // Mostrar los anuncios encontrados
        foreach ($resultados as $r) {
            echo "<article>\n";

            // Enlace a detalle_anuncio.php con el ID real (IdAnuncio)
            echo "<a href='detalle_anuncio.php?id=" . urlencode($r["IdAnuncio"]) . "'>\n";
            echo "<img src='../img/" . htmlspecialchars($r["FPrincipal"]) . "' alt='Foto del anuncio'>\n";
            echo "</a>\n";

            // Título también clicable
            echo "<h3><a href='detalle_anuncio.php?id=" . urlencode($r["IdAnuncio"]) . "'>"
                 . htmlspecialchars($r["Titulo"]) . "</a></h3>\n";

            echo "<p>Fecha: " . htmlspecialchars($r["FRegistro"]) . "</p>\n";
            echo "<p>Ciudad: " . htmlspecialchars($r["Ciudad"]) . "</p>\n";
            echo "<p>País: " . htmlspecialchars($r["NomPais"]) . "</p>\n"; 
            echo "<p>Precio: " . number_format($r["Precio"], 0, ',', '.') . " €</p>\n";

            echo "<form action='mensaje.php' method='get'>\n";
            echo "<button type='submit'>Mensaje</button>\n";
            echo "</form>\n";

            echo "</article>\n";
        }
        ?>
    </section>

    <section id="filtros">
        <h2 id="buscar-heading">Formulario de búsqueda</h2>
        <form method="get" action="resultado.php" aria-labelledby="buscar-heading">

            <fieldset>
                <legend>Tipo de operación</legend>
                <?php foreach ($tipos_anuncios as $tipo): ?>
                    <!-- Se sigue usando el nombre (NomTAnuncio) como valor para evitar cambiar la lógica SQL de WHERE TA.NomTAnuncio = ? -->
                    <input type="radio" id="tipo_<?php echo strtolower($tipo['nombre']); ?>" 
                           name="tipo_anuncio" value="<?php echo htmlspecialchars($tipo['nombre']); ?>"
                        <?php if ($tipo_anuncio == $tipo['nombre']) echo "checked"; ?>>
                    <label for="tipo_<?php echo strtolower($tipo['nombre']); ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></label>
                <?php endforeach; ?>
            </fieldset>

            <label for="tipo_vivienda_select">Seleccione tipo de vivienda</label>
            <select id="tipo_vivienda_select" name="tipo_vivienda">
                <option value="">---</option>
                <?php foreach ($tipos_viviendas as $vivienda): ?>
                    <!-- Ahora usamos el ID numérico (IdTVivienda) como valor -->
                    <option value="<?php echo htmlspecialchars($vivienda['id']); ?>" 
                        <?php if ($tipo_vivienda == $vivienda['id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($vivienda['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" placeholder="Ej: Alicante"
                   value="<?php echo htmlspecialchars($ciudad); ?>">

            <label for="pais_select">País</label>
            <select id="pais_select" name="pais">
                <option value="">---</option>
                <?php foreach ($paises_bd as $pais): ?>
                    <!-- Ahora usamos el ID numérico (IdPais) como valor -->
                    <option value="<?php echo htmlspecialchars($pais['id']); ?>"
                        <?php if ($pais_seleccionado == $pais['id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($pais['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="precio_min">Precio mínimo (EUR)</label>
            <input type="number" id="precio_min" name="precio_min" min="0" step="0.01" placeholder="0"
                   value="<?php echo htmlspecialchars($precio_min); ?>">

            <label for="precio_max">Precio máximo (EUR)</label>
            <input type="number" id="precio_max" name="precio_max" min="0" step="0.01" placeholder="0"
                   value="<?php echo htmlspecialchars($precio_max); ?>">

            <label for="fecha_publicacion">Fecha de publicación</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion"
                   value="<?php echo htmlspecialchars($fecha_publicacion); ?>">

            <button type="submit">Buscar</button>
            <button type="reset">Limpiar</button>
        </form>
    </section>
</main>

<?php
include "footer.php";
?>