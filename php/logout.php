<?php
// Iniciar sesión para poder destruirla
session_start();

// Eliminar todos los datos de la sesión
session_unset();  // Elimina todas las variables de la sesión
session_destroy();  // Destruye la sesión

// Redirigir al usuario al inicio de sesión
header("Location: ../html/login.html");
exit();
?>
