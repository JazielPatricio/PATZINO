<?php
// Iniciar sesión para poder acceder a las variables de sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["usuario_id"])) {
    // Si no hay sesión activa, redirigir al inicio de sesión
    header("Location: login.php");
    exit();  // Detener la ejecución del script
}

// Conectar a la base de datos
$host = "localhost";
$usuario_db = "u892208103_Jaziel";
$contraseña_db = "@Sistemas27";
$nombre_db = "u892208103_usuarios_db";

$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION["usuario_id"];

// Publicar contenido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publicar'])) {
    // Obtener contenido de la publicación
    $contenido = htmlspecialchars(trim($_POST["contenido"]));  // Sanitizar contenido

    // Validar que el campo no esté vacío
    if (!empty($contenido)) {
        // Insertar publicación en la base de datos
        $sql = "INSERT INTO publicaciones (usuario_id, contenido) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $contenido);  // "i" para entero y "s" para string

        if ($stmt->execute()) {
            echo "¡Publicación exitosa!";
        } else {
            echo "Hubo un error al publicar. Intenta nuevamente.";
        }
        $stmt->close();
    } else {
        echo "Por favor, ingresa un contenido para la publicación.";
    }
}

// Obtener las publicaciones del usuario
$sql = "SELECT contenido, fecha_publicacion FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);  // "i" para entero (ID del usuario)
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo $_SESSION["usuario_nombre"]; ?></h1>
        <a href="logout.php">Cerrar sesión</a>
    </header>

    <!-- Formulario para publicar -->
    <section class="publicar">
        <h2>Haz una publicación</h2>
        <form action="perfil.php" method="POST">
            <textarea name="contenido" placeholder="Escribe tu publicación aquí..." required></textarea>
            <button type="submit" name="publicar">Publicar</button>
        </form>
    </section>

    <!-- Mostrar publicaciones -->
    <section class="publicaciones">
        <h2>Publicaciones</h2>
        <?php
        if ($result->num_rows > 0) {
            // Mostrar las publicaciones
            while ($publicacion = $result->fetch_assoc()) {
                echo "<div class='publicacion'>";
                echo "<p><strong>Publicado el:</strong> " . $publicacion["fecha_publicacion"] . "</p>";
                echo "<p>" . nl2br($publicacion["contenido"]) . "</p>";  // nl2br convierte saltos de línea en <br>
                echo "</div>";
            }
        } else {
            echo "<p>No tienes publicaciones aún.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </section>
</body>
</html>
