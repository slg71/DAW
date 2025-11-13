<?php
// -------------------------------------------------------------
// index.php 
// -------------------------------------------------------------
session_start();

$titulo_pagina = "Inicio - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";

require_once("conexion_bd.php"); //conexion a la base de datos
$usuario_registrado = isset($_SESSION['usuario_id']);

// ====================================================================
// 1. OBTENER DATOS DE TABLAS MAESTRAS PARA DROPDOWNS
// ====================================================================

$tipos_anuncio = [];
$tipos_vivienda = [];
$paises_select = []; 
$anuncios = [];

$mysqli = conectarBD();

if ($mysqli) {
    // --- Obtener Tipos de Anuncios ---
    $sentencia_anuncio = "SELECT IdTAnuncio, NomTAnuncio FROM TIPOSANUNCIOS ORDER BY NomTAnuncio";
    if ($resultado = $mysqli->query($sentencia_anuncio)) {
        while ($fila = $resultado->fetch_assoc()) {
            $tipos_anuncio[] = $fila;
        }
        $resultado->free();
    }
    
    // --- Obtener Tipos de Viviendas ---
    $sentencia_vivienda = "SELECT IdTVivienda, NomTVivienda FROM TIPOSVIVIENDAS ORDER BY NomTVivienda";
    if ($resultado = $mysqli->query($sentencia_vivienda)) {
        while ($fila = $resultado->fetch_assoc()) {
            $tipos_vivienda[] = $fila;
        }
        $resultado->free();
    }

    // --- Obtener Países ---
    $sentencia_pais = "SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC";
    if ($resultado = $mysqli->query($sentencia_pais)) {
        while ($fila = $resultado->fetch_assoc()) {
            $paises_select[] = $fila;
        }
        $resultado->free();
    }

    // ==============================================================
    // 2. LÓGICA: Obtener los últimos 5 anuncios de la BD
    // ==============================================================
    
    $sentencia_anuncios_recientes = "
        SELECT
            A.IdAnuncio, A.Titulo, A.Precio, A.FPrincipal AS Foto, A.Ciudad,
            P.NomPais AS Pais, A.FRegistro AS FechaPublicacion
        FROM 
            ANUNCIOS A
        JOIN 
            PAISES P ON A.Pais = P.IdPais
        ORDER BY 
            A.FRegistro DESC
        LIMIT 5
    ";
    
    if ($resultado = $mysqli->query($sentencia_anuncios_recientes)) {
        while ($fila = $resultado->fetch_assoc()) {
            $anuncios[] = $fila;
        }
        $resultado->free();
    } else {
        error_log("Error al obtener anuncios en index.php: " . $mysqli->error);
    }
    
    $mysqli->close(); // Cerramos la conexión después de todas las consultas
}
?>

<main>
    <a href="#listado" class="saltar">Saltar al contenido principal</a>

    <section id="busqueda">
        <h2>Búsqueda Básica</h2>
        
        <!-- FORMULARIO DE BÚSQUEDA BÁSICA CON DESPLEGABLES DE BD -->
        <form action="resultado.php" method="get">
            <fieldset>
                <legend>Filtros principales</legend>
                
                <!-- TIPO DE ANUNCIO (Alquiler/Venta) -->
                <label for="tipo_anuncio">Tipo de Anuncio</label>
                <select id="tipo_anuncio" name="tipo_anuncio">
                    <option value="">Cualquiera</option>
                    <?php foreach ($tipos_anuncio as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo['IdTAnuncio']); ?>">
                            <?php echo htmlspecialchars($tipo['NomTAnuncio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- TIPO DE VIVIENDA (Oficina/Local/Garaje/Vivienda) -->
                <label for="tipo_vivienda">Tipo de Vivienda</label>
                <select id="tipo_vivienda" name="tipo_vivienda">
                    <option value="">Cualquiera</option>
                    <?php foreach ($tipos_vivienda as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo['IdTVivienda']); ?>">
                            <?php echo htmlspecialchars($tipo['NomTVivienda']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- PAÍS -->
                <label for="pais">País</label>
                <select id="pais" name="pais">
                    <option value="">Cualquiera</option>
                    <?php foreach ($paises_select as $pais): ?>
                        <option value="<?php echo htmlspecialchars($pais['IdPais']); ?>">
                            <?php echo htmlspecialchars($pais['NomPais']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- CAMPO DE TEXTO PARA CIUDAD/PALABRAS CLAVE -->
                <label for="buscar">Ciudad o Palabras Clave</label>
                <input type="text" id="buscar" name="buscar" placeholder="Ej: Alicante, garaje o ático">
                
            </fieldset>
            <button type="submit"><i class="icon-search"></i>Buscar</button>
        </form>        
    </section>

    <section id="listado">
        <h2>Últimos Anuncios publicados</h2>

        <?php
        // ==============================================================
        // VISUALIZACIÓN DE ANUNCIOS RECIENTES
        // ==============================================================
        if (!empty($anuncios)): 
            foreach ($anuncios as $anuncio):
                // Formateo de fecha y precio
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
</main>
<?php include "footer.php"; ?>
</body>
</html>