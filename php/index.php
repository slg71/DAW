<?php
// -------------------------------------------------------------
// Página: index.php (version publica, sin sesion)
// -------------------------------------------------------------
session_start();

$titulo_pagina = "Inicio - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";

//conexion a la base de datos
require_once("conexion_bd.php"); 
$usuario_registrado = isset($_SESSION['usuario_id']);
?>

<main>
    <a href="#listado" class="saltar">Saltar al contenido principal</a>

    <section id="busqueda">
        <h2>Búsqueda rápida</h2>
        <form action="resultado.php" method="get">
            <fieldset>
                <legend>Datos de búsqueda</legend>
                <input type="text" id="buscar" name="buscar" placeholder="Ej: vivienda alquiler alicante">
            </fieldset>
            <button type="submit"><i class="icon-search"></i>Buscar</button>
        </form>
    </section>

    <section id="listado">
        <h2>Últimos Anuncios publicados</h2>

        <?php
        // ==============================================================
        // Obtener los ultimos 5 anuncios de la BD
        // ==============================================================
        $anuncios = [];
        $mysqli = conectarBD();

        if ($mysqli) {
            $sentencia = "
                SELECT
                    A.IdAnuncio,
                    A.Titulo,
                    A.Precio,
                    A.FPrincipal AS Foto,
                    A.Ciudad,
                    P.NomPais AS Pais,
                    A.FRegistro AS FechaPublicacion
                FROM 
                    ANUNCIOS A
                JOIN 
                    PAISES P ON A.Pais = P.IdPais
                ORDER BY 
                    A.FRegistro DESC
                LIMIT 5
            ";
            
            if ($resultado = $mysqli->query($sentencia)) {
                while ($fila = $resultado->fetch_assoc()) {
                    $anuncios[] = $fila;
                }
                $resultado->free();
            } else {
                echo "<p class='error-bd'>Error al consultar anuncios: " . $mysqli->error . "</p>";
                error_log("Error al obtener anuncios en index.php: " . $mysqli->error);
            }
            
            $mysqli->close(); // Cerramos la conexión
        }

        // ==============================================================
        // Mostrar ultimos 5 anuncios publicados
        // ==============================================================
        if (!empty($anuncios)): 
            foreach ($anuncios as $anuncio):
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