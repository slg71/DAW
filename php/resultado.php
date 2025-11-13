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

// 1. AÑADIMOS CONEXIÓN
include "conexion_bd.php";

$titulo_pagina = "Resultado de Búsqueda";

// Obtener parámetros GET (esto ya lo tenías)
$buscar = isset($_GET["buscar"]) ? trim($_GET["buscar"]) : "";
$tipo_anuncio = isset($_GET["tipo_anuncio"]) ? trim($_GET["tipo_anuncio"]) : "";
$tipo_vivienda = isset($_GET["tipo_vivienda"]) ? trim($_GET["tipo_vivienda"]) : "";
$ciudad = isset($_GET["ciudad"]) ? trim($_GET["ciudad"]) : "";
$pais = isset($_GET["pais"]) ? trim($_GET["pais"]) : "";
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
    // Tengo que unir (JOIN) todo para poder buscar por los nombres de tu formulario cutre
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
    
    // Búsqueda rápida (la del PDF pág 7, que dice "vivienda alquiler alicante")
    // ...es muy difícil. La hago fácil: que busque solo en la ciudad.
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
    
    // Buscamos por el NOMBRE del país (NomPais)
    if ($pais != "") {
        $condiciones_sql[] = "P.NomPais LIKE ?";
        $params_tipos .= "s";
        $params_valores[] = "%" . $pais . "%";
    }
    // Buscamos por el NOMBRE del tipo (NomTAnuncio)
    if ($tipo_anuncio != "") {
        $condiciones_sql[] = "TA.NomTAnuncio = ?";
        $params_tipos .= "s";
        $params_valores[] = $tipo_anuncio;
    }
    // Buscamos por el NOMBRE de la vivienda (NomTVivienda)
    if ($tipo_vivienda != "") {
        $condiciones_sql[] = "TV.NomTVivienda = ?";
        $params_tipos .= "s";
        $params_valores[] = $tipo_vivienda;
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
        $condiciones_sql[] = "DATE(A.FRegistro) = ?"; // DATE() para ignorar la hora
        $params_tipos .= "s";
        $params_valores[] = $fecha_publicacion;
    }

    // 4. Montamos la consulta final
    $sql_final = $sql_base;
    if (count($condiciones_sql) > 0) {
        $sql_final .= " WHERE " . implode(" AND ", $condiciones_sql);
    }
    $sql_final .= " ORDER BY A.FRegistro DESC"; // La práctica dice de ordenar por fecha [cite: 269]

    // 5. Preparamos y ejecutamos (como en la pág 28 del PDF) [cite: 1302-1359]
    $stmt = mysqli_prepare($mysqli, $sql_final);
    
    if ($stmt) {
        // Solo hacemos bind si hay parámetros
        if (count($params_valores) > 0) {
            // Esto es un lío, pero es para meter todos los parámetros del array
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
// Incluir plantillas (esto ya lo tenías)
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

        // Mostrar los anuncios encontrados (MODIFICADO para usar nombres de la BD)
        foreach ($resultados as $r) {
            echo "<article>\n";

            // Enlace a detalle_anuncio.php con el ID real (IdAnuncio)
            echo "<a href='detalle_anuncio.php?id=" . urlencode($r["IdAnuncio"]) . "'>\n";
            // Usamos FPrincipal (asumo que está en img/anuncios/)
            echo "<img src='../img/" . htmlspecialchars($r["FPrincipal"]) . "' alt='Foto del anuncio'>\n";
            echo "</a>\n";

            // Título también clicable
            echo "<h3><a href='detalle_anuncio.php?id=" . urlencode($r["IdAnuncio"]) . "'>"
                 . htmlspecialchars($r["Titulo"]) . "</a></h3>\n";

            echo "<p>Fecha: " . htmlspecialchars($r["FRegistro"]) . "</p>\n";
            echo "<p>Ciudad: " . htmlspecialchars($r["Ciudad"]) . "</p>\n";
            echo "<p>País: " . htmlspecialchars($r["NomPais"]) . "</p>\n"; // <-- Usamos NomPais del JOIN
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
                <input type="radio" id="tipo_venta" name="tipo_anuncio" value="Venta"
                    <?php if ($tipo_anuncio == "Venta") echo "checked"; ?>>
                <label for="tipo_venta">Venta</label>

                <input type="radio" id="tipo_alquiler" name="tipo_anuncio" value="Alquiler"
                    <?php if ($tipo_anuncio == "Alquiler") echo "checked"; ?>>
                <label for="tipo_alquiler">Alquiler</label>
            </fieldset>

            <label for="tipo_vivienda_select">Seleccione tipo de vivienda</label>
            <select id="tipo_vivienda_select" name="tipo_vivienda">
                <option value="">---</option>
                <option value="Obra nueva" <?php if ($tipo_vivienda == "Obra nueva") echo "selected"; ?>>Obra Nueva</option>
                <option value="Vivienda" <?php if ($tipo_vivienda == "Vivienda") echo "selected"; ?>>Vivienda</option>
                <option value="Oficina" <?php if ($tipo_vivienda == "Oficina") echo "selected"; ?>>Oficina</option>
                <option value="Local" <?php if ($tipo_vivienda == "Local") echo "selected"; ?>>Local</option>
                <option value="Garaje" <?php if ($tipo_vivienda == "Garaje") echo "selected"; ?>>Garaje</option>
            </select>

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" placeholder="Ej: Alicante"
                   value="<?php echo htmlspecialchars($ciudad); ?>">

            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" placeholder="Ej: España"
                   value="<?php echo htmlspecialchars($pais); ?>">

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