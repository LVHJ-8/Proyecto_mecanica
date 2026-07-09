// CONTROL INTERACTIVO DE VALIDACIONES DE FORMULARIOS - LUVEN
document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("formulario-registro");

    // Escuchamos el evento de envío (Submit) del formulario
    if (formulario) {
        formulario.addEventListener("submit", function (evento) {
            
            // 1. Inicializar variables de control de errores
            let hayErrores = false;
            limpiarErrores();

            // 2. Capturar los elementos del formulario
            const nombre = document.getElementById("reg-nombre");
            const correo = document.getElementById("reg-correo");
            const telefono = document.getElementById("reg-telefono");
            const password = document.getElementById("reg-password");
            const confirmar = document.getElementById("reg-confirmar");
            const alertaGlobal = document.getElementById("alerta-error-global");

            // Expresión regular matemática para validar correos electrónicos estándar
            const patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // 3. VALIDACIÓN: Nombre Completo
            if (nombre.value.trim() === "") {
                mostrarError("nombre", "El nombre completo es obligatorio.");
                hayErrores = true;
            } else if (nombre.value.trim().length < 4) {
                mostrarError("nombre", "El nombre debe tener al menos 4 caracteres.");
                hayErrores = true;
            }

            // 4. VALIDACIÓN: Correo Electrónico
            if (correo.value.trim() === "") {
                mostrarError("correo", "El correo electrónico es obligatorio.");
                hayErrores = true;
            } else if (!patronCorreo.test(correo.value.trim())) {
                mostrarError("correo", "Por favor, ingresa un correo electrónico válido.");
                hayErrores = true;
            }

            // 5. VALIDACIÓN: Teléfono (Solo números y longitud peruana de 9 dígitos)
            if (telefono.value.trim() === "") {
                mostrarError("telefono", "El número de teléfono es obligatorio.");
                hayErrores = true;
            } else if (isNaN(telefono.value.trim())) {
                mostrarError("telefono", "El teléfono debe contener únicamente números.");
                hayErrores = true;
            } else if (telefono.value.trim().length !== 9) {
                mostrarError("telefono", "El teléfono debe tener exactamente 9 dígitos.");
                hayErrores = true;
            }

            // 6. VALIDACIÓN: Contraseña Segura
            if (password.value === "") {
                mostrarError("password", "La contraseña es obligatoria.");
                hayErrores = true;
            } else if (password.value.length < 8) {
                mostrarError("password", "La contraseña debe tener al menos 8 caracteres.");
                hayErrores = true;
            }

            // 7. VALIDACIÓN: Coincidencia de Contraseñas
            if (confirmar.value === "") {
                mostrarError("confirmar", "Debes confirmar tu contraseña.");
                hayErrores = true;
            } else if (password.value !== confirmar.value) {
                mostrarError("confirmar", "Las contraseñas ingresadas no coinciden.");
                hayErrores = true;
            }

            // 8. ACCIÓN FINAL: Si se detectaron errores, bloquear el envío
            if (hayErrores) {
                evento.preventDefault(); // Detiene el viaje de datos a PHP
                alertaGlobal.innerText = "❌ Por favor, corrige los campos marcados en rojo antes de continuar.";
                alertaGlobal.style.display = "block";
                window.scrollTo({ top: 0, behavior: 'smooth' }); // Sube la pantalla suavemente
            }
        });
    }

    // Funciones auxiliares para inyectar los textos informativos en el HTML
    function mostrarError(campoId, mensaje) {
        const inputField = document.getElementById(`reg-${campoId}`);
        const errorSpan = document.getElementById(`error-${campoId}`);
        
        if (inputField && errorSpan) {
            inputField.classList.add("input-con-error");
            errorSpan.innerText = mensaje;
            errorSpan.style.display = "block";
        }
    }

    function limpiarErrores() {
        const inputs = document.querySelectorAll(".grupo-input input");
        const spans = document.querySelectorAll(".error-individual");
        const alertaGlobal = document.getElementById("alerta-error-global");

        inputs.forEach(input => input.classList.remove("input-con-error"));
        spans.forEach(span => {
            span.innerText = "";
            span.style.display = "none";
        });
        if (alertaGlobal) {
            alertaGlobal.innerText = "";
            alertaGlobal.style.display = "none";
        }
    }
});

// CONTROL DEL FORMULARIO DE INICIO DE SESIÓN (LOGIN)
const formularioLogin = document.getElementById("formulario-login");
if (formularioLogin) {
    formularioLogin.addEventListener("submit", function (evento) {
        
        let hayErrores = false;
        
        // Reutilizamos la función para limpiar los bordes rojos y alertas previas
        const inputs = document.querySelectorAll(".grupo-input input");
        const spans = document.querySelectorAll(".error-individual");
        const alertaGlobal = document.getElementById("alerta-error-global");

        inputs.forEach(input => input.classList.remove("input-con-error"));
        spans.forEach(span => { span.innerText = ""; span.style.display = "none"; });
        if (alertaGlobal) { alertaGlobal.style.display = "none"; }

        // Capturar elementos específicos del Login
        const correo = document.getElementById("log-correo");
        const password = document.getElementById("log-password");
        const patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // VALIDACIÓN: Correo
        if (correo.value.trim() === "") {
            inputError("correo", "Debes ingresar tu correo electrónico.");
            hayErrores = true;
        } else if (!patronCorreo.test(correo.value.trim())) {
            inputError("correo", "El formato del correo electrónico no es válido.");
            hayErrores = true;
        }

        // VALIDACIÓN: Contraseña
        if (password.value === "") {
            inputError("password", "Debes ingresar tu contraseña.");
            hayErrores = true;
        }

        // Si hay un fallo, frenamos el submit de PHP
        if (hayErrores) {
            evento.preventDefault();
            alertaGlobal.innerText = "❌ Ingresa tus credenciales correctas.";
            alertaGlobal.style.display = "block";
        }
    });
}
function inputError(campoId, mensaje) {
    const inputField = document.getElementById(`log-${campoId}`);
    const errorSpan = document.getElementById(`error-${campoId}`);
    if (inputField && errorSpan) {
        inputField.classList.add("input-con-error");
        errorSpan.innerText = mensaje;
        errorSpan.style.display = "block";
    }
}