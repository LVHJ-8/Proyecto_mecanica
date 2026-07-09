document.addEventListener("DOMContentLoaded", function () {
    const fechaInput = document.getElementById("fecha-cita");
    const horaOcultaInput = document.getElementById("hora-seleccionada");
    const botonesHora = document.querySelectorAll(".bloque-hora");

    // 1. Manejar la selección visual de los bloques de horas al hacer clic
    botonesHora.forEach(boton => {
        boton.addEventListener("click", function () {
            // Si el botón está deshabilitado u ocupado, no hacer nada
            if (this.disabled || this.classList.contains("ocupado")) return;

            // Remover la selección previa de todos los botones
            botonesHora.forEach(btn => btn.classList.remove("seleccionado"));
            
            // Marcar el botón actual como seleccionado
            this.classList.add("seleccionado");
            
            // Almacenar el valor (ID del horario o texto de la hora) en el input hidden para el POST
            horaOcultaInput.value = this.getAttribute("data-hora");
        });
    });

    // 2. Escuchar cuando el usuario cambia la fecha para validar disponibilidad en tiempo real
    fechaInput.addEventListener("change", function () {
        const fechaSeleccionada = this.value;
        
        // Si limpian el calendario, reseteamos el valor de la hora seleccionada
        if (!fechaSeleccionada) {
            horaOcultaInput.value = "";
            return;
        }

        // Petición asíncrona (Fetch API) al controlador PHP para consultar la base de datos
        fetch(`../../Frontend/verificar_disponibilidad.php?fecha=${fechaSeleccionada}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Error en la respuesta del servidor");
                }
                return response.json();
            })
            .then(horasOcupadas => {
                // Paso A: Restablecer todos los bloques de horas a su estado disponible
                horaOcultaInput.value = ""; // Limpiar selección anterior obligatoriamente
                botonesHora.forEach(boton => {
                    boton.disabled = false;
                    boton.classList.remove("ocupado", "seleccionado");
                });

                // Paso B: Bloquear y pintar de rojo las horas devueltas por la base de datos
                if (Array.isArray(horasOcupadas) && horasOcupadas.length > 0) {
                    horasOcupadas.forEach(horaBloqueada => {
                        // Busca el botón cuyo atributo data-hora coincida con el registro ocupado de la BD
                        const botonAExcluir = document.querySelector(`.bloque-hora[data-hora="${horaBloqueada}"]`);
                        if (botonAExcluir) {
                            botonAExcluir.disabled = true;
                            botonAExcluir.classList.add("ocupado");
                        }
                    });
                }
            })
            .catch(error => {
                console.error("Error al verificar los horarios:", error);
            });
    });
});