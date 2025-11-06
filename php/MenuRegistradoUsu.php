<?php
session_start(); // Inicia o reanuda la sesión

// Comprueba si la variable de sesión 'usuario_id' NO está definida
if (!isset($_SESSION['usuario_id'])) {
    // Si no lo está, redirige al usuario a la página de login
    header('Location: login.php');
    exit; // Detiene la ejecución del script
}

// -------------------------------------------------------------
// Página: menuRegistradoUsu.php (usuario autenticado simulado)
// -------------------------------------------------------------

$titulo_pagina = "Menú (Usuario) - PI Pisos & Inmuebles";
include "paginas_Estilo.php";
include "header.php";


?>

  <main id="usuariomenu">
    <h2>Opciones</h2>
    <nav aria-label="Menú principal de usuario">
      <a href="inicio_registrado.php"><i class="icon-home"></i>Inicio</a>
      <a href="../error.html"><i class="icon-pencil"></i> Modificar mis datos</a>
      <a href="../error.html"><i class="icon-user-delete-outline"></i>Darme de baja</a>
      <a href="mis_anuncios.php"><i class="icon-eye"></i>Visualizar mis anuncios</a>
      <a href="crear_anuncio.php"><i class="icon-plus-outline"></i>Crear un anuncio nuevo</a>
      <a href="mismensajes.php"><i class="icon-mail"></i>Ver mensajes enviados y recibidos</a>
      <a href="solicitar_folleto.php"><i class="icon-doc"></i>Solicitar folleto publicitario impreso</a>
      <a href="salir.php"><i class="icon-logout"></i>Cerrar Sesión</a>
    </nav>
  </main>
<?php include "footer.php"; ?>
</body>
</html>
