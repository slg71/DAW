<?php
// -------------------------------------------------------------
// Página: perfil.php 
// -------------------------------------------------------------

include "sesion_control.php"; // Control central de sesión y cookies
$titulo_pagina = "Perfil"; 
include "paginas_Estilo.php";
include "header.php";

// 1. Incluimos la conexión
include "conexion_bd.php"; 

// 2. Pillar el ID de la URL
// La práctica dice "usuario seleccionado", así que pillo el id de la url 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<main><p>Error, no has dicho de qué usuario.</p></main>";
    include "footer.php";
    exit; // Adiós
}

$id_usuario_perfil = (int)$_GET['id']; // El dueño del perfil que estamos viendo
$id_visitante = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0; // Quien está mirando la web

// 3. Conectar a la BD
$mysqli = conectarBD();
if ($mysqli === null) {
    // La función conectarBD ya debería mostrar un error
    include "footer.php";
    exit;
}

// 4. CONSULTA 1: DATOS DEL USUARIO
// La práctica pide nombre, foto y fecha de incorporación 
$sql_usuario = "SELECT NomUsuario, Foto, FRegistro FROM usuarios WHERE IdUsuario = ?";

$stmt_usuario = mysqli_prepare($mysqli, $sql_usuario);

if ($stmt_usuario === false) {
    echo "<main><p>Error al preparar la consulta de usuario.</p></main>";
    mysqli_close($mysqli);
    include "footer.php";
    exit;
}

// Asociamos el parámetro
mysqli_stmt_bind_param($stmt_usuario, 'i', $id_usuario_perfil);

// Ejecutamos
mysqli_stmt_execute($stmt_usuario);

// Vinculamos los resultados a variables
mysqli_stmt_bind_result($stmt_usuario, $nombre_usuario, $foto_usuario, $fecha_registro);

// Obtenemos los valores
if (!mysqli_stmt_fetch($stmt_usuario)) {
    echo "<main><p>Error: Usuario no encontrado.</p></main>";
    mysqli_stmt_close($stmt_usuario);
    mysqli_close($mysqli);
    include "footer.php";
    exit;
}

// Cerramos esta sentencia para poder hacer otra
mysqli_stmt_close($stmt_usuario);

?>

<main>
    <section id="bloque">
        <h2>Datos del usuario</h2>
        <?php
            // Ponemos los datos que hemos pillado
            echo "<p>Nombre de usuario: " . htmlspecialchars($nombre_usuario) . "</p>";
            echo "<p>Fecha de incorporación: " . htmlspecialchars($fecha_registro) . "</p>";
            
            if (empty($foto_usuario)) {
                $foto_usuario = "perfil.jpg"; // Pongo una por defecto si no tiene
            }
            echo "<p>Foto de perfil: </p>";
            echo '<img src="../img/' . htmlspecialchars($foto_usuario) . '" alt="Foto de ' . htmlspecialchars($nombre_usuario) . '">';
        ?>
    </section>

    
    <section id="listado">
        <h2>Anuncios Publicados por <?php echo htmlspecialchars($nombre_usuario); ?></h2>

        <?php
        // 5. CONSULTA 2: ANUNCIOS DEL USUARIO
        // Pide un "listado simplificado". Saco lo básico.
        $sql_anuncios = "SELECT 
                            IdAnuncio, Titulo, FPrincipal, Ciudad, Precio, FRegistro 
                        FROM anuncios 
                        WHERE Usuario = ? 
                        ORDER BY FRegistro DESC";
        
        $stmt_anuncios = mysqli_prepare($mysqli, $sql_anuncios);
        
        if ($stmt_anuncios === false) {
            echo "<p>Error al preparar los anuncios.</p>";
        } else {
            mysqli_stmt_bind_param($stmt_anuncios, 'i', $id_usuario_perfil);
            mysqli_stmt_execute($stmt_anuncios);
            
            // Vinculamos las columnas que queremos a variables
            mysqli_stmt_bind_result(
                $stmt_anuncios, 
                $anuncio_id,
                $anuncio_titulo,
                $anuncio_foto,
                $anuncio_ciudad,
                $anuncio_precio,
                $anuncio_fecha
            );

            $hay_anuncios = false;
            
            // Recorremos los resultados con fetch
            while (mysqli_stmt_fetch($stmt_anuncios)) {
                $hay_anuncios = true;
                
                // Si soy yo (visitante) el dueño de este perfil -> Voy a ver_anuncio (Privado)
                // Si soy otro usuario -> Voy a detalle_anuncio (Público)
                if ($id_visitante == $id_usuario_perfil) {
                    $enlace_destino = "ver_anuncio.php?id=" . $anuncio_id;
                } else {
                    $enlace_destino = "detalle_anuncio.php?id=" . $anuncio_id;
                }
        ?>

        <article onclick="location.href='<?php echo $enlace_destino; ?>'" style="cursor: pointer;">
            <a href="<?php echo $enlace_destino; ?>">
                <img src="../img/<?php echo htmlspecialchars($anuncio_foto); ?>" 
                     alt="Foto: <?php echo htmlspecialchars($anuncio_titulo); ?>">
            </a>

            <h3><a href="<?php echo $enlace_destino; ?>"><?php echo htmlspecialchars($anuncio_titulo); ?></a></h3>
            <p>Fecha: <?php echo htmlspecialchars($anuncio_fecha); ?></p>
            <p>Ciudad: <?php echo htmlspecialchars($anuncio_ciudad); ?></p>
            <p>Precio: <?php echo number_format($anuncio_precio, 0, ',', '.'); ?> €</p>
        </article>

        <?php 
            } // Fin del while
            
            if (!$hay_anuncios) {
                echo '<p>Este usuario no tiene anuncios publicados.</p>';
            }

            mysqli_stmt_close($stmt_anuncios);
        }
        ?>
    </section>
</main>

<?php
// 6. Cerrar la conexión
mysqli_close($mysqli);

include "footer.php";
?>