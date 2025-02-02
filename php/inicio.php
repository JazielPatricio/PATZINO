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
$stmt->close();

// Manejo de publicación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publicar'])) {
    $contenido = trim($_POST['contenido']);
    if (!empty($contenido)) {
        $sql = "INSERT INTO publicaciones (usuario_id, contenido, fecha_publicacion) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $contenido);
        $stmt->execute();
        $stmt->close();
        header("Location: inicio.php");
        exit();
    }
}

// Obtener publicaciones del usuario y amigos
$sql = "SELECT publicaciones.contenido, publicaciones.fecha_publicacion, usuarios.nombre, usuarios.foto_perfil
        FROM publicaciones
        JOIN usuarios ON publicaciones.usuario_id = usuarios.id
        WHERE publicaciones.usuario_id = ?
        OR publicaciones.usuario_id IN (
            SELECT amigo_id FROM amigos WHERE usuario_id = ? AND estado = 'aceptado'
            UNION
            SELECT usuario_id FROM amigos WHERE amigo_id = ? AND estado = 'aceptado'
        )
        ORDER BY publicaciones.fecha_publicacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$publicaciones = $result->fetch_all(MYSQLI_ASSOC);
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
        <a href="amigos.php">Mis Amigos</a> |
        <a href="logout.php">Cerrar sesión</a>
    </header>

    <!-- Sección para crear una publicación -->
    <section class="crear-publicacion">
        <h2>Crear Publicación</h2>
        <div class="publicar-contenedor">
            <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" class="foto-perfil">
            <form method="POST">
                <textarea name="contenido" placeholder="¿Qué estás pensando, <?= htmlspecialchars($usuario['nombre']) ?>?" required></textarea>
                <button type="submit" name="publicar">Publicar</button>
            </form>
        </div>
    </section>

    <!-- Mostrar publicaciones -->
    <section class="publicaciones">
        <h2>Publicaciones</h2>
        <?php if (count($publicaciones) > 0): ?>
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="tweet">
                    <div class="perfil">
                        <img src="<?= htmlspecialchars($publicacion['foto_perfil']) ?>" alt="Foto de perfil" class="foto-perfil">
                        <div class="info-perfil">
                            <span class="nombre"><?= htmlspecialchars($publicacion['nombre']) ?></span>
                            <span class="fecha"><?= date("d M Y H:i", strtotime($publicacion['fecha_publicacion'])) ?></span>
                        </div>
                    </div>
                    <div class="contenido-tweet">
                        <p><?= nl2br(htmlspecialchars($publicacion['contenido'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay publicaciones para mostrar.</p>
        <?php endif; ?>
    </section>

</body>
</html>
