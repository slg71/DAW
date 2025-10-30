<?php
// Título de la página
$title = "Resultado de Búsqueda";

// Obtener parámetros GET
$buscar = isset($_GET["buscar"]) ? trim($_GET["buscar"]) : "";
$tipo_anuncio = isset($_GET["tipo_anuncio"]) ? trim($_GET["tipo_anuncio"]) : "";
$tipo_vivienda = isset($_GET["tipo_vivienda"]) ? trim($_GET["tipo_vivienda"]) : "";
$ciudad = isset($_GET["ciudad"]) ? trim($_GET["ciudad"]) : "";
$pais = isset($_GET["pais"]) ? trim($_GET["pais"]) : "";
$precio_min = isset($_GET["precio_min"]) ? trim($_GET["precio_min"]) : "";
$precio_max = isset($_GET["precio_max"]) ? trim($_GET["precio_max"]) : "";
$fecha_publicacion = isset($_GET["fecha_publicacion"]) ? trim($_GET["fecha_publicacion"]) : "";

// Verificar si hay algún criterio de búsqueda
$hay_busqueda = false;
if ($buscar != "" || $tipo_anuncio != "" || $tipo_vivienda != "" || 
    $ciudad != "" || $pais != "" || $precio_min != "" || 
    $precio_max != "" || $fecha_publicacion != "") {
    $hay_busqueda = true;
}

// Datos de ejemplo (simulando base de datos)
$anuncios = array(
    array(
        "id" => 1,
        "titulo" => "Piso renovado en el centro",
        "fecha" => "2025-09-23",
        "ciudad" => "Madrid",
        "pais" => "España",
        "precio" => 250000,
        "imagen" => "./img/piso.jpg"
    ),
    array(
        "id" => 2,
        "titulo" => "Casa con jardín",
        "fecha" => "2025-09-20",
        "ciudad" => "Valencia",
        "pais" => "España",
        "precio" => 220000,
        "imagen" => "./img/piso.jpg"
    ),
    array(
        "id" => 3,
        "titulo" => "Apartamento moderno",
        "fecha" => "2025-09-18",
        "ciudad" => "Barcelona",
        "pais" => "España",
        "precio" => 200000,
        "imagen" => "./img/piso.jpg"
    ),
    array(
        "id" => 4,
        "titulo" => "Ático con terraza",
        "fecha" => "2025-09-15",
        "ciudad" => "Sevilla",
        "pais" => "España",
        "precio" => 180000,
        "imagen" => "./img/piso.jpg"
    ),
    array(
        "id" => 5,
        "titulo" => "Piso céntrico reformado",
        "fecha" => "2025-09-10",
        "ciudad" => "Bilbao",
        "pais" => "España",
        "precio" => 210000,
        "imagen" => "./img/piso.jpg"
    )
);

// Filtrar resultados
$resultados = array();
$mensaje_error = "";

// Si no hay búsqueda, mostrar todos los anuncios
if (!$hay_busqueda) {
    $resultados = $anuncios;
} else {
    // Si hay búsqueda, aplicar filtros
    foreach ($anuncios as $a) {
        $cumple = true;
        
        // Filtro por búsqueda rápida (ciudad desde index)
        if ($buscar != "" && stripos($a["ciudad"], $buscar) === false) {
            $cumple = false;
        }

        // Filtros del formulario avanzado
        if ($ciudad != "" && strcasecmp($a["ciudad"], $ciudad) != 0) {
            $cumple = false;
        }
        
        if ($pais != "" && strcasecmp($a["pais"], $pais) != 0) {
            $cumple = false;
        }
        
        if ($precio_min != "" && $a["precio"] < $precio_min) {
            $cumple = false;
        }
        
        if ($precio_max != "" && $a["precio"] > $precio_max) {
            $cumple = false;
        }

        if ($cumple) {
            $resultados[] = $a;
        }
    }

    // Si no hay resultados después de filtrar, mostrar mensaje
    if (count($resultados) == 0) {
        $mensaje_error = "No se encontraron anuncios que coincidan con tu búsqueda.";
    }
}

// Declaración de DOCTYPE, <html>, <head>, <meta>, <link>, etc.
// require_once("cabecera.inc");

// Inicio de la página
// Contiene <body>
// Muestra logotipo, título del sitio web, barra de navegación principal,
// cuadro de buscar, etc.
// require_once("inicio.inc");

// El contenido principal de la página
?>
<main class="resultados">
    <section id="listado">
        <h2>Resultado de Búsqueda</h2>

<?php
if ($mensaje_error != "") {
    echo "<p class=\"error\">" . htmlspecialchars($mensaje_error) . "</p>\n";
}

if (count($resultados) > 0) {
    foreach ($resultados as $r) {
        echo "<article>\n";
        echo "<a href=\"detalle.php?id=" . urlencode($r["id"]) . "\">\n";
        echo "<img src=\"" . htmlspecialchars($r["imagen"]) . "\" alt=\"Foto del anuncio\">\n";
        echo "</a>\n";
        echo "<h3>" . htmlspecialchars($r["titulo"]) . "</h3>\n";
        echo "<p>Fecha: " . htmlspecialchars($r["fecha"]) . "</p>\n";
        echo "<p>Ciudad: " . htmlspecialchars($r["ciudad"]) . "</p>\n";
        echo "<p>País: " . htmlspecialchars($r["pais"]) . "</p>\n";
        echo "<p>Precio: " . number_format($r["precio"], 0, ',', '.') . " €</p>\n";
        echo "<form action=\"mensaje.html\" method=\"get\">\n";
        echo "<button type=\"submit\">Mensaje</button>\n";
        echo "</form>\n";
        echo "</article>\n";
    }
}
?>
    </section>

    <section id="filtros">
        <h2 id="buscar-heading">Formulario de búsqueda</h2>
        <form method="get" action="resultado.php" aria-labelledby="buscar-heading">
            <fieldset>
                <legend>Tipo de operación</legend>
                <input type="radio" id="tipo_venta" name="tipo_anuncio" value="venta" 
                    <?php if ($tipo_anuncio == "venta") echo "checked"; ?>>
                <label for="tipo_venta">Venta</label>
                <input type="radio" id="tipo_alquiler" name="tipo_anuncio" value="alquiler"
                    <?php if ($tipo_anuncio == "alquiler") echo "checked"; ?>>
                <label for="tipo_alquiler">Alquiler</label>
            </fieldset>

            <label for="tipo_vivienda_select">Seleccione tipo de vivienda</label>
            <select id="tipo_vivienda_select" name="tipo_vivienda">
                <option value="">---</option>
                <option value="obra_nueva" <?php if ($tipo_vivienda == "obra_nueva") echo "selected"; ?>>Obra Nueva</option>
                <option value="vivienda" <?php if ($tipo_vivienda == "vivienda") echo "selected"; ?>>Vivienda</option>
                <option value="oficina" <?php if ($tipo_vivienda == "oficina") echo "selected"; ?>>Oficina</option>
                <option value="local" <?php if ($tipo_vivienda == "local") echo "selected"; ?>>Local</option>
                <option value="garaje" <?php if ($tipo_vivienda == "garaje") echo "selected"; ?>>Garaje</option>
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
// El pie de la página: copyright, declaración legal, dirección de correo, etc.
// Contiene </body></html>
// require_once("pie.inc");
?>