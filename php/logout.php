<?php
// Iniciar sesión para poder destruirla
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION["usuario_id"])) {
    // Eliminar todas las variables de la sesión
    session_unset();  

    // Destruir la sesión
    session_destroy();

    // Limpiar la cookie de la sesión si está establecida
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirigir al usuario al inicio de sesión
header("Location: ../html/login.html");  
exit();
?>
