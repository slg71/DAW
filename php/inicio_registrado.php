<?php
// -------------------------------------------------------------
// Página: inicio_registrado.php
// -------------------------------------------------------------
require_once "sesion_control.php";

$titulo_pagina = "Inicio - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";

//conexion a la base de datos
require_once("conexion_bd.php"); 
$usuario_registrado = isset($_SESSION['usuario_id']);

include "funciones_imagenes.php";
?>

<?php
// ==============================================================
// Dar consejo del dia con JSON
// ==============================================================

//leer el fichero entero en una cadena
$contenido_json = file_get_contents("../ficheros/consejos.json");
//y convertirlo en un array php
$lista_consejos = json_decode($contenido_json, true);

//elegir una posicion al azar
$indice_azar_consejo = array_rand($lista_consejos);//devuelve como id no el valor
$consejo_del_dia = $lista_consejos[$indice_azar_consejo];

// ==============================================================
// Escoger un anuncio con TXT
// ==============================================================

//leer el fichero linea a lineaa enu n array
$lineas_fichero = @file("../ficheros/anuncios_seleccionados.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);//@ pa q no se pete

$anuncio_escogido_info = null;//pa guardar los datos del anuncio
$mensaje_experto = "";
$nombre_experto = "";

$mysqli = conectarBD();

if ($lineas_fichero && $mysqli) {
    shuffle($lineas_fichero);//mezclo el array pa q sea aleatorio

    foreach ($lineas_fichero as $linea) {
        //divido cada parte de la linea q está por |
        $partes = explode("|", $linea);

        if (count($partes) >= 3) {//3 porq cada linea tiene 3 datos (id|experto|comentario)
            $id_candidato = trim($partes[0]);
            $experto = trim($partes[1]);
            $comentario = trim($partes[2]);

            //consulto bd pa ver si existe el anuncio
            $sql_check = "SELECT IdAnuncio, Titulo, FPrincipal, Precio, Ciudad FROM anuncios WHERE IdAnuncio = $id_candidato";

            if ($res = $mysqli->query($sql_check)) {
                if ($fila = $res->fetch_assoc()) {
                    //guardo datos y paro de buscar (break)
                    $anuncio_escogido_info = $fila;
                    $nombre_experto = $experto;
                    $mensaje_experto = $comentario;
                    break; 
                }
            }
        }
    }
}
?>

<main class="resultados">
    <a href="#listado" class="saltar">Saltar al contenido principal</a>

    <section>
        <section id="busqueda">
            <h2>Búsqueda rápida</h2>
            <form action="resultado.php" method="get">
                <fieldset>
                    <legend>Datos de búsqueda</legend>
                    <input type="text" id="buscar" name="buscar" placeholder="Ej: vivienda alquiler alicante">
                </fieldset>
                <button type="submit"><i class="icon-search"></i>Buscar</button>
            </form>
        </section>

        <section id="listado">
            <h2>Últimos Anuncios publicados</h2>

            <?php
            // ==============================================================
            // Obtener los ultimos 5 anuncios de la BD
            // ==============================================================
            $anuncios = [];
            
            // Reutilizamos la conexión si sigue abierta, si no, conectamos
            if (!$mysqli || !$mysqli->ping()) {
                $mysqli = conectarBD();
            }

            if ($mysqli) {
                $sentencia = "
                    SELECT
                        A.IdAnuncio,
                        A.Titulo,
                        A.Precio,
                        A.FPrincipal AS Foto,
                        A.Ciudad,
                        P.NomPais AS Pais,
                        A.FRegistro AS FechaPublicacion
                    FROM 
                        ANUNCIOS A
                    JOIN 
                        PAISES P ON A.Pais = P.IdPais
                    ORDER BY 
                        A.FRegistro DESC
                    LIMIT 5
                ";
                
                if ($resultado = $mysqli->query($sentencia)) {
                    while ($fila = $resultado->fetch_assoc()) {
                        $anuncios[] = $fila;
                    }
                    $resultado->free();
                } else {
                    echo "<p class='error-bd'>Error al consultar anuncios: " . $mysqli->error . "</p>";
                    error_log("Error al obtener anuncios en index.php: " . $mysqli->error);
                }
                
                $mysqli->close(); // Cerramos la conexión
            }

            // ==============================================================
            // Mostrar ultimos 5 anuncios publicados
            // ==============================================================
            if (!empty($anuncios)): 
                foreach ($anuncios as $anuncio):
                    $fecha_formato = (new DateTime($anuncio['FechaPublicacion']))->format('d/m/Y');
                    $precio_formato = number_format($anuncio['Precio'], 2, ',', '.') . ' €';
            ?>
                    <article onclick="location.href='<?php echo $usuario_registrado ? 'detalle_anuncio.php?id=' . $anuncio['IdAnuncio'] : 'login.php'; ?>'" 
                             style="cursor: pointer;">
                        
                        <img src="../img/<?php echo htmlspecialchars($anuncio['Foto']); ?>" 
                             alt="Foto principal de <?php echo htmlspecialchars($anuncio['Titulo']); ?>"
                             onerror="this.onerror=null; this.src='../img/placeholder.jpg';"> 
                        
                        <details>
                            <summary><h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3></summary>
                            <p>
                                <strong><?php echo $precio_formato; ?></strong> | 
                                <?php echo htmlspecialchars($anuncio['Ciudad']); ?>, 
                                <?php echo htmlspecialchars($anuncio['Pais']); ?> (<?php echo $fecha_formato; ?>)
                            </p>
                            <?php if (!$usuario_registrado): ?>
                                 <p>Acceso restringido — Debes iniciar sesión.</p>
                            <?php endif; ?>
                            
                        </details>
                    </article>
            <?php 
                endforeach;
            else:
            ?>
                <section id="bloque" class="sin-anuncios">
                    <p>No se encontraron anuncios recientes en la base de datos.</p>
                    <a href="registro.php">Regístrate para ver más opciones</a>
                </section>
            <?php
            endif;
            ?>
        </section>
    </section>

    <section id="filtros">
        <h2>Consejo del día</h2>

        <article>
            <h3>Tip: <?php echo $consejo_del_dia['categoria']; ?></h3>
            <p>
                Importancia: <strong><?php echo $consejo_del_dia['importancia']; ?></strong>
            </p>
            <hr>
            <p><em>"<?php echo $consejo_del_dia['descripcion']; ?>"</em></p>
        </article>

        <?php if ($anuncio_escogido_info): ?>
        <h2>Selección del experto</h2>
        <article>
            <a href="detalle_anuncio.php?id=<?php echo $anuncio_escogido_info['IdAnuncio']; ?>">
                <img src="../img/<?php echo $anuncio_escogido_info['FPrincipal']; ?>" 
                     alt="Foto anuncio experto">
            </a>
            
            <h3>
                <a href="detalle_anuncio.php?id=<?php echo $anuncio_escogido_info['IdAnuncio']; ?>">
                    <?php echo $anuncio_escogido_info['Titulo']; ?>
                </a>
            </h3>
            
            <p>
                <strong><?php echo $nombre_experto; ?></strong> dice:
                <br>
                <em>"<?php echo $mensaje_experto; ?>"</em>
            </p>
            
            <p>
                <?php echo $anuncio_escogido_info['Ciudad']; ?> 
                <br>
                <strong><?php echo number_format($anuncio_escogido_info['Precio'], 0, ',', '.'); ?> €</strong>
            </p>
        </article>
        <?php endif; ?>
        <h2>Estadísticas de la semana</h2>
            <p>Fotos subidas en los últimos 7 días:</p>
            
            <?php
            //QUERY SQL AQUI!!!!
            //por ahora utilizo datos inventados para hacer pruebas
            $datos_simulados = [
                'Lun' => 5,
                'Mar' => 12,
                'Mie' => 8,
                'Jue' => 20,
                'Vie' => 15,
                'Sab' => 45,
                'Dom' => 3
            ];
            
            // Llamo a mi función
            $grafico_base64 = generar_grafico($datos_simulados);
            ?>
            
            <article>
                <img src="<?php echo $grafico_base64; ?>" alt="Gráfico de barras generado con GD">
            </article>
    </section>
</main>
<?php include "footer.php"; ?>
</body>
</html>