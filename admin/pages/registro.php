<?php
$host = "localhost";
<<<<<<< HEAD
$usuario = "root";       // Usuario por defecto en XAMPP
$clave = "";              // Contraseña por defecto en XAMPP
$bd = "mi_base";          // Asegúrate que este nombre coincida con tu base de datos
=======
$usuario = "root";   // cambia por tu usuario
$clave = "admin";         // cambia por tu contraseña
$bd = "pruebas_web";     // cambia por tu base de datos

>>>>>>> ecb720471bd41307e91d8369c240d7ebfaf1ef64

$conexion = new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Recibir datos del formulario
$username = $_POST['username'];
$contra = $_POST['contra'];
$tipo_user = $_POST['tipo_user'];
$correo = $_POST['correo'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$telefono = $_POST['telefono'];
$fechadenacimiento = $_POST['fechadenacimiento'];

// Encriptar la contraseña
$contra_hash = password_hash($contra, PASSWORD_DEFAULT);

// Preparar la consulta
$stmt = $conexion->prepare("INSERT INTO usuarios (username, contra, tipo_user, correo, nombre, apellido, telefono, fechadenacimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $username, $contra_hash, $tipo_user, $correo, $nombre, $apellido, $telefono, $fechadenacimiento);

// Ejecutar y verificar
if ($stmt->execute()) {
    echo "<script>alert('Usuario registrado correctamente'); window.location.href='registro.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
