<?php

$host = "localhost";
$usuario = "root";   // cambia por tu usuario
$clave = "admin";         // cambia por tu contraseña
$bd = "pruebas_web";     // cambia por tu base de datos


$conexion = new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre = $_POST['Nombre'];
$apellido = $_POST['Apellido'];
$contrasena = $_POST['Contrasena'];
$correo = $_POST['Correo'];
$telefono = $_POST['Telefono'];
$fecha_nacimiento = $_POST['Fechadenacimiento'];


$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);


$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, contrasena, correo, telefono, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre, $apellido, $contrasena_hash, $correo, $telefono, $fecha_nacimiento);


if ($stmt->execute()) {
    echo "<script>alert('Usuario registrado correctamente'); window.location.href='registro.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
