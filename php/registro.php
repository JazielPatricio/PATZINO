<?php
// Iniciar sesión para poder usar las variables de sesión
session_start();

// Datos de conexión a la base de datos
$host = "localhost"; // Cambia a la dirección del servidor si está en uno diferente
$usuario_db = "u892208103_Jaziel"; // Nombre de usuario de la base de datos
$contraseña_db = "@Sistemas27"; // Contraseña de la base de datos
$nombre_db = "u892208103_usuarios_db"; // Nombre de la base de datos

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Genera un token seguro
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar el token CSRF
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Error CSRF: El token no es válido.";
        exit();
    }

    // Sanitizar los datos del formulario
    $nombre = htmlspecialchars(trim($_POST["nombre"]));  
    $correo = htmlspecialchars(trim($_POST["correo"]));  
    $contrasena = trim($_POST["contrasena"]);  
    
    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena)) {
        echo "Todos los campos son obligatorios.";
        exit();
    }

    // Encriptar la contraseña antes de insertarla en la base de datos
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT); 

    // Preparar la consulta SQL para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $contrasena_hash); 

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
    <title>Registro</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1>Registro de Usuario</h1>
    </header>

    <form action="registro.php" method="POST">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

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
