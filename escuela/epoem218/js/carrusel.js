let currentIndex = 0;
const images = document.querySelectorAll(".imagenes img");
const totalImages = images.length;

const prevButton = document.querySelector(".prev");
const nextButton = document.querySelector(".next");

function showImage(index) {
    const newTransformValue = -index * 100;
    document.querySelector(".imagenes").style.transform = `translateX(${newTransformValue}%)`;
}

function nextImage() {
    currentIndex = (currentIndex === totalImages - 1) ? 0 : currentIndex + 1;
    showImage(currentIndex);
}

// Función para ir a la imagen anterior
function prevImage() {
    currentIndex = (currentIndex === 0) ? totalImages - 1 : currentIndex - 1;
    showImage(currentIndex);
}

// Controlar el cambio automático de las imágenes cada 3 segundos
let autoSlide = setInterval(nextImage, 3000);  // 3000ms = 3 segundos

// Detener el carrusel automático cuando el usuario haga clic en los botones
prevButton.addEventListener("click", () => {
    clearInterval(autoSlide);  // Detener el carrusel automático
    prevImage();
    autoSlide = setInterval(nextImage, 3000);  // Reiniciar el carrusel automático
});

nextButton.addEventListener("click", () => {
    clearInterval(autoSlide);  // Detener el carrusel automático
    nextImage();
    autoSlide = setInterval(nextImage, 3000);  // Reiniciar el carrusel automático
});
