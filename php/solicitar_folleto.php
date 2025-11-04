<?php
// -------------------------------------------------------------
// Página: solicitar_folleto.php
// -------------------------------------------------------------

include "funciones_costes.php";

// Función para generar la tabla de costes
function generar_tabla_costes() {
    $html = '<table class="tabla-costes-dinamica">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th rowspan="2">Número de páginas</th>';
    $html .= '<th rowspan="2">Número de fotos</th>';
    $html .= '<th colspan="2">Blanco y negro</th>';
    $html .= '<th colspan="2">Color</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th>150-300 dpi</th><th>450-900 dpi</th>';
    $html .= '<th>150-300 dpi</th><th>450-900 dpi</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    
    $html .= '<tbody>';
    
    for ($i = 1; $i <= 15; $i++) {
        $numPaginas = $i;
        $numFotos = $i * 3;
        
        $html .= '<tr>';
        $html .= '<td>' . $numPaginas . '</td>';
        $html .= '<td>' . $numFotos . '</td>';
        
        // BN <= 300 dpi
        $coste_bn_300 = calcular_coste_folleto($numPaginas, $numFotos, false, 300);
        // BN > 300 dpi (usar 600 como representativo)
        $coste_bn_900 = calcular_coste_folleto($numPaginas, $numFotos, false, 600);
        
        // Color <= 300 dpi
        $coste_color_300 = calcular_coste_folleto($numPaginas, $numFotos, true, 300);
        // Color > 300 dpi (usar 600 como representativo)
        $coste_color_900 = calcular_coste_folleto($numPaginas, $numFotos, true, 600);
        
        // Formatear con 2 decimales
        $html .= '<td>' . number_format($coste_bn_300, 2, ',', '.') . ' €</td>';
        $html .= '<td>' . number_format($coste_bn_900, 2, ',', '.') . ' €</td>';
        $html .= '<td>' . number_format($coste_color_300, 2, ',', '.') . ' €</td>';
        $html .= '<td>' . number_format($coste_color_900, 2, ',', '.') . ' €</td>';
        
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    return $html;
}

// =============================================================
// Estructura de la página Solicitar Folleto
// =============================================================

$titulo_pagina = "Solicitud folleto";
include "paginas_Estilo.php";
include "header.php";

?>

<main>
    <h2>Solicitud de impresión de folleto publicitario</h2>
    <p>Mediante esta opción puedes solicitar la impresión y envío de uno de tus anuncios a todo color, toda resolución.</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit.</p>

    <h2>Tarifas del folleto</h2>
    <table>
      <caption>Tarifas de impresión</caption>
      <tr><td>Coste procesamiento y envío</td><td>10 €</td></tr>
      <tr><td>&lt; 5 páginas</td><td>2 € por página</td></tr>
      <tr><td>Entre 5 y 10 páginas</td><td>1,8 € por página</td></tr>
      <tr><td>&gt; 10 páginas</td><td>1,6 € por página</td></tr>
      <tr><td>Blanco y Negro</td><td>0 €</td></tr>
      <tr><td>Color</td><td>0,5 € por foto</td></tr>
      <tr><td>Resolución ≤ 300 dpi</td><td>0 € por foto</td></tr>
      <tr><td>Resolución &gt; 300 dpi</td><td>0,2 € por foto</td></tr>
    </table>

    <h2>Tabla de posibles costes</h2>
    <section id="contenedor-tabla-costes-php">
        <?php echo generar_tabla_costes(); ?>
    </section>
    
    <h2>Formulario de solicitud</h2>
    <p>Rellene el siguiente formulario aportando todos los detalles para confeccionar su folleto publicitario.</p>
    <p>Los campos marcados con un asterisco (*) son obligatorios</p>

    <!-- FORMULARIO SIN VALIDACIÓN HTML5 NI JAVASCRIPT -->
    <form id="form-folleto" class="form-folleto" action="respuesta_folleto.php" method="POST" novalidate>
      
      <label for="texto_adicional">Texto Adicional</label>
      <textarea id="texto_adicional" name="texto_adicional" placeholder="Información adicional a la que tiene el propio anuncio"></textarea>
      
      <label for="nombre" class="required">Nombre</label>
      <input type="text" id="nombre" name="nombre" placeholder="su nombre">
      
      <label for="email" class="required">Email</label>
      <input type="text" id="email" name="email" placeholder="suemail@gmail.com">
      
      <label for="direccion" class="required">Dirección</label>
      <input type="text" id="direccion" name="direccion" placeholder="calle">
      
      <label for="numero" class="required">Número</label>
      <input type="text" id="numero" name="numero">
      
      <label for="cp" class="required">CP</label>
      <input type="text" id="cp" name="cp">
      
      <label for="localidad" class="required">Localidad</label>
      <select id="localidad" name="localidad">
        <option value="">---</option>
        <option value="alicante">Alicante</option>
        <option value="valencia">Valencia</option>
        <option value="albacete">Albacete</option>
        <option value="barcelona">Barcelona</option>
      </select>

      <label for="provincia" class="required">Provincia</label>
      <select id="provincia" name="provincia">
        <option value="">---</option>
        <option value="sanvi">San Vicente del Raspeig</option>
        <option value="elche">Elche</option>
        <option value="alicanteP">Alicante</option>
        <option value="sanjuan">San Juan</option>
      </select>
        
      <label for="telefono">Teléfono</label>
      <input type="text" id="telefono" name="telefono" placeholder="Ej: ### ## ## ##">
        
      <label for="color">Color de la Portada</label>
      <input type="text" id="color" name="color" value="#FF0000">

      <label for="paginas">Número de Páginas</label>
      <input type="text" id="paginas" name="paginas" value="1">

      <label for="fotos">Número de Fotos</label>
      <input type="text" id="fotos" name="fotos" value="3">

      <label for="impresion">Impresión (dpi)</label>
      <input type="text" id="impresion" name="impresion" value="150">
        
      <label for="anuncio" class="required">Anuncio</label>
      <select id="anuncio" name="anuncio">
        <option value="">--Seleccione un anuncio--</option>
        <option value="piso">Piso en alquiler</option>
        <option value="casa">Casa en venta</option>
        <option value="garage">Garaje disponible</option>
        <option value="oficina">Oficina en alquiler</option>
      </select>
    
      <label for="fecha_rec">Fecha de Recepción</label>
      <input type="text" id="fecha_rec" name="fecha_rec" placeholder="YYYY-MM-DD">
        
      <fieldset>
        <legend>¿Impresión a Color?</legend>
        <input type="radio" id="color_si" name="impresion_color" value="color" checked>
        <label for="color_si">Color</label>
        <input type="radio" id="color_no" name="impresion_color" value="bn">
        <label for="color_no">Blanco y Negro</label>
      </fieldset>

      <fieldset>
        <legend>¿Impresión del precio?</legend>
        <input type="radio" id="precio_si" name="imprimir_precio" value="si" checked>
        <label for="precio_si">Sí</label>
        <input type="radio" id="precio_no" name="imprimir_precio" value="no">
        <label for="precio_no">No</label>
      </fieldset>
      
      <label for="copias" class="required">Número de Copias</label>
      <input type="text" id="copias" name="copias" value="1">

      <button type="submit">Solicitar</button>
        
    </form>
</main>

<?php
include "footer.php";
?>