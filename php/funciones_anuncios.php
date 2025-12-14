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
            <p><a href="<?php echo $enlace_volver; ?>">&larr; <?php echo $enlace_volver_texto; ?></a></p>
        </section>

        <section class="galeria-fotos">
            <?php if ($num_total_fotos > 0): ?>
                <?php foreach ($anuncio_data['fotos'] as $foto): ?>
                    <div class="contenedor-foto">
                        <img 
                            src="../img/<?php echo htmlspecialchars($foto['Foto']); ?>" 
                            alt="<?php echo htmlspecialchars($foto['Alternativo']); ?>"
                            title="<?php echo htmlspecialchars($foto['Titulo']); ?>"
                            class="foto-galeria"
                        >
                        <p class="titulo-foto"><?php echo htmlspecialchars($foto['Titulo']); ?></p>

                        <!-- Botón de eliminar solo si es vista privada -->
                        <?php if ($es_privada): ?>
                            <form action="eliminar_foto.php" method="get">
                                <input type="hidden" name="id_anuncio" value="<?php echo $anuncio_data['IdAnuncio']; ?>">
                                <input type="hidden" name="id_foto" value="<?php echo $foto['IdFoto']; ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Este anuncio no tiene fotos actualmente.</p>
            <?php endif; ?>
        </section>
    </main>
    <?php
}
?>