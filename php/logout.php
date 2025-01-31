<?php
// Iniciar sesión para poder destruirla
session_start();

// Verificar si la sesión está activa antes de destruirla
if (isset($_SESSION["usuario_id"])) {
    // Eliminar todas las variables de la sesión
    session_unset();  // Elimina todas las variables de la sesión

    // Destruir la sesión
    session_destroy();

    // Limpiar la cookie de la sesión si está establecida
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirigir al usuario al inicio de sesión
header("Location: https://www.tusitio.com/html/login.html");  // Usa la URL completa si es necesario
exit();
?>
