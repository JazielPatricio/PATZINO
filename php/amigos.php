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

// Manejo de solicitudes de amistad y eliminación de amigos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['agregar_amigo'])) {
        $amigo_id = $_POST['amigo_id'];
        $sql = "INSERT INTO amigos (usuario_id, amigo_id, estado) VALUES (?, ?, 'pendiente')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $amigo_id);
        $stmt->execute();
    }
    if (isset($_POST['aceptar_amigo'])) {
        $solicitud_id = $_POST['solicitud_id'];
        $sql = "UPDATE amigos SET estado = 'aceptado' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $solicitud_id);
        $stmt->execute();
    }
    if (isset($_POST['rechazar_amigo'])) {
        $solicitud_id = $_POST['solicitud_id'];
        $sql = "DELETE FROM amigos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $solicitud_id);
        $stmt->execute();
    }
    if (isset($_POST['eliminar_amigo'])) {
        $amigo_id = $_POST['amigo_id'];
        $sql = "DELETE FROM amigos WHERE (usuario_id = ? AND amigo_id = ?) OR (usuario_id = ? AND amigo_id = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $usuario_id, $amigo_id, $amigo_id, $usuario_id);
        $stmt->execute();
    }
    header("Location: amigos.php"); // Redirigir para actualizar la página
    exit();
}

// Obtener todos los usuarios disponibles para enviar solicitud, excluyendo amigos actuales
$sql = "SELECT id, nombre FROM usuarios WHERE id != ? AND id NOT IN (
            SELECT CASE WHEN usuario_id = ? THEN amigo_id ELSE usuario_id END 
            FROM amigos WHERE (usuario_id = ? OR amigo_id = ?) AND estado = 'aceptado')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $usuario_id, $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuarios_disponibles = $result->fetch_all(MYSQLI_ASSOC);

// Obtener solicitudes pendientes
$sql = "SELECT amigos.id as solicitud_id, usuarios.id as usuario_id, usuarios.nombre 
        FROM amigos 
        JOIN usuarios ON amigos.usuario_id = usuarios.id 
        WHERE amigo_id = ? AND estado = 'pendiente'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$solicitudes_pendientes = $result->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de amigos aceptados
$sql = "SELECT usuarios.id, usuarios.nombre FROM amigos 
        JOIN usuarios ON (amigos.usuario_id = usuarios.id OR amigos.amigo_id = usuarios.id) 
        WHERE (amigos.usuario_id = ? OR amigos.amigo_id = ?) AND amigos.estado = 'aceptado' AND usuarios.id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$amigos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amigos</title>
</head>
<body>
    <h1>Amigos</h1>
    <h2>Agregar amigos</h2>
    <?php foreach ($usuarios_disponibles as $usuario): ?>
        <form method="POST">
            <input type="hidden" name="amigo_id" value="<?= $usuario['id'] ?>">
            <button type="submit" name="agregar_amigo">Agregar a <?= htmlspecialchars($usuario['nombre']) ?></button>
        </form>
    <?php endforeach; ?>

    <h2>Solicitudes de amistad</h2>
    <?php foreach ($solicitudes_pendientes as $solicitud): ?>
        <form method="POST">
            <p><?= htmlspecialchars($solicitud['nombre']) ?> quiere ser tu amigo.</p>
            <input type="hidden" name="solicitud_id" value="<?= $solicitud['solicitud_id'] ?>">
            <button type="submit" name="aceptar_amigo">Aceptar</button>
            <button type="submit" name="rechazar_amigo">Rechazar</button>
        </form>
    <?php endforeach; ?>

    <h2>Lista de Amigos</h2>
    <?php foreach ($amigos as $amigo): ?>
        <p><?= htmlspecialchars($amigo['nombre']) ?></p>
        <form method="POST">
            <input type="hidden" name="amigo_id" value="<?= $amigo['id'] ?>">
            <button type="submit" name="eliminar_amigo">Eliminar</button>
        </form>
    <?php endforeach; ?>
</body>
</html>
