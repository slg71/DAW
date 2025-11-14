<?php
// ==========================================================
// funciones_anuncios.php - Funciones para obtener detalles de anuncios, fotos y datos de usuario
// ==========================================================

include_once "conexion_bd.php";

function obtener_id_usuario_numerico($session_value) {
    $mysqli = conectarBD();
    if (!$mysqli) return null;

    $id_usuario_numerico = null;

    // Si es un numero lo usamos como IdUsuario.
    if (is_numeric($session_value)) {
        $id_usuario_numerico = (int)$session_value;
    } else {
        // Es el NomUsuario y buscamos su IdUsuario en la BD, consultamos
        $query_id = "SELECT IdUsuario FROM usuarios WHERE NomUsuario = ?";
        if ($stmt_id = $mysqli->prepare($query_id)) {
            $stmt_id->bind_param("s", $session_value);
            $stmt_id->execute();
            $result_id = $stmt_id->get_result();
            if ($row_id = $result_id->fetch_assoc()) {
                $id_usuario_numerico = $row_id['IdUsuario'];
            }
            $stmt_id->close();
        } else {
             error_log("Error al preparar la consulta de IdUsuario: " . $mysqli->error);
        }
    }
    $mysqli->close();
    return $id_usuario_numerico;
}

function obtener_anuncios_usuario($id_usuario) {
    $anuncios = [];
    $mysqli = conectarBD();

    if (!$mysqli) return $anuncios;

    // Consulta para obtener los datos del listado
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
    } else {
        error_log("Error al preparar la consulta de anuncios de usuario: " . $mysqli->error);
    }
    
    $mysqli->close();
    return $anuncios;
}

// ----------------------------------------------------------------------
// FUNCIONES DE DETALLE Y GALERIA
// ----------------------------------------------------------------------

function obtener_detalle_y_fotos_anuncio($id_anuncio) {
    $mysqli = conectarBD();
    if (!$mysqli) {
        return null;
    }

    $datos_anuncio = [];
    $fotos = [];

    // obtenemos la informacion del anuncio
    $query_anuncio = "
        SELECT 
            A.IdAnuncio, A.Titulo, A.Precio, A.Texto, A.FPrincipal, A.Usuario, A.Superficie, A.NHabitaciones, A.NBanyos, A.Planta, A.Anyo, A.FRegistro,
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
    } else {
        error_log("Error al preparar la consulta de detalle del anuncio: " . $mysqli->error);
        $mysqli->close();
        return null;
    }
    
    if (empty($datos_anuncio)) {
        $mysqli->close();
        return null;
    }


    // obtenemos listas de fotos secundarias del anuncio
    $query_fotos = "
        SELECT IdFoto, Foto, Alternativo, Titulo
        FROM fotos
        WHERE Anuncio = ?
    ";
    
    if ($stmt_fotos = $mysqli->prepare($query_fotos)) {
        $stmt_fotos->bind_param("i", $id_anuncio);
        $stmt_fotos->execute();
        $result_fotos = $stmt_fotos->get_result();
        
        while ($row = $result_fotos->fetch_assoc()) {
            $fotos[] = $row;
        }
        $stmt_fotos->close();
    } else {
        error_log("Error al preparar la consulta de fotos: " . $mysqli->error);
    }
    
    $mysqli->close();
    
    // Agregamos la foto principal al inicio de la lista de fotos
    array_unshift($fotos, [
        'IdFoto' => 0, 
        'Foto' => $datos_anuncio['FPrincipal'],
        'Alternativo' => $datos_anuncio['Titulo'] . ' - Principal',
        'Titulo' => 'Foto Principal'
    ]);
    
    $datos_anuncio['fotos'] = $fotos;
    return $datos_anuncio;
}

function mostrar_galeria_fotos($anuncio_data, $es_privada) {
    if (!$anuncio_data) {
        echo "<main><p>Anuncio no encontrado o ID no valido.</p></main>";
        return;
    }

    $num_total_fotos = count($anuncio_data['fotos']);
    $enlace_volver = $es_privada ? "ver_anuncio.php?id=" . $anuncio_data['IdAnuncio'] : "ultimos_anuncios.php";
    $enlace_volver_texto = $es_privada ? "Volver al Anuncio" : "Volver a Últimos Anuncios";
    
    ?>
    <main>
        <section class="info-anuncio-basica">
            <h2>Galería de Fotos del Anuncio #<?php echo htmlspecialchars($anuncio_data['IdAnuncio']); ?></h2>
            <h3><?php echo htmlspecialchars($anuncio_data['Titulo']); ?></h3>
            <p><strong>Tipo de Anuncio:</strong> <?php echo htmlspecialchars($anuncio_data['NomTAnuncio']); ?></p>
            <p><strong>Tipo de Vivienda:</strong> <?php echo htmlspecialchars($anuncio_data['NomTVivienda']); ?></p>
            <p><strong>País:</strong> <?php echo htmlspecialchars($anuncio_data['NomPais']); ?></p>
            <p><strong>Precio:</strong> <?php echo number_format($anuncio_data['Precio'], 2, ',', '.'); ?> €</p>
            <p><strong>Total de Fotos:</strong> <?php echo $num_total_fotos; ?></p>
            <p><a href="<?php echo $enlace_volver; ?>">&larr; <?php echo $enlace_volver_texto; ?></a></p>
        </section>

        <section class="galeria-fotos">
            <?php foreach ($anuncio_data['fotos'] as $foto): ?>
                <div class="contenedor-foto">
                    <img 
                        src="../img/<?php echo htmlspecialchars($foto['Foto']); ?>" 
                        alt="<?php echo htmlspecialchars($foto['Alternativo']); ?>"
                        title="<?php echo htmlspecialchars($foto['Titulo']); ?>"
                        class="foto-galeria"
                    >
                    <p class="titulo-foto"><?php echo htmlspecialchars($foto['Titulo']); ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <?php
}
?>