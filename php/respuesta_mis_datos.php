<?php
// -------------------------------------------------------------
// Paagina: respuesta_mis_datos.php
// -------------------------------------------------------------

session_start();
require_once "conexion_bd.php";
require_once "validaciones.php";

// Si no está logueado o no es POST, fuera
if (!isset($_SESSION['usuario_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Recogida de datos
$usuario = trim($_POST["usuario"] ?? "");
$email   = trim($_POST["email"] ?? "");
$sexo    = trim($_POST["sexo"] ?? "");
$nac     = trim($_POST["nac"] ?? "");
$ciudad  = trim($_POST["ciudad"] ?? "");
$pais    = trim($_POST["pais"] ?? "");

// Contraseñas
$pwd_actual = $_POST["pwd_actual"] ?? "";
$pwd_nueva  = $_POST["pwd_nueva"] ?? "";
$pwd_nueva2 = $_POST["pwd_nueva2"] ?? "";

$errores = [];

// ========================================================
// 1. VALIDACIONES COMUNES
// ========================================================

// Usuario
if ($usuario === "") {
    $errores["usuario"] = "El nombre de usuario no puede estar vacío.";
} elseif (!validar_usuario($usuario)) {
    $errores["usuario"] = "Usuario inválido (3-15 chars, letras/num, no empezar por num).";
}

// Email
if ($email === "") {
    $errores["email"] = "El email es obligatorio.";
} elseif (!validar_email($email)) {
    $errores["email"] = "Formato de email incorrecto.";
}

// Fecha y edad
if ($nac === "") {
    $errores["nac"] = "La fecha de nacimiento es obligatoria.";
} elseif (!es_mayor_edad($nac)) {
    $errores["nac"] = "Debes ser mayor de 18 años.";
}

// Otros campos obligatorios
if ($ciudad === "") $errores["ciudad"] = "La ciudad es obligatoria.";
if ($pais === "")   $errores["pais"] = "El país es obligatorio.";
$sexos_validos = ["hombre", "mujer", "otro"];
if (!in_array($sexo, $sexos_validos)) $errores["sexo"] = "Sexo no válido.";


// ========================================================
// 2. VALIDACIÓN DE CONTRASEÑA NUEVA
// ========================================================
$cambiar_clave = false;
$hash_nueva_clave = "";

if ($pwd_nueva !== "") {
    if (!validar_clave($pwd_nueva)) {
        $errores["pwd_nueva"] = "La nueva clave debe tener 6-15 caracteres, mayúsculas, minúsculas y números.";
    } elseif ($pwd_nueva !== $pwd_nueva2) {
        $errores["pwd_nueva2"] = "Las nuevas contraseñas no coinciden.";
    } else {
        $cambiar_clave = true;
        $hash_nueva_clave = password_hash($pwd_nueva, PASSWORD_DEFAULT);
    }
}

// ========================================================
// 3. VERIFICACIÓN DE SEGURIDAD
// ========================================================
$mysqli = conectarBD();

if ($mysqli) {
    // Obtenemos la clave actual de la BD para compararla
    $sql_check = "SELECT Clave FROM usuarios WHERE IdUsuario = ?";
    $stmt = mysqli_prepare($mysqli, $sql_check);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hash_actual_bd);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($pwd_actual === "") {
        $errores["pwd_actual"] = "Debes introducir tu contraseña actual para guardar cambios.";
    } elseif (!password_verify($pwd_actual, $hash_actual_bd)) {
        $errores["pwd_actual"] = "La contraseña actual es incorrecta.";
    }

    // Comprobación de nombre único (si el usuario ha cambiado el nombre)
    // Solo si no hay errores previos en usuario
    if (!isset($errores["usuario"])) {
        $sql_uniq = "SELECT IdUsuario FROM usuarios WHERE NomUsuario = ? AND IdUsuario != ?";
        $stmt_u = mysqli_prepare($mysqli, $sql_uniq);
        mysqli_stmt_bind_param($stmt_u, "si", $usuario, $id_usuario);
        mysqli_stmt_execute($stmt_u);
        mysqli_stmt_store_result($stmt_u);
        if (mysqli_stmt_num_rows($stmt_u) > 0) {
            $errores["usuario"] = "Este nombre de usuario ya está en uso por otra persona.";
        }
        mysqli_stmt_close($stmt_u);
    }

    // ========================================================
    // 4. PROCESAR ERRORES O GUARDAR
    // ========================================================
    
    if (!empty($errores)) {
        mysqli_close($mysqli);
        
        // Guardamos errores y datos en sesión
        $_SESSION['_flash'] = [
            'errors' => $errores,
            'old_input' => $_POST // Para repoblar el formulario
        ];
        
        // Redirigir de vuelta al formulario
        header("Location: mis_datos.php");
        exit;
    } else {
        // Todo correcto -> UPDATE
        // Preparamos valores
        $sexo_num = ($sexo == 'hombre') ? 1 : (($sexo == 'mujer') ? 0 : 2);
        
        if ($cambiar_clave) {
            // Actualizamos todo
            $sql_update = "UPDATE usuarios SET NomUsuario=?, Email=?, Sexo=?, FNacimiento=?, Ciudad=?, Pais=?, Clave=? WHERE IdUsuario=?";
            $stmt_up = mysqli_prepare($mysqli, $sql_update);
            // Tipos: s s i s s i s i
            mysqli_stmt_bind_param($stmt_up, "ssissisi", $usuario, $email, $sexo_num, $nac, $ciudad, $pais, $hash_nueva_clave, $id_usuario);
        } else {
            // Actualizamos sin tocar la clave
            $sql_update = "UPDATE usuarios SET NomUsuario=?, Email=?, Sexo=?, FNacimiento=?, Ciudad=?, Pais=? WHERE IdUsuario=?";
            $stmt_up = mysqli_prepare($mysqli, $sql_update);
            // Tipos: s s i s s i i
            mysqli_stmt_bind_param($stmt_up, "ssissii", $usuario, $email, $sexo_num, $nac, $ciudad, $pais, $id_usuario);
        }

        if (mysqli_stmt_execute($stmt_up)) {
            // Éxito
            mysqli_stmt_close($stmt_up);
            
            // Actualizamos el nombre en la sesión por si cambió
            $_SESSION['usuario_nombre'] = $usuario;
            
            /*si la sesión caduca, el usuario tendrá que loguearse de nuevo porque la cookie antigua tendrá el usuario viejo. */
            mysqli_close($mysqli);
            
            // Redirigimos con mensaje de éxito
            header("Location: mis_datos.php?ok=1");
            exit;
        } else {
            echo "<p>Error SQL al actualizar: " . mysqli_error($mysqli) . "</p>";
            mysqli_close($mysqli);
        }
    }

} else {
    echo "<p>Error de conexión a la base de datos.</p>";
}
?>