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

// Obtener los datos del usuario
$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT nombre, correo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Obtener las publicaciones del usuario
$sql_publicaciones = "SELECT contenido, fecha_publicacion FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC";
$stmt_publicaciones = $conn->prepare($sql_publicaciones);
$stmt_publicaciones->bind_param("i", $usuario_id);
$stmt_publicaciones->execute();
$result_publicaciones = $stmt_publicaciones->get_result();

// Cerrar las conexiones
$stmt->close();
$stmt_publicaciones->close();
$conn->close();
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
        <h1>Bienvenido, <?php echo $usuario["nombre"]; ?></h1>
        <a href="logout.php">Cerrar sesión</a>
    </header>

    <!-- Navegación entre apartados -->
    <nav>
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="amigos.php">Mis Amigos</a></li>
        </ul>
    </nav>

    <!-- Información del usuario -->
    <section class="informacion">
        <h2>Mi Perfil</h2>
        <p><strong>Nombre:</strong> <?php echo $usuario["nombre"]; ?></p>
        <p><strong>Correo:</strong> <?php echo $usuario["correo"]; ?></p>
    </section>

    <!-- Mostrar publicaciones propias -->
    <section class="publicaciones">
        <h2>Mis Publicaciones</h2>
        <?php
        if ($result_publicaciones->num_rows > 0) {
            // Mostrar las publicaciones
            while ($publicacion = $result_publicaciones->fetch_assoc()) {
                echo "<div class='tweet'>";
                echo "<p>" . nl2br($publicacion['contenido']) . "</p>";
                echo "<span class='fecha'>" . $publicacion['fecha_publicacion'] . "</span>";
                echo "</div>";
            }
        } else {
            echo "<p>No tienes publicaciones aún.</p>";
        }
        ?>
    </section>
</body>
</html>
