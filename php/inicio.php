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

// Obtener información del usuario
$sql = "SELECT nombre, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Error: No se encontró la información del usuario.");
}

// Manejo de publicación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_publicacion'])) {
    $contenido = trim($_POST['contenido']);
    if (!empty($contenido)) {
        $sql = "INSERT INTO publicaciones (usuario_id, contenido, fecha) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $contenido);
        $stmt->execute();
        header("Location: inicio.php");
        exit();
    } else {
        echo "<p style='color:red;'>La publicación no puede estar vacía.</p>";
    }
}

// Obtener publicaciones del usuario y sus amigos
$sql = "SELECT publicaciones.id, publicaciones.contenido, publicaciones.fecha, usuarios.nombre, usuarios.foto_perfil
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
    <style>
        .crear-publicacion {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .crear-publicacion img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .publicacion {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
        }
        .publicacion img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Inicio</h1>
    
    <div class="crear-publicacion">
        <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?: 'default.png') ?>" alt="Foto de perfil">
        <form method="POST">
            <input type="text" name="contenido" placeholder="¿Qué estás pensando, <?= htmlspecialchars($usuario['nombre']) ?>?" required>
            <button type="submit" name="crear_publicacion">Publicar</button>
        </form>
    </div>
    
    <h2>Publicaciones</h2>
    <?php if (empty($publicaciones)): ?>
        <p>No hay publicaciones aún.</p>
    <?php else: ?>
        <?php foreach ($publicaciones as $publicacion): ?>
            <div class="publicacion">
                <img src="<?= htmlspecialchars($publicacion['foto_perfil'] ?: 'default.png') ?>" alt="Foto de perfil">
                <strong><?= htmlspecialchars($publicacion['nombre']) ?></strong>
                <p><?= htmlspecialchars($publicacion['contenido']) ?></p>
                <small><?= $publicacion['fecha'] ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
