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

// Obtener publicaciones del usuario y sus amigos
$sql = "SELECT publicaciones.id, publicaciones.contenido, publicaciones.fecha, usuarios.nombre
        FROM publicaciones
        JOIN usuarios ON publicaciones.usuario_id = usuarios.id
        WHERE publicaciones.usuario_id = ?
        OR publicaciones.usuario_id IN (
            SELECT CASE WHEN usuario_id = ? THEN amigo_id ELSE usuario_id END 
            FROM amigos WHERE (usuario_id = ? OR amigo_id = ?) AND estado = 'aceptado')
        ORDER BY publicaciones.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $usuario_id, $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$publicaciones = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
</head>
<body>
    <h1>Inicio</h1>
    
    <h2>Publicaciones</h2>
    <?php foreach ($publicaciones as $publicacion): ?>
        <div>
            <strong><?= htmlspecialchars($publicacion['nombre']) ?></strong>
            <p><?= htmlspecialchars($publicacion['contenido']) ?></p>
            <small><?= $publicacion['fecha'] ?></small>
        </div>
    <?php endforeach; ?>
</body>
</html>
