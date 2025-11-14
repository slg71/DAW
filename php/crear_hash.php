<?php
// ======================================================
// crear_hash.php - PARA GENERAR HASHES
// primordialmente para contraseñas
// ======================================================

// 1. Texto a encriptar
$contraseña_plana = '1111';

// 2. Generar el hash
$hash_generado = password_hash($contraseña_plana, PASSWORD_DEFAULT);

// 3. Mostrar el hash
echo "<h1>Generador de Hashes</h1>";
echo "<p>Contraseña Plana: <b>" . htmlspecialchars($contraseña_plana) . "</b></p>";

// Usamos <pre> para que sea fácil de copiar
echo "<pre style='background:#eee; padding:10px; border:1px solid #ccc;'>" . htmlspecialchars($hash_generado) . "</pre>";


// 4. Verificar el hash INMEDIATAMENTE
$verificacion = password_verify($contraseña_plana, $hash_generado);

echo "<hr>";
echo "<h2>Verificación Automática:</h2>";
echo "<p>¿El hash generado coincide con la contraseña plana? ";

if ($verificacion) {
    echo "<b style='color:green;'>SÍ (bool(true))</b></p>";
    echo "<p style='color:green; font-weight:bold;'>¡Este hash es correcto y está listo para usarse!</p>";
} else {
    echo "<b style='color:red;'>NO (bool(false))</b></p>";
    echo "<p style='color:red; font-weight:bold;'>¡Algo salió mal! Revisa tu versión de PHP.</p>";
}

?>