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

// Primero, obtenemos las publicaciones del usuario autenticado (su propio feed)
$usuario_publicaciones = [];
$sql = "SELECT contenido, fecha_publicacion, 'usuario' AS tipo 
        FROM publicaciones 
        WHERE usuario_id = ? 
        UNION 
        SELECT contenido, fecha_publicacion, 'amigo' AS tipo 
        FROM publicaciones 
        WHERE usuario_id IN (
            SELECT amigo_id FROM amigos WHERE usuario_id = ? AND estado = 'aceptado'
            UNION
            SELECT usuario_id FROM amigos WHERE amigo_id = ? AND estado = 'aceptado'
        )
        ORDER BY fecha_publicacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Crear un array para almacenar todas las publicaciones (usuario + amigos)
$publicaciones = [];
while ($row = $result->fetch_assoc()) {
    $publicaciones[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <header>
        <h1>Inicio</h1>
        <a href="perfil.php">Mi perfil</a> | 
        <a href="amigos.php">Mis Amigos</a>
        <a href="logout.php">Cerrar sesión</a>
    </header>

    <!-- Mostrar publicaciones combinadas -->
    <section class="publicaciones">
        <h2>Todas las publicaciones</h2>
        <?php
        if (count($publicaciones) > 0) {
            foreach ($publicaciones as $publicacion) {
                echo "<div class='tweet'>";
                echo "<p>" . nl2br($publicacion['contenido']) . "</p>";
                echo "<span class='fecha'>" . $publicacion['fecha_publicacion'] . "</span>";
                echo "</div>";
            }
        } else {
            echo "<p>No hay publicaciones para mostrar.</p>";
        }
        ?>
    </section>
</body>
</html>
