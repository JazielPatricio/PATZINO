<?php
// Conectar a la base de datos
$host = "localhost"; 
$usuario_db = "u892208103_Jaziel"; 
$contraseña_db = "@Sistemas27"; 
$nombre_db = "u892208103_usuarios_db"; 

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
    $stmt->bind_param("sss", $nombre, $correo, $contrasena_hash); 

    // Encriptar la contraseña antes de insertarla en la base de datos
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT); 

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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
</head>
<body>
    <header>
        <h1>Regístrate</h1>
    </header>

    <form action="registro.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
