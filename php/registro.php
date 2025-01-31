<?php
// Conectar a la base de datos
$host = "localhost"; // Servidor de base de datos (cámbialo si está en un servidor remoto)
$usuario_db = "u892208103_Jaziel"; // Nombre de usuario de la base de datos
$contraseña_db = "@Sistemas27"; // Contraseña de la base de datos
$nombre_db = "u892208103_usuarios_db"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar datos del formulario
    $nombre = htmlspecialchars($_POST["nombre"]);
    $correo = htmlspecialchars($_POST["correo"]);
    $contrasena = htmlspecialchars($_POST["contrasena"]);
    
    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena)) {
        echo "Todos los campos son obligatorios.";
        exit();
    }

    // Preparar la consulta SQL para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $contrasena_hash); // "sss" significa que los tres parámetros son strings

    // Encriptar la contraseña antes de insertarla en la base de datos
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT); // Encriptar la contraseña

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "Registro exitoso. <a href='../html/login.html'>Inicia sesión</a>";
    } else {
        echo "Error al registrar el usuario. Por favor, intenta de nuevo más tarde.";
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>
