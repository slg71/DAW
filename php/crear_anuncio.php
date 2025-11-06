<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: Crear un anuncio nuevo
// -------------------------------------------------------------


$titulo_pagina = "Crear Nuevo Anuncio"; 
include "paginas_Estilo.php";
include "header.php";       
?>
    
<main>
    <h2>Publica tu Anuncio</h2>
    
    <form action="mis_anuncios.php" method="POST">
        

        <label for="titulo">Título del anuncio:</label>
        <input type="text" id="titulo" name="titulo" maxlength="80" required>

        <label for="contrato">Tipo de Contrato:</label>
        <select id="contrato" name="tipo_contrato" required>
            <option value="" disabled selected>Selecciona un tipo</option>
            <option value="venta">Venta</option>
            <option value="alquiler">Alquiler</option>
        </select>

        <label for="descripcion">Descripción del anuncio:</label>
        <textarea id="descripcion" name="descripcion" rows="10" cols="50" required></textarea>

        <label for="pais">País:</label>
        <input type="text" id="pais" name="pais" required>

        <label for="ciudad">Ciudad:</label>
        <input type="text" id="ciudad" name="ciudad" required>

        <label for="metros">Metros Cuadrados (m²):</label>
        <input type="number" id="metros" name="metros_cuadrados" min="1" required>

        <label for="habitaciones">Número de Habitaciones:</label>
        <input type="number" id="habitaciones" name="num_habitaciones" min="0" required>

        <label for="tipo_vivienda">Tipo de Vivienda:</label>
        <select id="tipo_vivienda" name="tipo_vivienda" required>
            <option value="" disabled selected>Selecciona un tipo</option>
            <option value="piso">Piso</option>
            <option value="casa">Casa/Chalet</option>
            <option value="apartamento">Apartamento</option>
            <option value="estudio">Estudio</option>
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