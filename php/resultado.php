<?php
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $buscar = trim($_GET["buscar"] ?? ""); // viene del index
        $tipo_anuncio = trim($_GET["tipo_anuncio"] ?? "");
        $tipo_vivienda = trim($_GET["tipo_vivienda"] ?? "");
        $ciudad = trim($_GET["ciudad"] ?? "");
        $pais = trim($_GET["pais"] ?? "");
        $precio_min = trim($_GET["precio_min"] ?? "");
        $precio_max = trim($_GET["precio_max"] ?? "");
        $fecha_publicacion = trim($_GET["fecha_publicacion"] ?? "");

        if (
            $buscar === "" &&
            $tipo_anuncio === "" &&
            $tipo_vivienda === "" &&
            $ciudad === "" &&
            $pais === "" &&
            $precio_min === "" &&
            $precio_max === "" &&
            $fecha_publicacion === ""
        ) {
            header("Location: ../resultado.php?error=empty");
            exit;
        }

        $anuncios = [
            [
                "id" => 1,
                "titulo" => "Piso renovado en el centro",
                "fecha" => "2025-09-23",
                "ciudad" => "Madrid",
                "pais" => "España",
                "precio" => 250000,
                "imagen" => "./img/piso.jpg"
            ],
            [
                "id" => 2,
                "titulo" => "Casa con jardín",
                "fecha" => "2025-09-20",
                "ciudad" => "Valencia",
                "pais" => "España",
                "precio" => 220000,
                "imagen" => "./img/piso.jpg"
            ],
            [
                "id" => 3,
                "titulo" => "Apartamento moderno",
                "fecha" => "2025-09-18",
                "ciudad" => "Barcelona",
                "pais" => "España",
                "precio" => 200000,
                "imagen" => "./img/piso.jpg"
            ]
        ];

        $resultados = [];

        foreach ($anuncios as $a) {
            // Si viene la búsqueda rápida del index
            if ($buscar && stripos($a["ciudad"], $buscar) === false) continue;

            // Si viene desde el formulario avanzado
            if ($ciudad && strcasecmp($a["ciudad"], $ciudad) !== 0) continue;
            if ($pais && strcasecmp($a["pais"], $pais) !== 0) continue;
            if ($precio_min && $a["precio"] < $precio_min) continue;
            if ($precio_max && $a["precio"] > $precio_max) continue;

            $resultados[] = $a;
        }

        // Si no hay resultados, redirigir con error
        if (empty($resultados)) {
            header("Location: ../resultado.php?error=sin_resultados");
            exit;
        }

        // require_once "cabecera.php";
        // require_once "inicio.php";

        echo "<main class='resultados'>";
        echo "<section id='listado'>";
        echo "<h2>Resultado de Búsqueda</h2>";

        foreach ($resultados as $r) {
            echo "<article>";
            echo "<a href='detalle.php?id=" . urlencode($r["id"]) . "'>";
            echo "<img src='" . htmlspecialchars($r["imagen"]) . "' alt='Foto del anuncio'>";
            echo "</a>";
            echo "<h3>" . htmlspecialchars($r["titulo"]) . "</h3>";
            echo "<p>Fecha: " . htmlspecialchars($r["fecha"]) . "</p>";
            echo "<p>Ciudad: " . htmlspecialchars($r["ciudad"]) . "</p>";
            echo "<p>País: " . htmlspecialchars($r["pais"]) . "</p>";
            echo "<p>Precio: " . number_format($r["precio"], 0, ',', '.') . " €</p>";
            echo "<form action='../mensaje.html' method='get'>";
            echo "<button type='submit'>Mensaje</button>";
            echo "</form>";
            echo "</article>";
        }

        echo "</section>";
        echo "</main>";

        // require_once "pie.php";
        exit;

    } else {
        // Si se accede directamente sin formulario
        header("Location: ../index.html?error=incorrect");
        exit;
    }
?>



<!-- el html original:
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Resultado de Busqueda</title>
  <link rel="stylesheet" href="./css/estilo.css">
  <link rel="alternate stylesheet" type="text/css" href="./css/contraste.css" title="Estilo de alto contraste" />
  <link rel="stylesheet" type="text/css" href="./css/impreso.css" media="print" /> 
  <link rel="alternate stylesheet" href="./css/noche.css" title="Estilo modo noche"/>
  <link rel="alternate stylesheet" type="text/css" href="./css/letra_y_contraste.css" title="Alto contraste y letra grande" />
  <link rel="alternate stylesheet" type="text/css" href="./css/letra_grande.css" title="Aumentar Letra" />
  <link rel="stylesheet" href="./css/fontello.css"> link que vincula la carpeta paraponer iconos
</head>
<body>
  <a href="#listado" class="saltar">Saltar al contenido principal</a>

  <header>
    <h1>PI- Pisos e Inmuebles</h1>
    <p id="eslogan">Tu nuevo hogar te espera</p>

        <nav>
            <a href="index_registrado.html">Inicio</a>
            <a href="publicar.html">Publicar anuncio</a>
            <a href="MenuRegistradoUsu.html">Menú de Usuario</a>
        </nav>
  </header>

  <main class="resultados">
    <section id="listado">
      <h2>Resultado de Búsqueda</h2>

      <article>
            <a href="anuncio.html">
              <img src="./img/piso.jpg" alt="Foto de piso 1">
            </a>
            <h3>Título del anuncio</h3>
            <p>Fecha: 2025-09-23</p>
            <p>Ciudad: Madrid</p>
            <p>País: España</p>
            <p>Precio: 250.000 €</p>
            <form action="mensaje.html" method="get">
              <button type="submit">Mensaje</button>
            </form>
          </article>

          <article>
            <a href="error.html">
              <img src="./img/piso.jpg" alt="Foto de piso 2">
            </a>
            <h3>Título del anuncio</h3>
            <p>Fecha: 2025-09-23</p>
            <p>Ciudad: Barcelona</p>
            <p>País: España</p>
            <p>Precio: 200.000 €</p>
            <form action="mensaje.html" method="get">
              <button type="submit">Mensaje</button>
            </form>
          </article>

          <article>
            <img src="./img/piso.jpg" alt="Foto de piso 3">
            <h3>Título del anuncio</h3>
            <p>Fecha: 2025-09-23</p>
            <p>Ciudad: Sevilla</p>
            <p>País: España</p>
            <p>Precio: 180.000 €</p>
            <form action="mensaje.html" method="get">
              <button type="submit">Mensaje</button>
            </form>
          </article>

          <article>
            <img src="./img/piso.jpg" alt="Foto de piso 4">
            <h3>Título del anuncio</h3>
            <p>Fecha: 2025-09-23</p>
            <p>Ciudad: Valencia</p>
            <p>País: España</p>
            <p>Precio: 220.000 €</p>
            <form action="mensaje.html" method="get">
              <button type="submit">Mensaje</button>
            </form>
          </article>

          <article>
            <img src="./img/piso.jpg" alt="Foto de piso 5">
            <h3>Título del anuncio</h3>
            <p>Fecha: 2025-09-23</p>
            <p>Ciudad: Bilbao</p>
            <p>País: España</p>
            <p>Precio: 210.000 €</p>
            <form action="mensaje.html" method="get">
              <button type="submit">Mensaje</button>
            </form>
          </article>
    </section>
    <section id="filtros">
      <h2 id="buscar-heading">Formulario de búsqueda</h2>
      <form method="get" action="./php/resultado.php" aria-labelledby="buscar-heading">
        <fieldset>
          <legend>¿Impresión del precio?</legend>
          <input type="radio" id="tipo_venta" name="tipo_anuncio" value="venta">
          <label for="tipo_venta">Venta</label>
          <input type="radio" id="tipo_alquiler" name="tipo_anuncio" value="alquiler">
          <label for="tipo_alquiler">Alquiler</label>
        </fieldset>



        <label for="tipo_vivienda_select">Seleccione tipo de vivienda</label>
        <select id="tipo_vivienda_select" name="tipo_vivienda">
          <option value="">---</option>
          <option value="obra_nueva">Obra Nueva</option>
          <option value="vivienda">Vivienda</option>
          <option value="oficina">Oficina</option>
          <option value="local">Local</option>
          <option value="garaje20">Garaje</option>
        </select>

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" placeholder="Ej: Alicante">

        <label for="pais">País</label>
        <input type="text" id="pais" name="pais" placeholder="Ej: España">

        <label for="precio_min">Precio mínimo (EUR)</label>
        <input type="number" id="precio_min" name="precio_min" min="0" step="0.01" placeholder="0">

        <label for="precio_max">Precio máximo (EUR)</label>
        <input type="number" id="precio_max" name="precio_max" min="0" step="0.01" placeholder="0">

        <label for="fecha_publicacion">Fecha de publicación</label>
        <input type="date" id="fecha_publicacion" name="fecha_publicacion">
    
        <button type="submit">Buscar</button>
        <button type="reset">Limpiar</button>
        
      </form>
    </section>
  </main>

  <footer>
    <p>© Leigh Garett & Maria Luisa Roca, Grupo 3 de Prácticas</p>
  </footer>
  <script src="./js/micodigo.js"></script>
</body>
</html> -->
