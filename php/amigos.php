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

// Manejo de la solicitud de agregar amigos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_amigo'])) {
    $amigo_id = $_POST['amigo_id'];
    // Verificar si ya son amigos o si ya enviaron una solicitud
    $sql = "SELECT * FROM amigos WHERE (usuario_id = ? AND amigo_id = ?) OR (usuario_id = ? AND amigo_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $usuario_id, $amigo_id, $amigo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Ya eres amigo de esta persona o ya existe una solicitud.";
    } else {
        // Agregar solicitud de amistad
        $sql = "INSERT INTO amigos (usuario_id, amigo_id, estado) VALUES (?, ?, 'pendiente')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $amigo_id);
        $stmt->execute();
        echo "Solicitud de amistad enviada.";
    }

    $stmt->close();
}

// Obtener la lista de usuarios (no amigos)
$sql = "SELECT id, nombre FROM usuarios WHERE id != ? AND id NOT IN (SELECT amigo_id FROM amigos WHERE usuario_id = ? AND estado = 'aceptado') AND id NOT IN (SELECT usuario_id FROM amigos WHERE amigo_id = ? AND estado = 'aceptado')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$usuarios_disponibles = [];
while ($row = $result->fetch_assoc()) {
    $usuarios_disponibles[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amigos</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <header>
        <h1>Amigos</h1>
        <a href="inicio.php">Inicio</a> | 
        <a href="perfil.php">Mi perfil</a> | 
        <a href="logout.php">Cerrar sesión</a>
    </header>

    <!-- Mostrar lista de usuarios disponibles para agregar como amigos -->
    <section class="agregar-amigos">
        <h2>Agregar amigos</h2>
        <?php
        if (count($usuarios_disponibles) > 0) {
            foreach ($usuarios_disponibles as $usuario) {
                echo "<div class='usuario'>";
                echo "<p>" . $usuario['nombre'] . "</p>";
                echo "<form action='amigos.php' method='POST'>";
                echo "<input type='hidden' name='amigo_id' value='" . $usuario['id'] . "'>";
                echo "<button type='submit' name='agregar_amigo'>Agregar</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No hay usuarios disponibles para agregar como amigos.</p>";
        }
        ?>
    </section>
</body>
</html>
