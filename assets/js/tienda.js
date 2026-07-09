/* ==========================================================================
   CONTROLADOR DE TIENDA: FILTROS DINÁMICOS ASÍNCRONOS (LUVEN)
   ========================================================================== */

document.addEventListener("DOMContentLoaded", () => {
    // 1. Capturamos los elementos de la interfaz (Asegúrate de que existan en tu HTML)
    const buscador = document.getElementById("buscar-producto");
    const selectCategoria = document.getElementById("filtrar-categoria");
    const contenedorProductos = document.querySelector(".productos-grid");

    // Validamos que el contenedor de productos exista en la página actual para evitar errores
    if (!contenedorProductos) return;

    /**
     * Función principal que realiza la petición asíncrona mediante Fetch
     */
    const filtrarProductos = () => {
        // Obtenemos los valores actuales de los inputs
        const textoBusqueda = buscador ? buscador.value.trim() : "";
        const idCategoria = selectCategoria ? selectCategoria.value : "";

        // Mostramos un efecto de carga sutil en el contenedor antes de recibir los datos
        contenedorProductos.style.opacity = "0.5";

        // Creamos los parámetros que se enviarán por la URL
        const parametros = new URLSearchParams({
            buscar: textoBusqueda,
            categoria: idCategoria
        });

        // Enviamos la solicitud a un procesador PHP encargado de devolver solo las tarjetas
        fetch(`procesar_filtro_tienda.php?${parametros.toString()}`)
            .then(respuesta => {
                if (!respuesta.ok) {
                    throw new Error("Error en la respuesta del servidor");
                }
                return respuesta.text(); // Esperamos código HTML limpio como respuesta
            })
            .then(htmlDeTarjetas => {
                // Inyectamos las nuevas tarjetas filtradas y restauramos la opacidad
                contenedorProductos.innerHTML = htmlDeTarjetas;
                contenedorProductos.style.opacity = "1";
            })
            .catch(error => {
                console.error("Error al filtrar el catálogo:", error);
                contenedorProductos.style.opacity = "1";
                contenedorProductos.innerHTML = `
                    <div class="tienda-vacia">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: #ef4444; margin-bottom: 10px;"></i>
                        <p style="color: #64748b; font-size: 1.1rem; margin: 0;">Hubo un problema al procesar tu búsqueda. Inténtalo de nuevo.</p>
                    </div>`;
            });
    };

    // 2. Escuchamos los eventos del usuario de forma inteligente
    
    if (buscador) {
        // 'input' detecta cada letra que el usuario escribe, borra o pega en tiempo real
        buscador.addEventListener("input", filtrarProductos);
    }

    if (selectCategoria) {
        // 'change' se dispara inmediatamente cuando cambian de "Aceites" a "Bujías", etc.
        selectCategoria.addEventListener("change", filtrarProductos);
    }
});