<?php
include_once(__DIR__ . '/../config/conexion.php');

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $contra = $_POST['contra'] ?? ''; 
    $correo = $_POST['correo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;

    if (!empty($username) && !empty($contra)) {
      
        $query = "INSERT INTO usuario (username, contra, correo, nombre, apellido, telefono, fecha_nacimiento) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            $mensaje = "Error en la consulta SQL: " . $conn->error;
        } else {
            $stmt->bind_param("sssssss", $username, $contra, $correo, $nombre, $apellido, $telefono, $fecha_nacimiento);

            if ($stmt->execute()) {
                session_start();
                $_SESSION['usuario'] = "ok";
                $_SESSION['nombreUsuario'] = $username;

                header("Location: /PROYECTOWEB1/index.php?registro=ok");
                exit;
            } else {
                $mensaje = "Error al registrar: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $mensaje = "Usuario y contraseña son obligatorios";
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Homework UVG - Registro -</title>
    <link rel="stylesheet" href="../../assets/css/StyleReg.css">
</head>

<body>
    <div class="logo-text">Homework UVG</div>

    <div class="container">
        <div class="header-section">

            <div class="sub-text">Crea una cuenta<br>Es rápido y fácil.</div>
        </div>

        <div class="card-body">
            <?php if ($mensaje) { ?>
                <div class="alert alert-warning"><?php echo $mensaje; ?></div>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="contra" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="correo" class="form-control">
                </div>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control">
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input type="text" name="apellido" class="form-control">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="form-group">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="fechaNacimiento" class="form-control">
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success btn-block">Registrarse</button>
                    <a href="inicioSesion.php" class="login-link">¿Ya tienes una cuenta?</a>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
    </div>
</body>

</html>