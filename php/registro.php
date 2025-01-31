<?php
// Conectar a la base de datos
$host = "localhost"; // Servidor de base de datos
$usuario_db = "Jaziel"; // Nombre de usuario de la base de datos
$contraseña_db = "@Sistemas27"; // Contraseña de la base de datos
$nombre_db = "u892208103_usuarios_db"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario_db, $contraseña_db, $nombre_db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT); // Encriptar la contraseña

    // Insertar usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES ('$nombre', '$correo', '$contrasena')";

    if ($conn->query($sql) === TRUE) {
        echo "Registro exitoso. <a href='login.html'>Inicia sesión</a>";
    } else {
        echo "Error: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
