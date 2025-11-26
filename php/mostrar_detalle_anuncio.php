<?php
// ---------------------------------------------
// Fichero: mostrar_detalle_anuncio.php
// Código común para mostrar detalles
// ---------------------------------------------

// Este fichero espera: $id y $es_privada

include "conexion_bd.php";

$anuncio = null;
$fotos_extra = array();

if ($id > 0) {
    $mysqli = conectarBD();
    if ($mysqli) {
        
        // Consulta del anuncio
        $sql_anuncio = "SELECT 
                            A.Titulo, A.FPrincipal, A.Precio, A.Texto, A.Ciudad, 
                            A.Superficie, A.NHabitaciones, A.NBanyos, A.Planta, A.Anyo, A.FRegistro,
                            P.NomPais,
                            TA.NomTAnuncio,
                            TV.NomTVivienda,
                            U.NomUsuario,
                            U.IdUsuario
                        FROM anuncios AS A
                        LEFT JOIN paises AS P ON A.Pais = P.IdPais
                        LEFT JOIN tiposanuncios AS TA ON A.TAnuncio = TA.IdTAnuncio
                        LEFT JOIN tiposviviendas AS TV ON A.TVivienda = TV.IdTVivienda
                        LEFT JOIN usuarios AS U ON A.Usuario = U.IdUsuario
                        WHERE A.IdAnuncio = ?";

        $stmt_anuncio = mysqli_prepare($mysqli, $sql_anuncio);
        
        if ($stmt_anuncio) {
            mysqli_stmt_bind_param($stmt_anuncio, "i", $id);
            mysqli_stmt_execute($stmt_anuncio);
            $resultado_anuncio = mysqli_stmt_get_result($stmt_anuncio);
            
            if (mysqli_num_rows($resultado_anuncio) == 1) {
                $anuncio = mysqli_fetch_assoc($resultado_anuncio);
                
                // Consulta de fotos
                $sql_fotos = "SELECT Foto, Alternativo FROM fotos WHERE Anuncio = ?";
                $stmt_fotos = mysqli_prepare($mysqli, $sql_fotos);
                
                if ($stmt_fotos) {
                    mysqli_stmt_bind_param($stmt_fotos, "i", $id);
                    mysqli_stmt_execute($stmt_fotos);
                    $resultado_fotos = mysqli_stmt_get_result($stmt_fotos);
                    
                    while ($fila_foto = mysqli_fetch_assoc($resultado_fotos)) {
                        $fotos_extra[] = $fila_foto;
                    }
                    mysqli_stmt_close($stmt_fotos);
                }
            }
            mysqli_stmt_close($stmt_anuncio);
        }
        mysqli_close($mysqli);
    }
}

// Lógica de visualización y enlaces
$num_total_fotos = $anuncio ? (1 + count($fotos_extra)) : 0;
// Determinar la página de destino para la galería:
$galeria_destino = $es_privada ? "ver_fotos_privado.php" : "ver_fotos_publico.php";
?>

<main id="anuncio">
    <?php if ($anuncio): ?>
        <section>
            <h2><?php echo htmlspecialchars($anuncio["Titulo"]); ?></h2>
            <article>
                
                <!-- 1. FOTO PRINCIPAL Y ENLACE A LA GALERÍA -->
                <section>
                    <a 
                        href="<?php echo $galeria_destino; ?>?id=<?php echo $id; ?>" 
                        title="Ver las <?php echo $num_total_fotos; ?> fotos"
                    >
                        <img 
                            src="../img/<?php echo htmlspecialchars($anuncio["FPrincipal"]); ?>" 
                            alt="Foto principal: <?php echo htmlspecialchars($anuncio["Titulo"]); ?>"
                        >
                    </a>
                    <p class="pie-foto">
                        Haz click en la imagen para ver la galería completa (<?php echo $num_total_fotos; ?> fotos)
                    </p>
                </section>
                <!-- FIN FOTO PRINCIPAL Y ENLACE -->

                <h3><?php echo number_format($anuncio["Precio"], 0, ',', '.'); ?> €</h3>
                
                <p><strong>Tipo de anuncio:</strong> <?php echo htmlspecialchars($anuncio["NomTAnuncio"]); ?></p>
                <p><strong>Tipo de vivienda:</strong> <?php echo htmlspecialchars($anuncio["NomTVivienda"]); ?></p>
                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($anuncio["FRegistro"]); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($anuncio["Ciudad"]) . ", " . htmlspecialchars($anuncio["NomPais"]); ?></p>

                <p><?php echo htmlspecialchars($anuncio["Texto"]); ?></p>

                <h4>Características</h4>
                <ul>
                    <li><strong>Superficie:</strong> <?php echo htmlspecialchars($anuncio["Superficie"]); ?> m²</li>
                    <li><strong>Habitaciones:</strong> <?php echo htmlspecialchars($anuncio["NHabitaciones"]); ?></li>
                    <li><strong>Baños:</strong> <?php echo htmlspecialchars($anuncio["NBanyos"]); ?></li>
                    <li><strong>Planta:</strong> <?php echo htmlspecialchars($anuncio["Planta"]); ?></li>
                    <li><strong>Año de construcción:</strong> <?php echo htmlspecialchars($anuncio["Anyo"]); ?></li>
                </ul>

                <section>
                    <!-- Aquí se muestran las fotos extra (la foto principal está arriba) -->
                    <?php
                    // Las fotos extra se muestran como miniaturas si existen.
                    foreach ($fotos_extra as $foto) {
                        echo "<img src='../img/" . htmlspecialchars($foto['Foto']) . "' alt='" . htmlspecialchars($foto['Alternativo']) . "'>";
                    }
                    ?>
                </section>

                <p><strong>Anunciante:</strong> <?php echo htmlspecialchars($anuncio["NomUsuario"]); ?></p>
                
                <?php if ($es_privada): ?>
                    <!-- Botones para el dueño -->
                    <button>
                        <a href="añadir_foto.php?anuncio_id=<?php echo $id; ?>">Añadir Foto</a>
                    </button>
                    <button>
                        <a href="modificar_anuncio.php?id=<?php echo $id; ?>">Modificar Anuncio</a>
                    </button>

                    <button>
                        <a href="mis_anuncios.php">Volver a Mis Anuncios</a>
                    </button>
                <?php else: ?>
                    <!-- Botones para visitantes -->
                    <button>
                        <a href="perfil.php?id=<?php echo $anuncio['IdUsuario']; ?>">
                            Ver perfil del anunciante
                        </a>
                    </button>

                    <form action="mensaje.php" method="get">
                        <button type="submit">Enviar mensaje</button>
                    </form>
                <?php endif; ?>
            </article>
        </section>
    <?php else: ?>
        <section id="bloque">
            <p>El anuncio no existe.</p>
        </section>
    <?php endif; ?>
</main>