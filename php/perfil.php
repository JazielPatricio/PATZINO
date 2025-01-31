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

// Obtener los datos del usuario desde la base de datos (como el nombre)
$sql = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);  // "i" para entero (ID del usuario)

$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $usuario_nombre = $usuario['nombre'];  // Guardamos el nombre del usuario
} else {
    echo "No se pudo encontrar el perfil del usuario.";
}

// Obtener las publicaciones del usuario
$sql = "SELECT contenido, fecha_publicacion FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION["usuario_id"]);
$stmt->execute();
$result = $stmt->get_result();

// Mostrar las publicaciones del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo $usuario_nombre; ?></title>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/blog.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo $usuario_nombre; ?></h1>
    </header>

    <!-- Formulario para agregar una nueva publicación -->
    <form action="perfil.php" method="POST">
        <textarea name="contenido" placeholder="Escribe una publicación..." required></textarea>
        <button type="submit">Publicar</button>
    </form>

    <!-- Mostrar las publicaciones -->
    <?php
    while ($publicacion = $result->fetch_assoc()) {
        echo '<div class="tweet">';
        echo '    <div class="perfil">';
        echo '        <img src="../imagenes/FotoDePerfil.jpeg" alt="Foto De Perfil" class="foto-perfil">';
        echo '        <div class="info-perfil">';
        echo '            <span class="nombre">' . $usuario_nombre . '</span>';
        echo '            <span class="fecha">' . date("d M Y H:i", strtotime($publicacion['fecha_publicacion'])) . '</span>';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="contenido-tweet">';
        echo '        <p>' . htmlspecialchars($publicacion['contenido']) . '</p>';
        echo '    </div>';
        echo '</div>';
    }
    ?>

</body>
</html>

<?php
// Cerrar la conexión
$stmt->close();
$conn->close();
?>
