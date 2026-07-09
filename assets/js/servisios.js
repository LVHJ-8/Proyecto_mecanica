let slideIndex = 0; 
const slides = document.querySelectorAll('.slide');

const tiempoCambio = 15000; 
let autoPlay = setInterval(autoSlide, tiempoCambio);

function mostrarSlide(index) {
    if (index >= slides.length) { 
        slideIndex = 0; 
    }
    if (index < 0) { 
        slideIndex = slides.length - 1; 
    }

    // Oculta todas las imágenes
    slides.forEach(slide => slide.classList.remove('active'));
    
    // Muestra únicamente la imagen actual
    slides[slideIndex].classList.add('active');
}

// Control de clics manuales en las flechas
function cambiarSlide(direccion) {
    reiniciarTemporizador(); 
    slideIndex += direccion;
    mostrarSlide(slideIndex);
}

function autoSlide() {
    slideIndex++;
    mostrarSlide(slideIndex);
}

function reiniciarTemporizador() {
    clearInterval(autoPlay);
    autoPlay = setInterval(autoSlide, tiempoCambio);
}