<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$host = "localhost";
$usuario_db = "u892208103_Jaziel";
$contraseña_db = "@Sistemas27";
$nombre_db = "u892208103_usuarios_db";

$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION["usuario_id"];

// Enviar solicitud de amistad
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["solicitar_amigo"])) {
    $amigo_id = $_POST["amigo_id"];

    // Verificar si ya existe una solicitud
    $sql = "SELECT * FROM amigos WHERE (usuario_id = ? AND amigo_id = ?) OR (usuario_id = ? AND amigo_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $usuario_id, $amigo_id, $amigo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Ya tienes una solicitud pendiente o ya son amigos.";
    } else {
        // Enviar solicitud de amistad
        $sql = "INSERT INTO amigos (usuario_id, amigo_id, estado) VALUES (?, ?, 'pendiente')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $amigo_id);

        if ($stmt->execute()) {
            echo "Solicitud de amistad enviada.";
        } else {
            echo "Error al enviar la solicitud.";
        }
    }

    $stmt->close();
}

// Obtener las publicaciones del usuario
$sql = "SELECT contenido, fecha_publicacion FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Buscar otros usuarios para enviar solicitud
$sql = "SELECT id, nombre FROM usuarios WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$usuarios_result = $stmt->get_result();
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
            while ($publicacion = $result->fetch_assoc()) {
                echo "<div class='tweet'>";
                echo "<div class='perfil'>";
                echo "<img class='foto-perfil' src='../imagenes/FotoDePerfil.jpeg' alt='Foto de perfil'>";
                echo "<div class='info-perfil'>";
                echo "<span class='nombre'>" . $_SESSION["usuario_nombre"] . "</span>";
                echo "<span class='fecha'>" . $publicacion["fecha_publicacion"] . "</span>";
                echo "</div>";
                echo "</div>";
                echo "<div class='contenido-tweet'><p>" . nl2br($publicacion["contenido"]) . "</p></div>";
                echo "</div>";
            }
        } else {
            echo "<p>No tienes publicaciones aún.</p>";
        }
        ?>
    </section>

    <!-- Mostrar usuarios para agregar como amigos -->
    <section class="agregar-amigos">
        <h2>Usuarios disponibles para agregar</h2>
        <?php
        while ($usuario = $usuarios_result->fetch_assoc()) {
            echo "<div class='usuario'>";
            echo "<span>" . $usuario["nombre"] . "</span>";
            echo "<form action='perfil.php' method='POST'>";
            echo "<input type='hidden' name='amigo_id' value='" . $usuario["id"] . "'>";
            echo "<button type='submit' name='solicitar_amigo'>Enviar solicitud</button>";
            echo "</form>";
            echo "</div>";
        }
        ?>
    </section>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
