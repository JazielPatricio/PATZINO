<?php
// Iniciar sesión para acceder a las variables de sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["usuario_id"])) {
    // Si no hay sesión activa, redirigir al inicio de sesión
    header("Location: ../html/login.html");
    exit();  // Detener la ejecución del script
}

// Conectar a la base de datos
$host = "localhost";
$usuario_db = "u892208103_Jaziel";  // Cambia por tu usuario de base de datos
$contraseña_db = "@Sistemas27";     // Cambia por tu contraseña
$nombre_db = "u892208103_usuarios_db";  // Nombre de la base de datos

$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION["usuario_id"];

// Obtener los datos del usuario desde la base de datos
$sql = "SELECT nombre, correo, fecha_registro FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);  // "i" para entero (ID del usuario)

$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    // Mostrar el perfil del usuario
    echo "<h1>Bienvenido, " . $usuario['nombre'] . "</h1>";
    echo "<p>Correo: " . $usuario['correo'] . "</p>";
    echo "<p>Fecha de registro: " . $usuario['fecha_registro'] . "</p>";
} else {
    echo "No se pudo encontrar el perfil del usuario.";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
