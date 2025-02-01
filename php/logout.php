<?php
// Iniciar sesión para poder destruirla
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION["usuario_id"])) {
    // Eliminar todas las variables de la sesión
    session_unset();  // Elimina todas las variables de la sesión

    // Destruir la sesión
    session_destroy();

    // Limpiar la cookie de la sesión si está establecida
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');  // Eliminar cookie de sesión
    }
}

// Redirigir al usuario al inicio de sesión
header("Location: ../html/login.html");  // O la URL completa si lo prefieres
exit();
?>
