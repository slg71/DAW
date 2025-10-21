/*=================================VALIDACIÓN FORMULARIOS=================================*/

// Función de ayuda rápida para obtener elementos por ID
function $(id) {
    return document.getElementById(id);
}

// Esperar a que el documento esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Selecciona el formulario de login si existe
    const formLogin = document.querySelector("#login form");
    if (formLogin) {
        formLogin.addEventListener("submit", validarLogin);
    }

    // Selecciona el formulario de registro si existe
    const formReg = document.querySelector("#registro form");
    if (formReg) {
        formReg.addEventListener("submit", validarRegistro);
    }
});


/*==================funcion login====================*/
function validarLogin(event) {
    //pillo los valores sin espacios y tabulaciones
    const usuario = $("usuario").value.trim();
    const pwd = $("pwd").value.trim();

    //bandera de si llega a validar
    let valido = true;
    //el mensaje a devolver ya sea error o confirmación
    let mensaje = "";

    //reiniciar estilos de bordes de campos por error
    $("usuario").style.border = "";
    $("pwd").style.border = "";

    //usuario
    if (usuario === "") {
        mensaje += "- El campo 'Usuario' no puede estar vacío.\n";
        $("usuario").style.border = "2px solid red";
        valido = false;
    } else if (usuario.length < 8) {
        mensaje += "- El usuario debe tener al menos 8 caracteres.\n";
        $("usuario").style.border = "2px solid red";
        valido = false;
    }

    //contraseña
    if (pwd === "") {
        mensaje += "- El campo 'Contraseña' no puede estar vacío.\n";
        $("pwd").style.border = "2px solid red";
        valido = false;
    }

    if (!valido) {
        alert("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        event.preventDefault(); //pa no enviarlo
    } else {
        alert("Inicio de sesión correcto. Redirigiendo a Inicio...");
    }
}


/*==================funcion registro====================*/
function validarRegistro(event) {
    //pillo los valores sin espacios y tabulaciones
    const usuario = $("usuario").value.trim();
    const pwd = $("pwd").value.trim();
    const pwd2 = $("pwd2").value.trim();
    const sexo = $("sexo").value.trim();
    const nac = $("nac").value.trim();
    const ciudad = $("ciudad").value.trim();
    const pais = $("pais").value;
    const email = $("email").value.trim();
    const foto = $("foto").value.trim();

    //bandera de si llega a validar
    let valido = true;
    //el mensaje a devolver ya sea error o confirmación
    let mensaje = "";

    //reiniciar estilos de bordes de campos por error
    $("usuario").style.border = "";
    $("pwd").style.border = "";
    $("pwd2").style.border = "";
    $("sexo").style.border = "";
    $("nac").style.border = "";
    $("ciudad").style.border = "";
    $("pais").style.border = "";
    $("email").style.border = "";
    $("foto").style.border = "";

    //usuario
    if (usuario === "") {
        mensaje += "- El campo 'Usuario' no puede estar vacío.\n";
        $("usuario").style.border = "2px solid red";
        valido = false;
    } else {
        if (usuario.length < 3 || usuario.length > 15) {
            mensaje += "- El nombre de usuario debe tener entre 3 y 15 caracteres.\n";
            $("usuario").style.border = "2px solid red";
            valido = false;
        }
        //todas las letras y numeros para compararlo en usuario
        const letras = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const numeros = "0123456789";

        if (numeros.includes(usuario.charAt(0))) {
            mensaje += "- El usuario no puede comenzar con un número.\n";
            $("usuario").style.border = "2px solid red";
            valido = false;
        }

        for (let i = 0; i < usuario.length; i++) {
            const caracter = usuario.charAt(i);
            if (!letras.includes(caracter) && !numeros.includes(caracter)) {
                mensaje += "- El usuario solo puede contener letras o números.\n";
                $("usuario").style.border = "2px solid red";
                valido = false;
            }
        }
    }

    //contraseña
    if (pwd === "") {
        mensaje += "- El campo 'Contraseña' no puede estar vacío.\n";
        $("pwd").style.border = "2px solid red";
        valido = false;
    } else {
        if (pwd.length < 6 || pwd.length > 15) {
            mensaje += "- La contraseña debe tener entre 6 y 15 caracteres.\n";
            $("pwd").style.border = "2px solid red";
            valido = false;
        }

        const mayus = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const minus = "abcdefghijklmnopqrstuvwxyz";
        const numeros = "0123456789";
        const permitidos = "-_";
        let tieneMayus = false, tieneMinus = false, tieneNum = false;
        let caracteresValidos = true;

        for (let i = 0; i < pwd.length; i++) {
            const caracter = pwd.charAt(i);
            if (mayus.includes(caracter)) tieneMayus = true;
            else if (minus.includes(caracter)) tieneMinus = true;
            else if (numeros.includes(caracter)) tieneNum = true;
            else if (!permitidos.includes(caracter)) caracteresValidos = false;
        }

        if (!caracteresValidos) {
            mensaje += "- La contraseña solo puede contener letras, números, guion o guion bajo.\n";
            $("pwd").style.border = "2px solid red";
            valido = false;
        }

        if (!tieneMayus || !tieneMinus || !tieneNum) {
            mensaje += "- La contraseña debe tener al menos una mayúscula, una minúscula y un número.\n";
            $("pwd").style.border = "2px solid red";
            valido = false;
        }
    }

    //repetir contraseña
    if (pwd2 === "") {
        mensaje += "- Debes repetir la contraseña.\n";
        $("pwd2").style.border = "2px solid red";
        valido = false;
    } else if (pwd !== pwd2) {
        mensaje += "- Las contraseñas no coinciden.\n";
        $("pwd2").style.border = "2px solid red";
        valido = false;
    }

    //email
    if (email === "") {
        mensaje += "- El campo 'Dirección de email' no puede estar vacío.\n";
        $("email").style.border = "2px solid red";
        valido = false;
    } else {
        if (!email.includes("@") || email.startsWith("@") || email.endsWith("@")) {
            mensaje += "- El email debe tener el formato parte-local@dominio.\n";
            $("email").style.border = "2px solid red";
            valido = false;
        } else {
            const partes = email.split("@");//dividirlo en dos donde haya @
            const local = partes[0];//primera parte
            const dominio = partes[1];//seguunda
            if (local.length < 1 || dominio.length < 1) {
                mensaje += "- El email debe tener una parte local y un dominio válidos.\n";
                $("email").style.border = "2px solid red";
                valido = false;
            } else if (email.length > 254) {
                mensaje += "- La dirección de email no puede superar los 254 caracteres.\n";
                $("email").style.border = "2px solid red";
                valido = false;
            }
        }
    }

    //sexo
    if (sexo === "") {
        mensaje += "- Debes seleccionar un sexo.\n";
        $("sexo").style.border = "2px solid red";
        valido = false;
    }

    //fecha de nacimiento
    if (nac === "") {
        mensaje += "- Debes indicar tu fecha de nacimiento.\n";
        $("nac").style.border = "2px solid red";
        valido = false;
    } else {
        const fechaNac = new Date(nac);
        const hoy = new Date();

        if (isNaN(fechaNac)) {
            mensaje += "- La fecha de nacimiento no es válida.\n";
            $("nac").style.border = "2px solid red";
            valido = false;
        } else {
            // Calcular edad
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const mes = hoy.getMonth() - fechaNac.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) edad--;
            if (edad < 18) {
                mensaje += "- Debes tener al menos 18 años.\n";
                $("nac").style.border = "2px solid red";
                valido = false;
            }
        }
    }

    //pais
    if (pais === "") {
        mensaje += "- Debes seleccionar un país de residencia.\n";
        $("pais").style.border = "2px solid red";
        valido = false;
    }

    //final
    if (!valido) {
        alert("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        event.preventDefault(); //pa no enviarlo
    } else {
        alert("Inicio de sesión correcto. Redirigiendo a Inicio...");
    }
}