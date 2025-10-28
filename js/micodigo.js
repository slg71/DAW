function $(id) {
    return document.getElementById(id);
}

const MAPA_LOCALIDADES = {
    'alicante': 'Alicante',
    'valencia': 'Valencia',
    'albacete': 'Albacete',
    'barcelona': 'Barcelona',
    '': '---'
};
const MAPA_PROVINCIAS = {
    'sanvi': 'San Vicente del Raspeig',
    'elche': 'Elche',
    'alicanteP': 'Alicante', 
    'sanjuan': 'San Juan',
    '': '---'
};
const MAPA_ANUNCIOS = {
    'piso': 'Piso en alquiler',
    'casa': 'Casa en venta',
    'garage': 'Garaje disponible',
    'oficina': 'Oficina en alquiler',
    '': '--Seleccione un anuncio--'
};


// Esperar a que el documento esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Formulario de login
    const formLogin = document.querySelector("#login form");
    if (formLogin) {
        formLogin.addEventListener("submit", validarLogin);
    }

    // Formulario de registro
    const formReg = document.querySelector("#registro form");
    if (formReg) {
        formReg.addEventListener("submit", validarRegistro);
    }

    // Formulario de solicitud de folleto
    const formFolleto = document.querySelector("#form-folleto");
    if (formFolleto) {
        formFolleto.addEventListener("submit", validarFolleto);
    }

    inicializarTablaCostes();

    // PROCESAR RESPUESTA SI ESTAMOS EN LA PÁGINA DE RESPUESTA
    if (document.title.includes("Respuesta Solicitud Folleto")) {
        procesarRespuestaFolleto();
    }
});

/*=================================MODAL=================================*/
function crearModal() {
    const modal = document.createElement("dialog");

    const texto = document.createElement("p");
    const boton = document.createElement("button");
    boton.textContent = "Aceptar";

    boton.addEventListener("click", () => modal.close());

    modal.appendChild(texto);
    modal.appendChild(boton);
    document.body.appendChild(modal);

    // Función global para usar en lugar de alert()
    window.mostrarModal = function (mensaje) {
        texto.textContent = mensaje;
        modal.showModal();
    };
}

document.addEventListener("DOMContentLoaded", crearModal);

/*=================================MARCADOR DE CAMPOS Y ERRORES=================================*/
function marcarError(campo) {
    $(campo).style.border = "2px solid red";
}

function resetearMarcador(campo) {
    $(campo).style.border = "";
}

function mostrarErrorCampo(idCampo, mensaje) {
    const campo = $(idCampo);

    // Si ya existe un mensaje previo, lo borramos
    const prevError = campo.nextElementSibling;
    if (prevError && prevError.classList.contains("error-campo")) {
        prevError.remove();
    }

    // Crear un nuevo span para el error
    const error = document.createElement("span");
    error.className = "error-campo";
    error.textContent = mensaje;

    // Insertarlo después del campo
    campo.insertAdjacentElement("afterend", error);

    // También marcar borde rojo
    marcarError(idCampo);
}

function limpiarErroresCampo(idCampo) {
    const campo = $(idCampo);

    // Borrar borde rojo
    resetearMarcador(idCampo);

    // Borrar span de error si existe
    const prevError = campo.nextElementSibling;
    if (prevError && prevError.classList.contains("error-campo")) {
        prevError.remove();
    }
}


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
    // $("usuario").style.border = "";
    // $("pwd").style.border = "";
    limpiarErroresCampo("usuario");
    limpiarErroresCampo("pwd");

    //usuario
    if (usuario === "") {
        // mensaje += "- El campo 'Usuario' no puede estar vacío.\n";
        // // $("usuario").style.border = "2px solid red";
        // marcarError("usuario");
        mostrarErrorCampo("usuario", "El campo 'Usuario' no puede estar vacío.");
        valido = false;
    } 
    // else if (usuario.length < 3 || usuario.length > 16) {
    //     mensaje += "- El usuario debe tener al menos 8 caracteres.\n";
    //     // $("usuario").style.border = "2px solid red";
    //     valido = false;
    // }

    //contraseña
    if (pwd === "") {
        // mensaje += "- El campo 'Contraseña' no puede estar vacío.\n";
        // // $("pwd").style.border = "2px solid red";
        // marcarError("pwd");
        mostrarErrorCampo("pwd", "El campo 'Contraseña' no puede estar vacío.");
        valido = false;
    }

    if (!valido) {
        // alert("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        mostrarModal("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        event.preventDefault(); //pa no enviarlo
    } else {
        mostrarModal("Inicio de sesión correcto. Redirigiendo a Inicio...");
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
    // $("usuario").style.border = "";
    // $("pwd").style.border = "";
    // $("pwd2").style.border = "";
    // $("sexo").style.border = "";
    // $("nac").style.border = "";
    // $("ciudad").style.border = "";
    // $("pais").style.border = "";
    // $("email").style.border = "";
    // $("foto").style.border = "";
    limpiarErroresCampo("usuario");
    limpiarErroresCampo("pwd");
    limpiarErroresCampo("pwd2");
    limpiarErroresCampo("sexo");
    limpiarErroresCampo("nac");
    limpiarErroresCampo("ciudad");
    limpiarErroresCampo("pais");
    limpiarErroresCampo("email");
    limpiarErroresCampo("foto");

    //usuario
    if (usuario === "") {
        // mensaje += "- El campo 'Usuario' no puede estar vacío.\n";
        // // $("usuario").style.border = "2px solid red";
        // marcarError("usuario");
        mostrarErrorCampo("usuario","El campo 'Usuario' no puede estar vacío.");
        valido = false;
    } else {
        if (usuario.length < 3 || usuario.length > 15) {
            // mensaje += "- El nombre de usuario debe tener entre 3 y 15 caracteres.\n";
            // // $("usuario").style.border = "2px solid red";
            // marcarError("usuario");
            mostrarErrorCampo("usuario","El nombre de usuario debe tener entre 3 y 15 caracteres.");
            valido = false;
        }
        //todas las letras y numeros para compararlo en usuario
        const letras = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const numeros = "0123456789";

        if (numeros.includes(usuario.charAt(0))) {
            // mensaje += "- El usuario no puede comenzar con un número.\n";
            // // $("usuario").style.border = "2px solid red";
            // marcarError("usuario");
            mostrarErrorCampo("usuario","El usuario no puede comenzar con un número.");
            valido = false;
        }

        for (let i = 0; i < usuario.length; i++) {
            const caracter = usuario.charAt(i);
            if (!letras.includes(caracter) && !numeros.includes(caracter)) {
                // mensaje += "- El usuario solo puede contener letras o números.\n";
                // // $("usuario").style.border = "2px solid red";
                // marcarError("usuario");
                mostrarErrorCampo("usuario","El usuario solo puede contener letras o números.");
                valido = false;
            }
        }
    }

    //contraseña
    if (pwd === "") {
        // mensaje += "- El campo 'Contraseña' no puede estar vacío.\n";
        // // $("pwd").style.border = "2px solid red";
        // marcarError("pwd");
        mostrarErrorCampo("pwd","El campo 'Contraseña' no puede estar vacío.");
        valido = false;
    } else {
        if (pwd.length < 6 || pwd.length > 15) {
            // mensaje += "- La contraseña debe tener entre 6 y 15 caracteres.\n";
            // // $("pwd").style.border = "2px solid red";
            // marcarError("pwd");
            mostrarErrorCampo("pwd","La contraseña debe tener entre 6 y 15 caracteres.");
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
            // mensaje += "- La contraseña solo puede contener letras, números, guion o guion bajo.\n";
            // // $("pwd").style.border = "2px solid red";
            // marcarError("pwd");
            mostrarErrorCampo("pwd","La contraseña solo puede contener letras, números, guion o guion bajo.");
            valido = false;
        }

        if (!tieneMayus || !tieneMinus || !tieneNum) {
            // mensaje += "- La contraseña debe tener al menos una mayúscula, una minúscula y un número.\n";
            // // $("pwd").style.border = "2px solid red";
            // marcarError("pwd");
            mostrarErrorCampo("pwd","La contraseña debe tener al menos una mayúscula, una minúscula y un número.");
            valido = false;
        }
    }

    //repetir contraseña
    if (pwd2 === "") {
        // mensaje += "- Debes repetir la contraseña.\n";
        // // $("pwd2").style.border = "2px solid red";
        // marcarError("pwd2");
        mostrarErrorCampo("pwd2","Debes repetir la contraseña.");
        valido = false;
    } else if (pwd !== pwd2) {
        // mensaje += "- Las contraseñas no coinciden.\n";
        // // $("pwd2").style.border = "2px solid red";
        // marcarError("pwd2");
        mostrarErrorCampo("pwd2","Las contraseñas no coinciden.");
        valido = false;
    }

    //email
    if (email === "") {
        // mensaje += "- El campo 'Dirección de email' no puede estar vacío.\n";
        // // $("email").style.border = "2px solid red";
        // marcarError("email");
        mostrarErrorCampo("email","El campo 'Dirección de email' no puede estar vacío.");
        valido = false;
    } else {
        if (!email.includes("@") || email.startsWith("@") || email.endsWith("@")) {
            // mensaje += "- El email debe tener el formato parte-local@dominio.\n";
            // // $("email").style.border = "2px solid red";
            // marcarError("email");
            mostrarErrorCampo("email","El email debe tener el formato parte-local@dominio.");
            valido = false;
        } else {
            const partes = email.split("@");//dividirlo en dos donde haya @
            const local = partes[0];//primera parte
            const dominio = partes[1];//seguunda
            if (local.length < 1 || dominio.length < 1) {
                // mensaje += "- El email debe tener una parte local y un dominio válidos.\n";
                // // $("email").style.border = "2px solid red";
                // marcarError("email");
                mostrarErrorCampo("email","El email debe tener una parte local y un dominio válidos.");
                valido = false;
            } else if (email.length > 254) {
                // mensaje += "- La dirección de email no puede superar los 254 caracteres.\n";
                // // $("email").style.border = "2px solid red";
                // marcarError("email");
                mostrarErrorCampo("email","La dirección de email no puede superar los 254 caracteres.");
                valido = false;
            }
        }
    }

    //sexo
    if (sexo === "") {
        // mensaje += "- Debes seleccionar un sexo.\n";
        // // $("sexo").style.border = "2px solid red";
        // marcarError("sexo");
        mostrarErrorCampo("sexo","Debes seleccionar un sexo.");
        valido = false;
    }

    //fecha de nacimiento
    if (nac === "") {
        // mensaje += "- Debes indicar tu fecha de nacimiento.\n";
        // // $("nac").style.border = "2px solid red";
        // marcarError("nac");
        mostrarErrorCampo("nac","Debes indicar tu fecha de nacimiento.");
        valido = false;
    } else {
        const fechaNac = new Date(nac);
        const hoy = new Date();

        if (isNaN(fechaNac)) {
            // mensaje += "- La fecha de nacimiento no es válida.\n";
            // // $("nac").style.border = "2px solid red";
            // marcarError("nac");
            mostrarErrorCampo("nac","La fecha de nacimiento no es válida.");
            valido = false;
        } else {
            // Calcular edad
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const mes = hoy.getMonth() - fechaNac.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) edad--;
            if (edad < 18) {
                // mensaje += "- Debes tener al menos 18 años.\n";
                // // $("nac").style.border = "2px solid red";
                // marcarError("nac");
                mostrarErrorCampo("nac","Debes tener al menos 18 años.");
                valido = false;
            }
        }
    }

    //pais
    if (pais === "") {
        // mensaje += "- Debes seleccionar un país de residencia.\n";
        // // $("pais").style.border = "2px solid red";
        // marcarError("pais");
        mostrarErrorCampo("pais","Debes seleccionar un país de residencia.");
        valido = false;
    }

    //final
    if (!valido) {
        // alert("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        mostrarModal("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        event.preventDefault(); //pa no enviarlo
    } else {
        mostrarModal("Inicio de sesión correcto. Redirigiendo a Inicio...");
    }
}


/*==================FUNCION PARA SOLICITAR FOLLETO====================*/

function validarFolleto(event) {
    const camposObligatorios = ["nombre", "email", "direccion", "numero", "cp", "localidad", "provincia", "anuncio"];
    let valido = true;
    let mensaje = "";

    camposObligatorios.forEach(id => $(id).style.border = "");

    //campos vacíos
    camposObligatorios.forEach(id => {
        const valor = $(id).value.trim();
        if (valor === "") {
            // mensaje += `- El campo '${id}' es obligatorio.\n`;
            // // $(id).style.border = "2px solid red";
            // marcarError(id);
            mostrarErrorCampo(id,"El campo " + id + " es obligatorio.");
            valido = false;
        }
    });

    //email
    const email = $("email").value.trim();
    if (email !== "") {
        const emailRegex = new RegExp(
            "^(?!\\.)[A-Za-z0-9!#$%&'*+/=?^_`{|}~-]+(\\.[A-Za-z0-9!#$%&'*+/=?^_`{|}~-]+)*@" +
            "(?=.{1,255}$)([A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?\\.)+" +
            "[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?$"
        );

        if (!emailRegex.test(email)) {
            // mensaje += "- La dirección de email no tiene un formato válido.\n";
            // // $("email").style.border = "2px solid red";
            // marcarError("email");
            mostrarErrorCampo("email","La dirección de email no tiene un formato válido.");
            valido = false;
        }

        const partes = email.split("@");
        const parteLocal = partes[0] || "";
        const dominio = partes[1] || "";

        if (parteLocal.length > 64) {
            // mensaje += "- La parte local del email no puede tener más de 64 caracteres.\n";
            // // $("email").style.border = "2px solid red";
            // marcarError("email");
            mostrarErrorCampo("email","La parte local del email no puede tener más de 64 caracteres.");
            valido = false;
        }
        if (dominio.length > 255) {
            // mensaje += "- El dominio del email no puede tener más de 255 caracteres.\n";
            // // $("email").style.border = "2px solid red";
            // marcarError("emai");
            mostrarErrorCampo("email","El dominio del email no puede tener más de 255 caracteres.");
            valido = false;
        }
    }

    const nombre = $("nombre").value.trim();
    if (nombre !== "") {
        const nombreRegex = /^[A-Za-z][A-Za-z0-9]{2,14}$/;
        if (!nombreRegex.test(nombre)) {
            // mensaje += "- El nombre solo puede contener letras y números, no puede comenzar con un número y debe tener entre 3 y 15 caracteres.\n";
            // // $("nombre").style.border = "2px solid red";
            // marcarError("nombre");
            mostrarErrorCampo("nombre","El nombre solo puede contener letras y números, no puede comenzar con un número y debe tener entre 3 y 15 caracteres.");
            valido = false;
        }
    }

    if (!valido) {
        mostrarModal("Por favor, corrige los siguientes errores:\n\n" + mensaje);
        event.preventDefault();
    } else {
        mostrarModal("Formulario enviado correctamente. Redirigiendo a la página de respuesta...");
    }
}



/*=================================TABLA DE COSTES FOLLETO=================================*/

function calcularCostePaginas(numPaginas) {
    let coste = 0;
    
    if (numPaginas <= 4) {
        coste = numPaginas * 2;
    } else if (numPaginas <= 10) {
        coste = (4 * 2) + ((numPaginas - 4) * 1.8);
    } else {
        coste = (4 * 2) + (6 * 1.8) + ((numPaginas - 10) * 1.6);
    }
    
    return coste;
}


function calcularCosteFolleto(numPaginas, numFotos, esColor, resolucion) {
    // Coste base de procesamiento y envio
    let coste = 10; 
    
    // Coste de páginas
    coste += calcularCostePaginas(numPaginas);
    
    // Coste de color 0.5€ por fotos a color
    if (esColor) {
        coste += 0.5 * numFotos;
    }
    
    // Coste de resolución > 300 dpi añade 0.2€ por foto
    if (resolucion > 300) {
        coste += 0.2 * numFotos;
    }
    
    return coste.toFixed(2); 
}


function crearTablaCostes() {
    
    // datos de la tabla del enunciado
    const filasTabla = [];
    for (let i = 1; i <= 15; i++) {
        filasTabla.push({
            paginas: i,
            fotos: i * 3
        });
    }

    const colores = [
        { texto: "Blanco y Negro", valor: false },
        { texto: "Color", valor: true }
    ];
    const resoluciones = [
        { texto: "150-300 dpi", valor: 300 }, 
        { texto: "450-900 dpi", valor: 600 } 
    ];
    
    //crear la tabla
    const tabla = document.createElement("table");
    
    const caption = document.createElement("caption");
    tabla.appendChild(caption);
    
    const thead = document.createElement("thead");
    const filaEncabezado = document.createElement("tr");
    
    const encabezados = ["Número de páginas", "Número de fotos", "Blanco y negro", "Color"];

    let thVacio = document.createElement("th");
    thVacio.setAttribute("rowspan", "2");
    thVacio.textContent = encabezados[0];
    filaEncabezado.appendChild(thVacio);

    thVacio = document.createElement("th");
    thVacio.setAttribute("rowspan", "2");
    thVacio.textContent = encabezados[1];
    filaEncabezado.appendChild(thVacio);
    
    for (const color of colores) {
        const thColor = document.createElement("th");
        thColor.setAttribute("colspan", resoluciones.length); 
        thColor.textContent = color.texto;
        filaEncabezado.appendChild(thColor);
    }

    thead.appendChild(filaEncabezado);
    
    
    const filaEncabezadoResolucion = document.createElement("tr");
    for (let i = 0; i < colores.length; i++) {
        for (const resolucion of resoluciones) {
            const thResolucion = document.createElement("th");
            thResolucion.textContent = resolucion.texto;
            filaEncabezadoResolucion.appendChild(thResolucion);
        }
    }
    thead.appendChild(filaEncabezadoResolucion);
    tabla.appendChild(thead);
    
    const tbody = document.createElement("tbody");
    
    for (const filaData of filasTabla) {
        const numPaginas = filaData.paginas;
        const numFotos = filaData.fotos;
        
        const fila = document.createElement("tr");
        
        // numero de paginas
        let tdPaginas = document.createElement("td");
        tdPaginas.textContent = numPaginas;
        fila.appendChild(tdPaginas);

        // numero de fotos
        let tdFotos = document.createElement("td");
        tdFotos.textContent = numFotos;
        fila.appendChild(tdFotos);
        
        // Colores Blanco y Negro / Color
        for (const color of colores) {
            const esColor = color.valor;
            
            // Resoluciones de 300 dpi a 600 dpi
            for (const resolucion of resoluciones) {
                const valorResolucion = resolucion.valor;
                
                const costeCalculado = calcularCosteFolleto(numPaginas, numFotos, esColor, valorResolucion);
                
                const tdCoste = document.createElement("td");
                
                tdCoste.textContent = costeCalculado + " €";
            
                fila.appendChild(tdCoste);
            }
        }

        tbody.appendChild(fila);
    }
    
    tabla.appendChild(tbody);
    return tabla;
}

function toggleTablaCostes() {
    const contenedor = $("contenedor-tabla-costes");
    const boton = $("btn-toggle-tabla");
    
    if (contenedor.style.display === "none" || contenedor.style.display === "") {
        contenedor.style.display = "block";
        boton.textContent = "Ocultar tabla de costes";
    } else {
        contenedor.style.display = "none";
        boton.textContent = "Mostrar tabla de costes";
    }
}

function inicializarTablaCostes() {
    const boton = $("btn-toggle-tabla");
    
    if (boton) {
        boton.addEventListener("click", toggleTablaCostes);
        
        const contenedor = $("contenedor-tabla-costes");
        if (contenedor) {
            const tabla = crearTablaCostes();
            contenedor.appendChild(tabla);
            
            contenedor.style.display = "none";
        }
    }
}


/*=================================RESPUESTA SOLICITUD FOLLETO=================================*/

function actualizarCeldaRespuesta(filaIndex, valor) {
    const tabla = document.querySelector('main table');
    if (tabla) {
        // La celda de valor es la segunda (índice 1) de la fila
        const fila = tabla.rows[filaIndex];
        if (fila && fila.cells.length > 1) {
            fila.cells[1].textContent = valor;
        }
    }
}


function procesarRespuestaFolleto() {

    const params = new URLSearchParams(window.location.search);

    const nombre = params.get('nombre') || 'N/A';
    const email = params.get('email') || 'N/A';
    
    // Dirección
    const direccion = params.get('direccion') || '';
    const numero = params.get('numero') || '';
    const cp = params.get('cp') || '';
    const dirCompleta = `${direccion}${direccion && numero ? ' Nº ' + numero : ''}${cp ? ' CP ' + cp : ''}`.trim() || 'N/A';

    
    const localidad = MAPA_LOCALIDADES[params.get('localidad')] || 'N/A';
    const provincia = MAPA_PROVINCIAS[params.get('provincia')] || 'N/A';

    const telefono = params.get('telefono') || 'N/A';
    const textoAdicional = params.get('texto_adicional') || 'Ninguno';
    
    
    const paginasPorFolleto = parseInt(params.get('paginas')) || 1; 
    const fotosPorFolleto = parseInt(params.get('fotos')) || 1;     
    const copias = 1;
    const impresionRes = parseInt(params.get('impresion')) || 150;
    
    const anuncio = MAPA_ANUNCIOS[params.get('anuncio')] || 'N/A';
    const fechaRec = params.get('fecha_rec') || 'N/A';

    const impresionColorVal = params.get('impresion_color') === 'color';
    const impresionColorText = impresionColorVal ? 'Sí (Color)' : 'No (Blanco y Negro)';

    const imprimirPrecioVal = params.get('imprimir_precio') === 'si';
    const imprimirPrecioText = imprimirPrecioVal ? 'Sí' : 'No';
    
    const costeTotal = calcularCosteFolleto(
        paginasPorFolleto * copias, 
        fotosPorFolleto * copias, 
        impresionColorVal, 
        impresionRes
    );
    
    actualizarCeldaRespuesta(0, nombre);
    actualizarCeldaRespuesta(1, email);
    actualizarCeldaRespuesta(2, dirCompleta);
    actualizarCeldaRespuesta(3, localidad);
    actualizarCeldaRespuesta(4, provincia);
    actualizarCeldaRespuesta(5, telefono);
    actualizarCeldaRespuesta(6, textoAdicional);
    actualizarCeldaRespuesta(7, paginasPorFolleto.toString()); 
    actualizarCeldaRespuesta(8, fotosPorFolleto.toString());   
    actualizarCeldaRespuesta(9, impresionRes + ' dpi');        
    actualizarCeldaRespuesta(10, anuncio);                     
    actualizarCeldaRespuesta(11, fechaRec);                    
    actualizarCeldaRespuesta(12, impresionColorText);          
    actualizarCeldaRespuesta(13, imprimirPrecioText);          
    actualizarCeldaRespuesta(14, costeTotal + ' €'); 
}