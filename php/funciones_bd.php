<?php
// ==========================================================
// funciones_bd.php - Funciones de consulta a BD reutilizables
// ==========================================================

// Nos aseguramos de incluir la conexión solo una vez
require_once "conexion_bd.php"; 

/**
 * Obtiene una lista de opciones (ID, Nombre) de cualquier tabla.
 * Usado para rellenar los <select> (desplegables).
 */
function obtener_opciones_bd($tabla, $id_columna, $nombre_columna) {
    $opciones = [];
    $mysqli = conectarBD(); // conectarBD() está en conexion_bd.php
    if (!$mysqli) return $opciones;

    // Sanitizamos nombres de tabla/columna (no se pueden parametrizar)
    // Esto es básico, para producción se necesitaría una lista blanca.
    $tabla = $mysqli->real_escape_string($tabla);
    $id_columna = $mysqli->real_escape_string($id_columna);
    $nombre_columna = $mysqli->real_escape_string($nombre_columna);

    $query = "SELECT $id_columna, $nombre_columna FROM $tabla ORDER BY $nombre_columna";

    if ($resultado = $mysqli->query($query)) {
        while ($fila = $resultado->fetch_assoc()) {
            // Usamos claves genéricas 'id' y 'nombre' para que sea reutilizable
            $opciones[] = [
                'id' => $fila[$id_columna],
                'nombre' => $fila[$nombre_columna]
            ];
        }
        $resultado->free();
    } else {
        error_log("Error al consultar la tabla $tabla: " . $mysqli->error);
    }
    $mysqli->close();
    return $opciones;
}
?>