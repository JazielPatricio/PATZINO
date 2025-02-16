// Función para alternar el menú en pantallas pequeñas
function toggleMenu() {
    var menu = document.getElementById("menu");
    menu.classList.toggle("menu-responsive");
}

// Función para abrir/cerrar el submenú en pantallas pequeñas
document.querySelectorAll('.submenu').forEach(function(submenu) {
    submenu.addEventListener('click', function(e) {
        // Evita que el clic afecte a otros elementos
        e.stopPropagation();
        this.classList.toggle('active'); // Alterna la clase 'active' para mostrar/ocultar el submenú
    });
});
