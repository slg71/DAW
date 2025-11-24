<?php
session_start();

require_once "conexion_bd.php";
require_once "validaciones.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- Recolección de datos ---
    $usuario = trim($_POST["usuario"] ?? "");
    $pwd = trim($_POST["pwd"] ?? "");
    $pwd2 = trim($_POST["pwd2"] ?? "");
    $sexo = trim($_POST["sexo"] ?? "");
    $nac = trim($_POST["nac"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $pais = trim($_POST["pais"] ?? "");
    $email = trim($_POST["email"] ?? "");

    $errores = [];

    // ============ Validaciones ============

    // --- Validación Usuario ---
    if ($usuario === "") {
        $errores["usuario"] = "El usuario es obligatorio.";
    } elseif (!validar_usuario($usuario)) {
        $errores["usuario"] = "Usuario inválido: 3-15 carácteres, letras/números, no empezar por número.";
    }

    // --- Validación Contraseña (pwd) ---
    if ($pwd === "") {
        $errores["pwd"] = "La contraseña es obligatoria.";
    } elseif (!validar_clave($pwd)) {
        $errores["pwd"] = "La contraseña debe tener 6-15 carácteres, mayúscula, minúscula y número.";
    }

    // --- Validación Repetir Contraseña (pwd2) ---
    if ($pwd !== $pwd2) {
        $errores["pwd2"] = "Las contraseñas no coinciden.";
    }
    
    // --- Validación Email ---
    if ($email === "") {
        $errores["email"] = "El email es obligatorio.";
    } elseif (!validar_email($email)) {
        $errores["email"] = "El formato del email no es válido.";
    }

    // --- Validación Sexo ---
    $sexos_validos = ["hombre", "mujer", "otro"];
    if ($sexo === "") {
        $errores["sexo"] = "Debes seleccionar un sexo.";
    }
    elseif (!in_array($sexo, $sexos_validos)) {
        $errores["sexo"] = "Debes seleccionar un sexo válido.";
    }

    // --- Validación Fecha Nacimiento ---
    if ($nac === "") {
        $errores["nac"] = "La fecha es obligatoria.";
    } elseif (!es_mayor_edad($nac)) {
        $errores["nac"] = "Debes ser mayor de 18 años.";
    }

    // --- Validación País ---
    if ($pais === "") {
        $errores["pais"] = "Debes seleccionar un país.";
    }
    
    // --- Validación Ciudad ---
    if ($ciudad === "") {
        $errores["ciudad"] = "La ciudad es obligatoria.";
    }


    // ============ Flashdata ============
    // Si hay errores, guardamos todo en la "estructura secundaria" _flash
    
    if (!empty($errores)) {
        
        $_SESSION['_flash'] = [
            'errors' => $errores,
            'old_input' => [
                "usuario" => $usuario,
                "email"   => $email,
                "sexo"    => $sexo,
                "pais"    => $pais,
                "ciudad"  => $ciudad,
                "nac"     => $nac
            ]
        ];
        
        header("Location: registro.php");
        exit;
    }

    // ============ Si todo está bien, insertar en bd ============
    $mysqli = conectarBD(); //de conexion_bd.php

    if ($mysqli) {
        //comprobar si existe usuario ya en el bd
        $sql_check = "SELECT IdUsuario FROM usuarios WHERE NomUsuario = ?";

        // Preparamos
        $stmt = mysqli_prepare($mysqli, $sql_check);
        // Vinculamos: "s" significa que el parámetro es un String
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        // Ejecutamos...
        mysqli_stmt_execute($stmt);
        // Guardamos resultado para contar filas
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // El usuario existe
            $_SESSION['_flash']['errors']['usuario'] = "Ese nombre ya está cogido.";
            $_SESSION['_flash']['old_input'] = $_POST;
            
            mysqli_stmt_close($stmt);
            mysqli_close($mysqli);
            header("Location: registro.php");
            exit;
        }

        mysqli_stmt_close($stmt); // Cerramos esta sentencia

        // 2. Insertar nuevo usuario
        // Encriptamos contraseña
        $clave_hash = password_hash($pwd, PASSWORD_DEFAULT);
        
        // Valores por defecto
        $foto_defecto = "perfil.jpg"; 
        $estilo_defecto = 1; // Estilo por defecto (ID 1 en tu base de datos)
        
        // Convertir sexo a número (TinyInt en BD): Hombre=1, Mujer=0, Otro=2
        $sexo_num = 2; 
        if ($sexo == 'hombre') $sexo_num = 1;
        if ($sexo == 'mujer') $sexo_num = 0;

        $sql_insert = "INSERT INTO usuarios (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_ins = mysqli_prepare($mysqli, $sql_insert);

        // Tipos: s=string, i=integer
        // Orden: Nom(s), Clave(s), Email(s), Sexo(i), Nac(s), Ciudad(s), Pais(i), Foto(s), Estilo(i)
        // Total: sssisissi
        mysqli_stmt_bind_param($stmt_ins, "sssisissi", 
            $usuario, $clave_hash, $email, $sexo_num, $nac, $ciudad, $pais, $foto_defecto, $estilo_defecto
        );

        if (mysqli_stmt_execute($stmt_ins)) {
            // Mostramos mensaje de bienvenida
            include "paginas_Estilo.php";
            include "header.php";
            ?>
            <main>
                <section id="bloque">
                    <h2>Registro Completado</h2>
                    <p>Bienvenido, <strong><?php echo htmlspecialchars($usuario); ?></strong>.</p>
                    <p>Tus datos se han guardado correctamente.</p>
                    <br>
                    <a href="login.php">Iniciar Sesión</a>
                </section>
            </main>
            <?php
            include "footer.php";
        } else {
            echo "<p>Error al insertar: " . mysqli_error($mysqli) . "</p>";
        }

        mysqli_stmt_close($stmt_ins);
        mysqli_close($mysqli);
    }else {
    echo "<p>Error de conexión a la Base de Datos.</p>";
}

} else {
    // Si no es POST, redirigimos (sin cambios)
    header("Location: registro.php");
    exit;
}
?>