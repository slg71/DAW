<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Página: mis_anuncios.php (AHORA CON BD)
// -------------------------------------------------------------

$titulo_pagina = "Mis Anuncios";

// 1. AÑADIMOS CONEXIÓN
include "conexion_bd.php";

$anuncios = array(); // Array para los anuncios del usuario
$mensaje_error = "";
$usuario_id = $_SESSION['usuario_id']; // El ID del usuario logueado

// DEBUG: Ver qué ID tenemos
//echo "DEBUG: Buscando anuncios del usuario ID: " . $usuario_id . "<br>";

$mysqli = conectarBD();

if ($mysqli) {
    
    // 2. Consulta SQL: Pillar solo los anuncios del usuario
    $sql = "SELECT 
                A.IdAnuncio, A.Titulo, A.FPrincipal, A.Ciudad, A.Precio, A.FRegistro,
                P.NomPais
            FROM anuncios AS A
            LEFT JOIN paises AS P ON A.Pais = P.IdPais
            WHERE A.Usuario = ?
            ORDER BY A.FRegistro DESC";

    $stmt = mysqli_prepare($mysqli, $sql);
    
    if ($stmt) {
        // Bind del usuario_id
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        // Meter los anuncios en el array
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $anuncios[] = $fila;
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $mensaje_error = "Error al consultar tus anuncios.";
    }
    
    mysqli_close($mysqli);
} else {
    $mensaje_error = "Error de conexión a la base de datos.";
}

include "paginas_Estilo.php";
include "header.php";
?>

<main>

    <?php
    // Mostrar error si hay
    if ($mensaje_error != "") {
        echo "<section><p class='error'>" . htmlspecialchars($mensaje_error) . "</p></section>";
    }
    
    if (count($anuncios) == 0) {
        echo '<section id="bloque"><p>Aún no has publicado ningún anuncio.</p><button><a href="crear_anuncio.php">Crea tu primer anuncio</a></button></section>';
    } else {
    ?>

    <section id="listado">
        <h2>MIS ANUNCIOS PUBLICADOS</h2>
        
        <p>Total de anuncios: <?php echo count($anuncios); ?></p>

        <?php foreach ($anuncios as $anuncio): ?>
        
        <article>
            
            <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
              <img src="../img/<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" 
                   alt="Foto principal">
            </a>

            <h3>
                <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                    <?php echo htmlspecialchars($anuncio['Titulo']); ?>
                </a>
            </h3>
            
            <p>Fecha: <?php echo htmlspecialchars($anuncio['FRegistro']); ?></p>
            <p>Ciudad: <?php echo htmlspecialchars($anuncio['Ciudad']); ?></p>
            <p>País: <?php echo htmlspecialchars($anuncio['NomPais']); ?></p>
            <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>

        
            <section id="bloque">
                <button>
                    <a href="añadir_foto.php?anuncio_id=<?php echo $anuncio['IdAnuncio']; ?>">
                    Añadir Foto
                    </a>
                </button>
                <button>
                    <a href="editar_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                        Editar
                    </a>
                </button>
            </section>

                
            <form action="gestion_anuncios.php" method="post">
                <button type="submit" 
                        name="eliminar" 
                        value="<?php echo $anuncio['IdAnuncio']; ?>" 
                        onclick="return confirm('¿Estás seguro de que quieres eliminar este anuncio?');"
                        class="boton-eliminar">
                    Eliminar
                </button>
            </form>
        </article>
        <?php endforeach; ?>
    </section>

    <section id="bloque">
        <button>
            <a href="añadir_foto.php">
                Añadir Foto a anuncio
            </a>
        </button>
        <button>
            <a href="crear_anuncio.php">
                Crear anuncio
            </a>
        </button>
    </section>
    <?php } ?>
</main>

<?php
include "footer.php";
?>