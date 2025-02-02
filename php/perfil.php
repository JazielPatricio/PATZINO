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

// Obtener datos del usuario
$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT nombre, correo, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Subir foto de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_perfil'])) {
    $target_dir = "../imagenes/perfiles/";
    $target_file = $target_dir . basename($_FILES["foto_perfil"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (getimagesize($_FILES["foto_perfil"]["tmp_name"]) === false) {
        echo "El archivo no es una imagen válida.";
    } elseif ($_FILES["foto_perfil"]["size"] > 2000000) {
        echo "El archivo es demasiado grande. Máximo permitido: 2MB.";
    } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "Solo se permiten imágenes JPG, JPEG, PNG y GIF.";
    } else {
        if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file)) {
            $sql_update = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $target_file, $usuario_id);
            $stmt_update->execute();
            $stmt_update->close();
            echo "Foto de perfil actualizada exitosamente.";
            $_SESSION["foto_perfil"] = basename($target_file);
        } else {
            echo "Hubo un error al subir la foto.";
        }
    }
}

// Obtener publicaciones del usuario
$sql_publicaciones = "SELECT contenido, fecha_publicacion FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC";
$stmt_publicaciones = $conn->prepare($sql_publicaciones);
$stmt_publicaciones->bind_param("i", $usuario_id);
$stmt_publicaciones->execute();
$result_publicaciones = $stmt_publicaciones->get_result();

// Cerrar conexiones
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
    <link rel="stylesheet" href="../css/perfil2.css"> <!-- Archivo de estilos -->
</head>
<body>

<!-- Menú de navegación -->
<nav class="navbar">
    <div class="logo">MiRedSocial</div>
    <ul>
        <li><a href="inicio.php">Inicio</a></li>
        <li><a href="amigos.php">Mis Amigos</a></li>
        <li><a href="solicitudes.php">Solicitudes</a></li>
        <li><a href="perfil.php">Mi Perfil</a></li>
        <li><a href="logout.php" class="logout">Cerrar Sesión</a></li>
    </ul>
</nav>

<div class="contenedor">
    <h2>Mi Perfil</h2>

    <!-- Foto de perfil principal (Centrada y grande) -->
    <div class="foto-perfil">
        <img class="foto-perfil-principal" src="<?php echo isset($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : '../imagenes/perfiles/default.jpg'; ?>" alt="Foto de perfil">
    </div>

    <div class="info">
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario["nombre"]); ?></p>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario["correo"]); ?></p>
    </div>

    <!-- Cambiar foto de perfil -->
    <div class="subir-foto">
        <h3>Cambiar foto de perfil</h3>
        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <label for="foto_perfil" class="btn-subir">Seleccionar foto</label>
            <input type="file" name="foto_perfil" id="foto_perfil" required>
            <button type="submit">Subir Foto</button>
        </form>
    </div>

    <!-- Publicaciones -->
    <div class="publicaciones">
        <h2>Mis Publicaciones</h2>
        <?php if ($result_publicaciones->num_rows > 0): ?>
            <?php while ($publicacion = $result_publicaciones->fetch_assoc()): ?>
                <div class="tweet">
                    <div class="tweet-header">
                        <img class="foto-perfil-publicacion" src="<?php echo isset($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : '../imagenes/perfiles/default.jpg'; ?>" alt="Foto de perfil">
                        <div class="info-perfil">
                            <span class="nombre"><?php echo htmlspecialchars($usuario["nombre"]); ?></span>
                            <span class="fecha"><?php echo htmlspecialchars($publicacion["fecha_publicacion"]); ?></span>
                        </div>
                    </div>
                    <div class="contenido-tweet">
                        <p><?php echo nl2br(htmlspecialchars($publicacion["contenido"])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No tienes publicaciones aún.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
