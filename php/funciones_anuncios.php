<?php
// ==========================================================
// funciones_anuncios.php - Funciones para obtener detalles de anuncios, fotos y datos de usuario
// ==========================================================

include_once "conexion_bd.php";

function obtener_id_usuario_numerico($session_value) {
    $mysqli = conectarBD();
    if (!$mysqli) return null;

    $id_usuario_numerico = null;

    // Si es un numero lo usamos como IdUsuario
    if (is_numeric($session_value)) {
        $id_usuario_numerico = (int)$session_value;
    } else {
        // Es el NomUsuario y buscamos su IdUsuario en la BD
        $query_id = "SELECT IdUsuario FROM usuarios WHERE NomUsuario = ?";
        if ($stmt_id = $mysqli->prepare($query_id)) {
            $stmt_id->bind_param("s", $session_value);
            $stmt_id->execute();
            $result_id = $stmt_id->get_result();
            if ($row_id = $result_id->fetch_assoc()) {
                $id_usuario_numerico = $row_id['IdUsuario'];
            }
            $stmt_id->close();
        }
    }
    $mysqli->close();
    return $id_usuario_numerico;
}

function obtener_anuncios_usuario($id_usuario) {
    $anuncios = [];
    $mysqli = conectarBD();

    if (!$mysqli) return $anuncios;

    $query = "
        SELECT 
            A.IdAnuncio, A.Titulo, A.FPrincipal, A.Precio, A.FRegistro,
            TA.NomTAnuncio, TV.NomTVivienda
        FROM anuncios AS A
        JOIN tiposanuncios AS TA ON A.TAnuncio = TA.IdTAnuncio
        JOIN tiposviviendas AS TV ON A.TVivienda = TV.IdTVivienda
        WHERE A.Usuario = ?
        ORDER BY A.FRegistro DESC
    ";
    
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $anuncios[] = $row;
        }
        $stmt->close();
    }
    
    $mysqli->close();
    return $anuncios;
}

// ----------------------------------------------------------------------
// FUNCIONES DE DETALLE Y GALERIA
// ----------------------------------------------------------------------

function obtener_detalle_y_fotos_anuncio($id_anuncio) {
    $mysqli = conectarBD();
    if (!$mysqli) return null;

    $datos_anuncio = [];
    $fotos = [];

    // Obtenemos la informacion del anuncio
    $query_anuncio = "
        SELECT 
            A.IdAnuncio, A.Titulo, A.Precio, A.Texto, A.FPrincipal, A.Usuario, 
            A.Superficie, A.NHabitaciones, A.NBanyos, A.Planta, A.Anyo, A.FRegistro,
            TA.NomTAnuncio, TV.NomTVivienda, P.NomPais, U.NomUsuario, U.IdUsuario
        FROM anuncios A
        JOIN tiposanuncios TA ON A.TAnuncio = TA.IdTAnuncio
        JOIN tiposviviendas TV ON A.TVivienda = TV.IdTVivienda
        JOIN paises P ON A.Pais = P.IdPais
        JOIN usuarios U ON A.Usuario = U.IdUsuario
        WHERE A.IdAnuncio = ?
    ";

    if ($stmt_anuncio = $mysqli->prepare($query_anuncio)) {
        $stmt_anuncio->bind_param("i", $id_anuncio);
        $stmt_anuncio->execute();
        $result_anuncio = $stmt_anuncio->get_result();
        
        if ($result_anuncio->num_rows === 1) {
            $datos_anuncio = $result_anuncio->fetch_assoc();
        }
        $stmt_anuncio->close();
    }
    
    if (empty($datos_anuncio)) {
        $mysqli->close();
        return null;
    }

    // Obtenemos lista de fotos secundarias
    $query_fotos = "SELECT IdFoto, Foto, Alternativo, Titulo FROM fotos WHERE Anuncio = ?";
    
    if ($stmt_fotos = $mysqli->prepare($query_fotos)) {
        $stmt_fotos->bind_param("i", $id_anuncio);
        $stmt_fotos->execute();
        $result_fotos = $stmt_fotos->get_result();
        while ($row = $result_fotos->fetch_assoc()) {
            if (!empty($datos_anuncio['FPrincipal']) && $row['Foto'] === $datos_anuncio['FPrincipal']) {
                continue; 
            }
            $fotos[] = $row;
        }
        $stmt_fotos->close();
    }
    
    $mysqli->close();
    
    // Si hay foto principal, la agregamos con IdFoto = 0
    if (!empty($datos_anuncio['FPrincipal'])) {
        array_unshift($fotos, [
            'IdFoto' => 0, 
            'Foto' => $datos_anuncio['FPrincipal'],
            'Alternativo' => $datos_anuncio['Titulo'] . ' - Principal',
            'Titulo' => 'Foto Principal'
        ]);
    }
    
    $datos_anuncio['fotos'] = $fotos;
    return $datos_anuncio;
}


// Mostramos las fotos con paginacion .

function mostrar_galeria_fotos_paginada($anuncio_data, $es_privada, $fotos_por_pagina = 2) {
    if (!$anuncio_data) {
        echo "<section><p>Anuncio no encontrado.</p></section>";
        return;
    }

    $todas_las_fotos = $anuncio_data['fotos'];
    $total_fotos = count($todas_las_fotos);
    $total_paginas = ceil($total_fotos / $fotos_por_pagina);

    $pagina_actual = isset($_GET['pag_foto']) ? (int)$_GET['pag_foto'] : 1;
    if ($pagina_actual < 1) $pagina_actual = 1;
    if ($pagina_actual > $total_paginas && $total_paginas > 0) $pagina_actual = $total_paginas;

    $inicio = ($pagina_actual - 1) * $fotos_por_pagina;
    $fotos_mostrar = array_slice($todas_las_fotos, $inicio, $fotos_por_pagina);

    $enlace_volver = $es_privada ? "ver_anuncio.php?id=" . $anuncio_data['IdAnuncio'] : "ultimos_anuncios.php";
    ?>
    <section class="info-anuncio-basica">
        <h2>Galería de Fotos (Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>)</h2>
        <h3><?php echo htmlspecialchars($anuncio_data['Titulo']); ?></h3>
        <p><a href="<?php echo $enlace_volver; ?>">&larr; Volver al Anuncio</a></p>
    </section>

    <section id="listado-mis-anuncios"> <?php if ($total_fotos > 0): ?>
            <?php foreach ($fotos_mostrar as $foto): ?>
                <article class="tarjeta-anuncio">
                    <figure class="contenedor-miniatura">
                        <?php 
                        //  Generar miniatura 
                        $ruta_thumb = generar_miniatura($foto['Foto'], 800); 
                        $titulo_limpio = preg_replace('/\s\(\d+\)$/', '', $foto['Titulo']);
                        ?>
                        <img src="<?php echo $ruta_thumb; ?>" 
                             alt="<?php echo htmlspecialchars($foto['Alternativo']); ?>"
                             class="img-mis-anuncios">
                        <figcaption><?php echo htmlspecialchars($titulo_limpio); ?></figcaption>
                    </figure>
                    
                    <?php if ($es_privada && $foto['IdFoto'] !== 0): ?>
                        <footer class="acciones-foto">
                            <form action="eliminar_foto.php" method="get">
                                <input type="hidden" name="id_anuncio" value="<?php echo $anuncio_data['IdAnuncio']; ?>">
                                <input type="hidden" name="id_foto" value="<?php echo $foto['IdFoto']; ?>">
                                <button type="submit" class="btn-aceptar">Eliminar</button>
                            </form>
                        </footer>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Este anuncio no tiene fotos.</p>
        <?php endif; ?>
    </section>

    <?php if ($total_paginas > 1): ?>
        <nav class="paginacion-container">
            <?php if ($pagina_actual > 1): ?>
                <a href="?id=<?php echo $anuncio_data['IdAnuncio']; ?>&pag_foto=<?php echo $pagina_actual - 1; ?>" class="btn-pag">Anterior</a>
            <?php endif; ?>
            
            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?id=<?php echo $anuncio_data['IdAnuncio']; ?>&pag_foto=<?php echo $pagina_actual + 1; ?>" class="btn-pag">Siguiente</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
    <?php
}
?>