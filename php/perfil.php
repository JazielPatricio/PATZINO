<?php
// Iniciar sesión para acceder a las variables de sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["usuario_id"])) {
    // Si no hay sesión activa, redirigir al inicio de sesión
    header("Location: ../html/login.html");
    exit();  // Detener la ejecución del script
}

// Si el usuario está autenticado, se puede mostrar su perfil
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];

echo "Bienvenido, " . $usuario_nombre;  // Muestra el nombre del usuario
?>
