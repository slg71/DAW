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
        <?php include "bloque_busqueda.php"; ?>

        <?php include "bloque_ultimos_anuncios.php"; ?>
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
        <?php include "bloque_estadisticas.php"; ?>
    </section>
</main>
<?php include "footer.php"; ?>
</body>
</html>