<?php
// Iniciar sesión para poder usar las variables de sesión
session_start();

// Datos de conexión a la base de datos
$host = "localhost";
$usuario_db = "Jaziel"; 
$contraseña_db = "@Sistemas27"; 
$nombre_db = "u892208103_usuarios_db"; 

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];

    // Consulta para verificar si el usuario existe
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña usando password_verify (si está encriptada)
        if (password_verify($contrasena, $usuario["contrasena"])) {
            // Iniciar sesión: almacenar datos del usuario en la sesión
            $_SESSION["usuario_id"] = $usuario["id"];  // Almacenar el ID del usuario
            $_SESSION["usuario_nombre"] = $usuario["nombre"];  // Almacenar el nombre del usuario

            // Redirigir al perfil del usuario o a una página privada
            header("Location: perfil.php");
            exit();  // Detener el script
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
}

// Cerrar la conexión
$conn->close();
?>
