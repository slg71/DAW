<?php
// 1. Iniciar sesion
session_start();

$titulo_pagina = "Últimos Anuncios Visitados";
include "paginas_Estilo.php";
include "header.php";

// 2. Usamos la conexión a BD en vez de datos estáticos
require_once "conexion_bd.php"; 
?>

<main>
    <h2>Últimos Anuncios Visitados</h2>

    <?php
    $nombre_cookie = 'ultimos_anuncios';
    $lista_ids = [];

    // 3. Leer la cookie
    if (isset($_COOKIE[$nombre_cookie])) {
        $lista_ids = json_decode($_COOKIE[$nombre_cookie], true);
        if (!is_array($lista_ids)) {
            $lista_ids = [];
        }
        // Invertimos para ver el más reciente primero
        $lista_ids = array_reverse($lista_ids);
    }

    // 4. Mostrar listado si hay datos
    if (!empty($lista_ids)) {
        
        $mysqli = conectarBD(); // Conectar a BD

        if ($mysqli) {
            echo '<section id="listado">';

            foreach ($lista_ids as $id_anuncio) {
                $id_anuncio = intval($id_anuncio);
                if ($id_anuncio <= 0) continue;

                // Consultamos los datos reales de este anuncio
                $sql = "SELECT IdAnuncio, Titulo, Precio, Ciudad, FPrincipal 
                        FROM anuncios 
                        WHERE IdAnuncio = ?";
                
                // Usamos sentencias preparadas (Técnica PDF 4.3.2)
                $stmt = mysqli_prepare($mysqli, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
                    mysqli_stmt_execute($stmt);
                    $resultado = mysqli_stmt_get_result($stmt);
                    
                    // Si existe el anuncio, lo dibujamos
                    if ($fila = mysqli_fetch_assoc($resultado)) {
                        ?>
                        <article onclick="location.href='detalle_anuncio.php?id=<?php echo $id_anuncio; ?>'" style="cursor: pointer;">
                            
                            <img src="../img/<?php echo htmlspecialchars($fila['FPrincipal']); ?>" 
                                 alt="Foto anuncio <?php echo $id_anuncio; ?>">
                            
                            <div class="info-anuncio">
                                <h3><?php echo htmlspecialchars($fila['Titulo']); ?></h3>
                                
                                <p>
                                    <?php echo htmlspecialchars($fila['Ciudad']); ?> | 
                                    <?php echo number_format($fila['Precio'], 0, ',', '.'); ?> €
                                </p>
                            </div>

                            <a href="detalle_anuncio.php?id=<?php echo $id_anuncio; ?>">
                                <button>Ver de nuevo</button>
                            </a>

                        </article>
                        <?php
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            echo '</section>';
            mysqli_close($mysqli);

        } else {
            echo '<section id="bloque"><p>Error de conexión a la base de datos.</p></section>';
        }

    } else {
        echo '<section id="bloque">';
        echo '<p>Aún no has visitado ningún anuncio. ¡Explora nuestro catálogo!</p>';
        // Enlace dinámico según si está logueado
        if (isset($_SESSION['usuario_id'])) {
            echo '<a href="inicio_registrado.php">Ir al Inicio</a>';
        } else {
            echo '<a href="index.php">Ir al Inicio</a>';
        }
        echo '</section>';
    }
    ?>
</main>

<?php
include "footer.php";
?>