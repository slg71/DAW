<?php
// -------------------------------------------------------------
// Página: respuesta_registro.php
// -------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //pillo los valores
    $usuario = trim($_POST["usuario"] ?? "");
    $pwd = trim($_POST["pwd"] ?? "");
    $pwd2 = trim($_POST["pwd2"] ?? "");

    if ($usuario == "" || $pwd == "" || $pwd2 == "") {
        // Redirigir de vuelta con error de campos vacíos 
        header("Location: ../registro.html?error=empty");
        exit; // [cite: 570]
    }

    if ($pwd !== $pwd2) {
        // Redirigir de vuelta con error de campos que no coinciden
        header("Location: ../registro.html?error=pwd_nocoinciden");
        exit; // [cite: 570]
    }

//plantilla

include "paginas_Estilo.php";
include "header.php";
?>

<main id="bloque">
    <h1>¡Registro completado con éxito!</h1>
    <section>
        <h2>Tus datos de registro son:</h2>
        <ul>
            <li>Usuario: <?php echo htmlspecialchars($usuario); ?></li>
            <li>Contraseña: <?php echo htmlspecialchars($pwd); ?></li>
            <p><a href="../index_registrado.html">Ir a Inicio</a></p>
        </ul>
    </section>
</main>

<?php
include "footer.php";
}else {
    // Si se accede directamente al script sin enviar el formulario
    header("Location: ../registro.html");
    exit;
}
?>
