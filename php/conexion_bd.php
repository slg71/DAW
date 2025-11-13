<?php
// ==========================================================
// conexion_bd.php — Conexion a la base de datos
// ==========================================================

$config = parse_ini_file("config.ini", true);//archivo de configuracion
$db_config = $config['BD'];

// Definimos las constantes de conexión
define("DB_SERVER", $db_config['Server']);
define("DB_USER", $db_config['User']);
define("DB_PASSWORD", $db_config['Password']);
define("DB_DATABASE", $db_config['Database']);

function conectarBD() {
    // establecer la conexion
    $mysqli = @new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

    // Verificamos si hay error de conexion
    if ($mysqli->connect_errno) {
        // Registramos el error en el log del servidor
        error_log("Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
        
        echo "<p> Error de conexión a la base de datos. Por favor, revise la configuración.</p>";
        return null;
    }

    // Configura conjunto de caracteres a utf8mb4
    if (!$mysqli->set_charset("utf8mb4")) {
        error_log("Error cargando el conjunto de caracteres utf8mb4: " . $mysqli->error);
    }

    return $mysqli;
}
?>