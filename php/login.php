<?php
session_start();  // Asegúrate de que esta línea esté al principio

// Datos de conexión a la base de datos
$host = "localhost";
$usuario_db = "u892208103_Jaziel";
$contraseña_db = "@Sistemas27";
$nombre_db = "u892208103_usuarios_db";

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $correo = htmlspecialchars(trim($_POST["correo"]));  // Sanitizar y eliminar espacios
    $contrasena = trim($_POST["contrasena"]);  // Eliminar espacios extra de la contraseña
    
    // Validar que los campos no estén vacíos
    if (empty($correo) || empty($contrasena)) {
        echo "Por favor, complete todos los campos.";
        exit();
    }

    // Consulta preparada para evitar inyección SQL
    $sql = "SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);  // El "s" indica que el parámetro es un string

    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña usando password_verify (si está encriptada)
        if (password_verify($contrasena, $usuario["contrasena"])) {
            // Iniciar sesión: almacenar datos del usuario en la sesión
            $_SESSION["usuario_id"] = $usuario["id"];  // Almacenar el ID del usuario
            $_SESSION["usuario_nombre"] = $usuario["nombre"];  // Almacenar el nombre del usuario

            // Redirigir al perfil del usuario o a una página privada
            header("Location: perfil.php");
            exit();  // Detener el script
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }

    // Cerrar la declaración
    $stmt->close();

    // Cerrar la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Iniciar sesión</h1>
    </header>

    <form action="login.php" method="POST">
        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <button type="submit">Iniciar sesión</button>
    </form>
</body>
</html>
