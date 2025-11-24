<?php
// validaciones igual q en el JS para las páginas php

// --------- USUARIO ----------
// 3-15 caract, letras/num, no empezar con num
function validar_usuario($usuario) {
    //comprobar longitud
    if (strlen($usuario) < 3 || strlen($usuario) > 15) {
        return false;
    }

    //comprobar si empieza con un numero
    if (is_numeric($usuario[0])) {
        return false;
    }

    //comprobar caract vsalidas con expr regu
    //^ = principio     [a-zA-Z0-9] = letras/num    + = uno o mas   $ = final
    if (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        return false;
    }

    return true;
}

// --------- CONTRASEÑA ----------
// 6-15 caract, letras/num/-_, mayus, minus, num
function validar_clave($pwd) {
    if (strlen($pwd) < 6 || strlen($pwd) > 15) {
        return false;
    }

    if (!preg_match("/^[a-zA-Z0-9\-_]+$/", $pwd)) {
        return false;
    }

    if (!preg_match("/[a-z]/", $pwd)) return false; // Minúscula
    if (!preg_match("/[A-Z]/", $pwd)) return false; // Mayúscula
    if (!preg_match("/[0-9]/", $pwd)) return false; // Número

    return true;
}

// --------- EMAIL ----------
// filter_var
function validar_email($email) {
    if (strlen($email) > 254) {
        return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

// --------- EDAD ----------
// > 18
function es_mayor_edad($fecha) {
    //calcular la diferencia entre
    $fecha_nac = new DateTime($fecha); //cumpleaños
    $hoy = new DateTime(); //fecha actual

    //en años (y)
    $edad = $hoy->diff($fecha_nac)->y;

    if ($edad < 18) {
        return false;
    }
    return true;
}
?>