<?php
session_start();

// Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------------------
// Pagina: respuesta_folleto.php
// -------------------------------------------------------------

require_once "conexion_bd.php";
require_once "funciones_costes.php";

// =============================================================
// PARAMETRO DE FOTOS POR PAGINA
// =============================================================
define('FOTOS_POR_PAGINA', 3); // Parámetro modificable solicitado

$mysqli = conectarBD();
$errores = [];
$exito = false;

// Variables de resultado
$coste_total = 0;
$num_paginas = 0;
$num_fotos_total = 0;
$mensaje_resultado = "";

// Inicializar variables del formulario
$anuncio_id = 0;
$nombre = '';
$email = '';
$direccion_completa = '';
$telefono = '';
$color_portada = '';
$copias = 1;
$resolucion = 150;
$fecha_rec = null;
$es_color = 0;
$imprimir_precio = 0;
$texto_adicional = '';
$titulo_anuncio = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // OBTENER DATOS DEL FORMULARIO
    $anuncio_id = isset($_POST['anuncio']) ? intval($_POST['anuncio']) : 0;
    $texto_adicional = isset($_POST['texto_adicional']) ? trim($_POST['texto_adicional']) : '';
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    // Concatenamos la direcciOn para guardarla en un solo campo de texto en la BD
    $calle = $_POST['direccion'] ?? '';
    $num = $_POST['numero'] ?? '';
    $cp = $_POST['cp'] ?? '';
    $loc = $_POST['localidad'] ?? '';
    $prov = $_POST['provincia'] ?? '';
    
    // Construimos la direcciOn completa
    $partes_direccion = array_filter([$calle, $num, $cp, $loc, $prov]); 
    $direccion_completa = implode(", ", $partes_direccion);
    
    $telefono = $_POST['telefono'] ?? '';
    $color_portada = $_POST['color'] ?? '#000000';
    $copias = isset($_POST['copias']) ? intval($_POST['copias']) : 1;
    $resolucion = isset($_POST['impresion']) ? intval($_POST['impresion']) : 150;
    
    $fecha_rec = !empty($_POST['fecha_rec']) ? $_POST['fecha_rec'] : null; 
    
    // Radio buttons
    $es_color = (isset($_POST['impresion_color']) && $_POST['impresion_color'] == 'color') ? 1 : 0;
    $imprimir_precio = (isset($_POST['imprimir_precio']) && $_POST['imprimir_precio'] == 'si') ? 1 : 0;

    // VALIDACIONES PHP
    if ($anuncio_id <= 0) $errores[] = "Debes seleccionar un anuncio válido.";
    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($email)) $errores[] = "El email es obligatorio.";
    if (empty($calle)) $errores[] = "La dirección es obligatoria.";
    if ($copias < 1) $errores[] = "Debes solicitar al menos 1 copia.";

    // CALCULOS AUTOMATICOS
    if (empty($errores)) {
        
        // A) Obtener datos del anuncio y contar foto principal
        // Verificamos que el anuncio pertenezca al usuario logueado
        $sql_princ = "SELECT Titulo, FPrincipal FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?";
        $stmt_p = $mysqli->prepare($sql_princ);
        
        if ($stmt_p) {
            $stmt_p->bind_param("ii", $anuncio_id, $_SESSION['usuario_id']);
            $stmt_p->execute();
            $res_p = $stmt_p->get_result();
            $row_p = $res_p->fetch_assoc();
            $stmt_p->close();
            
            if (!$row_p) {
                $errores[] = "El anuncio seleccionado no existe o no te pertenece.";
            } else {
                $titulo_anuncio = $row_p['Titulo'];
                
                // Si tiene foto principal, suma 1
                if (!empty($row_p['FPrincipal'])) {
                    $num_fotos_total++;
                }

                // Contar fotos secundarias en tabla 'fotos'
                $sql_sec = "SELECT COUNT(*) as total FROM fotos WHERE Anuncio = ?";
                $stmt_s = $mysqli->prepare($sql_sec);
                if ($stmt_s) {
                    $stmt_s->bind_param("i", $anuncio_id);
                    $stmt_s->execute();
                    $res_s = $stmt_s->get_result();
                    $row_s = $res_s->fetch_assoc();
                    $num_fotos_total += $row_s['total'];
                    $stmt_s->close();
                }

                // Calcular páginas: ceil(fotos / parametro)
                if ($num_fotos_total == 0) {
                    // Si no hay fotos, asumimos al menos 1 pagina de texto
                    $num_paginas = 1;
                } else {
                    $num_paginas = ceil($num_fotos_total / FOTOS_POR_PAGINA);
                }

                // calcular coste
                $coste_unitario = calcular_coste_folleto($num_paginas, $num_fotos_total, ($es_color == 1), $resolucion);//en funcion_costes
                $coste_total = $coste_unitario * $copias;
            }
        } else {
            $errores[] = "Error de conexión al verificar el anuncio.";
        }
    }

    // INSERTAR EN LA BD
    if (empty($errores)) {
        $sql_insert = "INSERT INTO solicitudes 
            (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, Coste) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
        $stmt = $mysqli->prepare($sql_insert);
        if ($stmt) {
        
            $stmt->bind_param("issssssiisiid", 
                $anuncio_id, $texto_adicional, $nombre, $email, $direccion_completa, $telefono, 
                $color_portada, $copias, $resolucion, $fecha_rec, $es_color, $imprimir_precio, $coste_total
            );

            if ($stmt->execute()) {
                $exito = true;
                $mensaje_resultado = "Solicitud guardada correctamente.";
            } else {
                $errores[] = "Error al guardar en la base de datos: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errores[] = "Error al preparar la inserción: " . $mysqli->error;
        }
    }
} else {
    // Si intentan entrar directo sin POST
    header("Location: solicitar_folleto.php");
    exit;
}

$mysqli->close();

$titulo_pagina = "Confirmación Solicitud";
require_once "paginas_Estilo.php";
require_once "header.php";
?>

<main>
    <h2>Estado de la Solicitud</h2>

    <?php if ($exito): ?>
        <section class="exito">
            <h3>¡Solicitud registrada con éxito!</h3>
            <p>Gracias, <strong><?php echo htmlspecialchars($nombre); ?></strong>. Hemos procesado tu pedido para el anuncio "<?php echo htmlspecialchars($titulo_anuncio); ?>".</p>
            
            <table class="tabla-detalles-solicitud">
                <caption>Resumen de costes calculados</caption>
                <tr>
                    <th>Concepto</th>
                    <th>Detalle</th>
                </tr>
                <tr>
                    <td>Total Fotos detectadas</td>
                    <td><?php echo $num_fotos_total; ?></td>
                </tr>
                <tr>
                    <td>Páginas calculadas</td>
                    <td><?php echo $num_paginas; ?> (a razón de <?php echo FOTOS_POR_PAGINA; ?> fotos/pág)</td>
                </tr>
                <tr>
                    <td>Copias solicitadas</td>
                    <td><?php echo $copias; ?></td>
                </tr>
                <tr>
                    <td>Impresión a Color</td>
                    <td><?php echo $es_color ? 'Sí' : 'No'; ?></td>
                </tr>
                <tr>
                    <td>Dirección de envío</td>
                    <td><?php echo htmlspecialchars($direccion_completa); ?></td>
                </tr>
                <tr>
                    <td><strong>COSTE TOTAL</strong></td>
                    <td><strong><?php echo number_format($coste_total, 2, ',', '.'); ?> €</strong></td>
                </tr>
            </table>

            <button><a href="mis_anuncios.php">Volver a mis anuncios</a></button>
        </section>

    <?php else: ?>
        <section class="error">
            <h3>No se pudo procesar la solicitud</h3>
            <p>Por favor, revisa los siguientes errores:</p>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>
                <button onclick="history.back()">Volver al formulario</button>
            </p>
        </section>
    <?php endif; ?>
</main>

<?php require_once "footer.php"; ?>
