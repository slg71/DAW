<?php
session_start(); // Inicia la sesión
ob_start();

// Comprueba si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// ---------------------------------------------
// Página: detalle.php (Con BD de verdad)
// ---------------------------------------------

$titulo_pagina = "Detalle del anuncio";
include "paginas_Estilo.php";
include "header.php";

// 1. AÑADIMOS LA CONEXIÓN
include "conexion_bd.php"; 

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$anuncio = null; // Para guardar el anuncio
$fotos_extra = []; // Para las fotos de la tabla 'fotos'

if ($id > 0) {
    $mysqli = conectarBD(); // Conectamos
    if ($mysqli) {
        
        // 2. CONSULTA 1: Pillar los datos del anuncio y del usuario
        // La práctica pide todo, así que hay que cruzar tablas
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

        // Usamos el método procedural del PDF (pág 28)
        $stmt_anuncio = mysqli_prepare($mysqli, $sql_anuncio);
        
        if ($stmt_anuncio) {
            mysqli_stmt_bind_param($stmt_anuncio, "i", $id);
            mysqli_stmt_execute($stmt_anuncio);
            
            $resultado_anuncio = mysqli_stmt_get_result($stmt_anuncio); 
            
            if (mysqli_num_rows($resultado_anuncio) == 1) {
                $anuncio = mysqli_fetch_assoc($resultado_anuncio);
                
                // 3. CONSULTA 2: Pillar las fotos extra
                $sql_fotos = "SELECT Foto, Alternativo FROM fotos WHERE Anuncio = ?";
                $stmt_fotos = mysqli_prepare($mysqli, $sql_fotos);
                
                if ($stmt_fotos) {
                    mysqli_stmt_bind_param($stmt_fotos, "i", $id);
                    mysqli_stmt_execute($stmt_fotos);
                    $resultado_fotos = mysqli_stmt_get_result($stmt_fotos);
                    
                    // Metemos las fotos en un array
                    while ($fila_foto = mysqli_fetch_assoc($resultado_fotos)) {
                        $fotos_extra[] = $fila_foto;
                    }
                    mysqli_stmt_close($stmt_fotos);
                }
            }
            mysqli_stmt_close($stmt_anuncio);
        }
        mysqli_close($mysqli); // Cerramos conexión
    }
}


// Lógica de Cookies
if ($anuncio !== null) {
    $nombre_cookie = 'ultimos_anuncios';
    $lista_visitados = [];

    if (isset($_COOKIE[$nombre_cookie])) {
        $lista_visitados = json_decode($_COOKIE[$nombre_cookie], true);
        if (!is_array($lista_visitados)) $lista_visitados = [];
    }
    $lista_visitados = array_diff($lista_visitados, [$id]);
    array_unshift($lista_visitados, $id);
    $lista_visitados = array_slice($lista_visitados, 0, 4);
    setcookie($nombre_cookie, json_encode($lista_visitados), time() + (7 * 24 * 60 * 60), "/");
}
?>

<main id="anuncio">
    <?php if ($anuncio): // Si la consulta SQL funcionó ?>
        <section>
            <h2><?php echo htmlspecialchars($anuncio["Titulo"]); ?></h2>
            <article>
                <img src="../img/<?php echo htmlspecialchars($anuncio["FPrincipal"]); ?>" alt="Foto principal">

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
                    <?php
                    // Usamos el array $fotos_extra
                    foreach ($fotos_extra as $foto) {
                        echo "<img src='../img/" . htmlspecialchars($foto['Foto']) . "' alt='" . htmlspecialchars($foto['Alternativo']) . "'>";
                    }
                    ?>
                </section>

                <p><strong>Anunciante:</strong> <?php echo htmlspecialchars($anuncio["NomUsuario"]); ?></p>
                
                <button>
                    <a href="perfil.php?id=<?php echo $anuncio['IdUsuario']; ?>" >
                        Ver perfil del anunciante
                    </a>
                </button>

                <form action="mensaje.php" method="get" style="margin-top: 20px;">
                    <button type="submit">Enviar mensaje</button>
                </form>
            </article>
        </section>
    <?php else: ?>
        <section id="bloque">
            <p>El anuncio no existe.</p>
        </section>
    <?php endif; ?>
</main>

<?php
include "footer.php";
ob_end_flush();
?>