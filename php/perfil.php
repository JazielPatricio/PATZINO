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
$sql = "SELECT nombre, correo, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Subir foto de perfil (si se ha enviado un archivo)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_perfil'])) {
    // Verificar si se subió una imagen
    $target_dir = "../imagenes/perfiles/";
    $target_file = $target_dir . basename($_FILES["foto_perfil"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Comprobar que el archivo es una imagen
    if (getimagesize($_FILES["foto_perfil"]["tmp_name"]) === false) {
        echo "El archivo no es una imagen válida.";
    } else {
        // Verificar el tamaño del archivo (max 2MB)
        if ($_FILES["foto_perfil"]["size"] > 2000000) {
            echo "El archivo es demasiado grande. Máximo permitido: 2MB.";
        } else {
            // Permitir ciertos formatos de imagen
            if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
                echo "Solo se permiten imágenes JPG, JPEG, PNG y GIF.";
            } else {
                // Subir el archivo
                if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file)) {
                    // Actualizar la base de datos con la nueva foto de perfil
                    $sql_update = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("si", $target_file, $usuario_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                    echo "Foto de perfil actualizada exitosamente.";
                } else {
                    echo "Hubo un error al subir la foto.";
                }
            }
        }
    }
}

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
        <!-- Mostrar foto de perfil -->
        <div class="foto-perfil">
            <img src="<?php echo '../imagenes/perfiles/' . $usuario['foto_perfil']; ?>" alt="Foto de perfil" width="150" height="150">
        </div>
        <p><strong>Nombre:</strong> <?php echo $usuario["nombre"]; ?></p>
        <p><strong>Correo:</strong> <?php echo $usuario["correo"]; ?></p>
    </section>

    <!-- Subir foto de perfil -->
    <section class="subir-foto">
        <h3>Cambiar foto de perfil</h3>
        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="foto_perfil" required>
            <button type="submit">Subir Foto</button>
        </form>
    </section>

    <!-- Mostrar publicaciones propias -->
    <section class="publicaciones">
        <h2>Mis Publicaciones</h2>
        <?php
        if ($result_publicaciones->num_rows > 0) {
            // Mostrar las publicaciones
            while ($publicacion = $result_publicaciones->fetch_assoc()) {
                echo "<div class='tweet'>";
                // Mostrar foto de perfil junto a la publicación
                echo "<div class='perfil'>";
                echo "<img class='foto-perfil' src='../imagenes/perfiles/" . $usuario["foto_perfil"] . "' alt='Foto de perfil'>";
                echo "<div class='info-perfil'>";
                echo "<span class='nombre'>" . $usuario["nombre"] . "</span>";
                echo "<span class='fecha'>" . $publicacion["fecha_publicacion"] . "</span>";
                echo "</div>";  // Cierre de info-perfil
                echo "</div>";  // Cierre de perfil
                echo "<div class='contenido-tweet'><p>" . nl2br($publicacion["contenido"]) . "</p></div>";  // Contenido de la publicación
                echo "</div>";  // Cierre de tweet
            }
        } else {
            echo "<p>No tienes publicaciones aún.</p>";
        }
        ?>
    </section>
</body>
</html>
