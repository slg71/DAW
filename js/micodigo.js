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

    // Inicializar tabla de costes si estamos en la página correcta
    inicializarTablaCostes();
});

/*=================================VALIDACIÓN FORMULARIOS=================================*/

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

/*=================================TABLA DE COSTES FOLLETO=================================*/
//lo q está marcado con     /*==========FALTA==========*/
//es lo q me falta por hacer

// Función que calcula el coste de las páginas usando sistema de bloques
// Recibe: número de páginas
// Devuelve: coste calculado según las tarifas por bloques
function calcularCostePaginas(numPaginas) {
    let coste = 0;
    
    //las tarifas:
    // - Páginas 1-4: 2€ cada una
    // - Páginas 5-10: 1.8€ cada una
    // - Páginas 11+: 1.6€ cada una
    
    if (numPaginas <= 4) {
        coste = numPaginas * 2;
    } else if (numPaginas <= 10) {
        coste = (4 * 2) + ((numPaginas - 4) * 1.8);
    } else {
        coste = (4 * 2) + (6 * 1.8) + ((numPaginas - 10) * 1.6);
    }
    
    return coste;
}

// Función que calcula el coste total del folleto
// Recibe: número de páginas, número de fotos, si es color (true/false), resolución (300 o 600)
// Devuelve: coste total formateado con 2 decimales
function calcularCosteFolleto(numPaginas, numFotos, esColor, resolucion) {
    let coste = 10; // Coste base (procesamiento y envío)
    
    //coste de páginas usando la función calcularCostePaginas()
    coste += calcularCostePaginas(numPaginas);
    
    //Si es color, añadir 0.5€ por cada foto
    if (esColor) {
        coste += 0.5*numFotos;
    }
    
    //Si resolución > 300, añadir 0.2€ por cada foto
    if (resolucion > 300) {
        coste += 0.2*numFotos;
    }
    
    return coste.toFixed(2); // Devuelve con 2 decimales
}

// Función que crea toda la tabla
function crearTablaCostes() {
    // Arrays con los valores que aparecerán en la tabla
    const paginas = [1, 5, 10, 15];//del enunciado
    const fotos = [3, 5, 8];//igual
    const colores = [
        { texto: "Blanco y Negro", valor: false },
        { texto: "Color", valor: true }
    ];
    const resoluciones = [
        { texto: "150-300 dpi", valor: 300 },
        { texto: "> 300 dpi", valor: 600 }
    ];
    
    // Crear el elemento <table>
    const tabla = document.createElement("table");
    
    // Crear y añadir el título de la tabla (<caption>)
    const caption = document.createElement("caption");
    caption.textContent = "Tabla de posibles costes de un folleto";
    tabla.appendChild(caption);
    
    // Crear <thead> con la fila de encabezados
    const thead = document.createElement("thead");
    const filaEncabezado = document.createElement("tr");
    
    // Array con los textos de los encabezados
    const encabezados = ["Nº Páginas", "Nº Fotos", "B/N - Color", "Resolución", "Coste"];

    /*==========FALTA==========*/
    // Recorrer el array encabezados y crear un <th> por cada uno
    // Para cada encabezado:
    //   1. Crear elemento th con document.createElement("th")
    //   2. Asignar el texto con textContent
    //   3. Añadirlo a filaEncabezado con appendChild()
    
    thead.appendChild(filaEncabezado);
    tabla.appendChild(thead);
    
    // Crear <tbody> donde irán todas las filas de datos
    const tbody = document.createElement("tbody");
    
    /*==========FALTA==========*/
    // Crear 4 bucles anidados (uno por cada parámetro)
    // rollo como este for pero ns si está bien
    // for (let i = 0; i < paginas.length; i++) {
    //     for (let j = 0; j < fotos.length; j++) {
    //         for (let k = 0; k < colores.length; k++) {
    //             for (let m = 0; m < resoluciones.length; m++) {
    //                 
    //                 aqui deberia hacer esto:
    //                 1. Crear una fila nueva: document.createElement("tr")
    //                 2. Crear 5 celdas <td> (páginas, fotos, color, resolución, coste)
    //                 3. Para cada celda:
    //                    - Crear con document.createElement("td")
    //                    - Asignar textContent con el valor correspondiente
    //                    - Añadirla a la fila con appendChild()
    //                 4. Para la última celda (coste):
    //                    - Llamar a calcularCosteFolleto() con los valores actuales
    //                    - Añadir " €" al final
    //                 5. Añadir la fila completa al tbody
    //             }
    //         }
    //     }
    // }
    
    tabla.appendChild(tbody);
    return tabla;
}

// Función que muestra u oculta la tabla al pulsar el botón
function toggleTablaCostes() {
    const contenedor = $("contenedor-tabla-costes");
    
    // Si está oculta, la mostramos
    if (contenedor.style.display === "none" || contenedor.style.display === "") {
        contenedor.style.display = "block";
        $("btn-toggle-tabla").textContent = "Ocultar tabla de costes";
    } else {
        // Si está visible, la ocultamos
        contenedor.style.display = "none";
        $("btn-toggle-tabla").textContent = "Mostrar tabla de costes";
    }
}

// Función que se ejecuta al cargar la página (si existe el botón)
function inicializarTablaCostes() {
    const boton = $("btn-toggle-tabla");
    
    // Solo ejecutar si estamos en la página del folleto (existe el botón)
    if (boton) {
        // Asociar la función toggle al click del botón
        boton.addEventListener("click", toggleTablaCostes);
        
        const contenedor = $("contenedor-tabla-costes");
        if (contenedor) {
            // Crear la tabla y añadirla al contenedor
            const tabla = crearTablaCostes();
            contenedor.appendChild(tabla);
            
            // Empezar con la tabla oculta
            contenedor.style.display = "none";
        }
    }
}