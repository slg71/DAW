<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    
    header('Location: login.php');
    exit; 
}

// -------------------------------------------------------------
// Página: respuesta_folleto.php
// -------------------------------------------------------------

require_once "funciones_costes.php";

// $mapa_localidades = [
//     'alicante' => 'Alicante', 'valencia' => 'Valencia', 'albacete' => 'Albacete', 'barcelona' => 'Barcelona'
// ];
// $mapa_provincias = [
//     'sanvi' => 'San Vicente del Raspeig', 'elche' => 'Elche', 'alicanteP' => 'Alicante', 'sanjuan' => 'San Juan'
// ];
// $mapa_anuncios = [
//     'piso' => 'Piso en alquiler', 'casa' => 'Casa en venta', 'garage' => 'Garaje disponible', 'oficina' => 'Oficina en alquiler'
// ];

// =============================================================
// PROCESAMIENTO DE LA SOLICITUD
// =============================================================

// se ha enviado el formulario por POST si no, fuera
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: solicitar_folleto.php");
    exit;
}

// recoger datos del formulario
$nombre = htmlspecialchars($_POST['nombre'] ?? 'N/A');
$email = htmlspecialchars($_POST['email'] ?? 'N/A');
$direccion = htmlspecialchars($_POST['direccion'] ?? '');
$numero = htmlspecialchars($_POST['numero'] ?? '');
$cp = htmlspecialchars($_POST['cp'] ?? '');
$localidad_key = $_POST['localidad'] ?? '';
$provincia_key = $_POST['provincia'] ?? '';
$telefono = htmlspecialchars($_POST['telefono'] ?? 'N/A');
$texto_adicional = htmlspecialchars($_POST['texto_adicional'] ?? 'Ninguno');
$fecha_rec = htmlspecialchars($_POST['fecha_rec'] ?? 'N/A');

//valores ficticios
$num_paginas_anuncio = 5;
$num_fotos_anuncio = 9;  

// Datos elegidos por el usuario
$resolucion = (int)($_POST['impresion'] ?? 150);
$impresion_color_val = ($_POST['impresion_color'] ?? 'bn') === 'color';
$copias = (int)($_POST['copias'] ?? 1);

// LÓGICA DEL COSTE
// Coste Unitario de los valores ficticios y los datos del usuario
$coste_unitario = calcular_coste_folleto(
    $num_paginas_anuncio, 
    $num_fotos_anuncio,   
    $impresion_color_val,
    $resolucion
);


$coste_final = $coste_unitario * $copias;

$localidad = $mapa_localidades[$localidad_key] ?? 'N/A';
$provincia = $mapa_provincias[$provincia_key] ?? 'N/A';
$anuncio_texto = $mapa_anuncios[$_POST['anuncio'] ?? ''] ?? 'N/A';
$impresion_color_texto = $impresion_color_val ? 'Sí (Color)' : 'No (Blanco y Negro)';
$imprimir_precio_texto = ($_POST['imprimir_precio'] ?? 'no') === 'si' ? 'Sí' : 'No';

$dir_completa = trim($direccion . ($direccion && $numero ? ' Nº ' . $numero : '') . ($cp ? ' CP ' . $cp : '')) ?: 'N/A';

// =============================================================
// HTML DE LA PAGINA DE RESPUESTA
// =============================================================

$titulo_pagina = "Solicitud Registrada";
require_once "paginas_Estilo.php";
require_once "header.php";
?>

<main>
    <h1>Solicitud de Folleto Registrada con Éxito</h1>
    <p>¡Gracias, **<?php echo $nombre; ?>**! Tu solicitud de folleto publicitario ha sido procesada. A continuación, se detallan los parámetros de impresión y el coste final del pedido.</p>

    <section>
        <h2>Detalles del Folleto e Impresión</h2>
        <table class="tabla-detalles-solicitud">
            <caption>Resumen de la solicitud</caption>
            
            <tr><td>Anuncio Seleccionado</td><td><?php echo $anuncio_texto; ?></td></tr>
            <tr><td>Impresión a Color</td><td><?php echo $impresion_color_texto; ?></td></tr>
            <tr><td>Resolución de Impresión</td><td><?php echo $resolucion; ?> dpi</td></tr>
            <tr><td>Imprimir Precio del Anuncio</td><td><?php echo $imprimir_precio_texto; ?></td></tr>
            <tr><td>Número de Copias</td><td><?php echo $copias; ?></td></tr>
            
            <!-- Datos ficticios el cal base -->
            <tr class="datos-coste-base"><td>*Páginas del anuncio (Base)</td><td><?php echo $num_paginas_anuncio; ?></td></tr>
            <tr class="datos-coste-base"><td>*Fotos del anuncio (Base)</td><td><?php echo $num_fotos_anuncio; ?></td></tr>
        </table>
    </section>


    <section class="coste-final">
        <h2>Coste Total del Pedido</h2>
        <p class="precio-total">
            Coste Total por <?php echo $copias; ?> copia<?php echo $copias > 1 ? 's' : ''; ?>: 
            <strong><?php echo number_format($coste_final, 2, ',', '.'); ?> €</strong>
            <!--number_format(numero, decimales, separador_decimal, separador_miles)-->
        </p>
    </section>
    
    <section>
        <h2>Datos de Recepción y Contacto</h2>
        <p>Los folletos serán enviados a:</p>
        <address>
            <strong><?php echo $nombre; ?></strong><br>
            <?php echo $dir_completa; ?><br>
            <?php echo $localidad; ?>, <?php echo $provincia; ?><br>
            Teléfono: <?php echo $telefono; ?><br>
            Email: <?php echo $email; ?>
        </address>
        <p>Fecha de recepción solicitada: **<?php echo $fecha_rec; ?>**</p>
        <?php if ($texto_adicional !== 'Ninguno'): ?>
            <p><strong>Texto Adicional:</strong> <?php echo $texto_adicional; ?></p>
        <?php endif; ?>
    </section>

</main>

<?php
require_once "footer.php";
?>
