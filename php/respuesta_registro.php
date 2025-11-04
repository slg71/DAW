<?php
// ======================================================
// respuesta_registro.php — Validación múltiple como en JS
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger los campos del formulario
    $usuario = trim($_POST["usuario"] ?? "");
    $pwd = trim($_POST["pwd"] ?? "");
    $pwd2 = trim($_POST["pwd2"] ?? "");
    $sexo = trim($_POST["sexo"] ?? "");
    $nac = trim($_POST["nac"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $pais = trim($_POST["pais"] ?? "");
    $email = trim($_POST["email"] ?? "");

    // Array para acumular errores
    $errores = [];

    // ============ Validaciones ============
    if ($usuario === "" || $pwd === "" || $pwd2 === "" || $sexo === "" || $nac === "" || $ciudad === "" || $pais === "" || $email === "") {
        $errores["general"] = "Debes rellenar todos los campos obligatorios.";
    }

    if ($usuario !== "") {
        if (strlen($usuario) < 3 || strlen($usuario) > 15) {
            $errores["usuario"] = "El usuario debe tener entre 3 y 15 caracteres.";
        } elseif (is_numeric($usuario[0])) {
            $errores["usuario"] = "El usuario no puede comenzar con un número.";
        } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
            $errores["usuario"] = "El usuario solo puede contener letras y números.";
        }
    }

    if ($pwd !== "") {
        if (strlen($pwd) < 6 || strlen($pwd) > 15) {
            $errores["pwd"] = "La contraseña debe tener entre 6 y 15 caracteres.";
        } elseif (!preg_match("/^[A-Za-z0-9\-_]+$/", $pwd)) {
            $errores["pwd"] = "La contraseña solo puede contener letras, números, guion o guion bajo.";
        } elseif (!preg_match("/[A-Z]/", $pwd) || !preg_match("/[a-z]/", $pwd) || !preg_match("/[0-9]/", $pwd)) {
            $errores["pwd"] = "La contraseña debe tener al menos una mayúscula, una minúscula y un número.";
        }
    }

    if ($pwd2 === "") {
        $errores["pwd2"] = "Debes repetir la contraseña.";
    } elseif ($pwd !== $pwd2) {
        $errores["pwd2"] = "Las contraseñas no coinciden.";
    }

    if ($email !== "") {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores["email"] = "El email no tiene un formato válido.";
        } elseif (strlen($email) > 254) {
            $errores["email"] = "La dirección de email no puede superar los 254 caracteres.";
        }
    }

    $sexos_validos = ["hombre", "mujer", "otro"];
    if ($sexo !== "" && !in_array($sexo, $sexos_validos)) {
        $errores["sexo"] = "Debes seleccionar un sexo válido.";
    }

    if ($nac !== "") {
        $fechaNac = DateTime::createFromFormat('Y-m-d', $nac);
        if (!$fechaNac) {
            $errores["nac"] = "La fecha de nacimiento no es válida.";
        } else {
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNac)->y;
            if ($edad < 18) {
                $errores["nac"] = "Debes tener al menos 18 años.";
            }
        }
    }

    if ($pais === "") {
        $errores["pais"] = "Debes seleccionar un país.";
    }

    // ============ Si hay errores ============
    if (!empty($errores)) {
        // Serializamos los errores y datos en JSON y los pasamos en GET
        $query = http_build_query([
            "errores" => json_encode($errores),
            "usuario" => $usuario,
            "email" => $email,
            "sexo" => $sexo,
            "pais" => $pais,
            "ciudad" => $ciudad,
            "nac" => $nac
        ]);
        header("Location: registro.php?$query");
        exit;
    }

    // ============ Si todo está bien ============
    include "paginas_Estilo.php";
    include "header.php";
    ?>
    <main>
        <section id="bloque">
            <h2>Registro completado con éxito</h2>
            <p>Bienvenido/a, <strong><?php echo htmlspecialchars($usuario); ?></strong>.</p>
            <p>Tu cuenta ha sido registrada correctamente.</p>
        </section>
    </main>
    <?php
    include "footer.php";
} else {
    header("Location: registro.php");
    exit;
}
?>
