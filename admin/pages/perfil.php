<?php
session_start();
include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');


if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok" || !isset($_SESSION['nombreUsuario'])) {
    header("Location: ../inicioSesion.php");
    exit;
}

$nombreUsuario = $_SESSION['nombreUsuario'];


$sqlUsuario = "SELECT * FROM usuario WHERE username = ?";
$stmt = $conn->prepare($sqlUsuario);
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

$id_usuario = $usuario['id_usuario'];
$fotoPerfil = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'default.png';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $contra = $conn->real_escape_string($_POST['contra']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $fecha_nacimiento = $conn->real_escape_string($_POST['fecha_nacimiento']);

    
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $archivoTmp = $_FILES['foto_perfil']['tmp_name'];
        $nombreArchivo = basename($_FILES['foto_perfil']['name']);
        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $permitidas)) {
           
            foreach ($permitidas as $extOld) {
                $archivoViejo = __DIR__ . "/../../assets/img/user_" . $id_usuario . "." . $extOld;
                if (file_exists($archivoViejo)) {
                    unlink($archivoViejo);
                }
            }

            $nuevoNombre = "user_" . $id_usuario . "." . $ext;
            $rutaDestino = __DIR__ . "/../../assets/img/" . $nuevoNombre;

            if (move_uploaded_file($archivoTmp, $rutaDestino)) {
                $fotoPerfil = $nuevoNombre;
            } else {
                $error = "Error al subir la imagen.";
            }
        } else {
            $error = "Formato de imagen no permitido. Solo jpg, png, gif.";
        }
    }

    
    if (!isset($fotoPerfil)) {
        $fotoPerfil = $usuario['foto_perfil'];
    }

    
    $sqlUpdate = "UPDATE usuario SET 
        username = ?, 
        contra = ?, 
        nombre = ?, 
        apellido = ?, 
        correo = ?, 
        telefono = ?, 
        fecha_nacimiento = ?, 
        foto_perfil = ?
        WHERE id_usuario = ?";

    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssssssssi", $username, $contra, $nombre, $apellido, $correo, $telefono, $fecha_nacimiento, $fotoPerfil, $id_usuario);

    if ($stmtUpdate->execute()) {
        $msg = "Perfil actualizado correctamente.";
        $usuario['username'] = $username; 
        $usuario['contra'] = $contra;
        $usuario['nombre'] = $nombre;
        $usuario['apellido'] = $apellido;
        $usuario['correo'] = $correo;
        $usuario['telefono'] = $telefono;
        $usuario['fecha_nacimiento'] = $fecha_nacimiento;
        $usuario['foto_perfil'] = $fotoPerfil;
    } else {
        $error = "Error al actualizar: " . $stmtUpdate->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../../assets/css/StyleP.css" />
</head>
<body class="<?= $tema_actual ?>">
    <aside class="sidebar">
        <img src="../../assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto perfil" class="foto-perfil-mini" />
        <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
        <nav>
            <a href="#perfil" class="active" onclick="mostrarTab(event, 'perfil')">Perfil</a>
            <a href="#config" onclick="mostrarTab(event, 'config')">Configuración</a>
            <a href="#portafolio" onclick="mostrarTab(event, 'portafolio')">Portafolio</a>
            <a href="<?php echo URL_BASE ?>index.php">Salir</a>
        </nav>
    </aside>

    <main class="dashboard">
        <section id="perfil" class="tab active">
            <h1><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h1>
            <div class="perfil-info">
                <img src="../../assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto perfil" class="foto-perfil" />
                <button class="btn-editar-img" onclick="document.getElementById('input-foto').click()">Editar imagen de perfil</button>
            </div>

            <?php if (isset($msg)) echo "<p class='mensaje-exito'>$msg</p>"; ?>
            <?php if (isset($error)) echo "<p class='mensaje-error'>$error</p>"; ?>

            <form class="form-perfil" method="POST" action="perfil.php" enctype="multipart/form-data">
                <input type="file" id="input-foto" name="foto_perfil" accept="image/*" style="display:none" />

                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required />

                <label for="contra">Contraseña</label>
                <input type="password" id="contra" name="contra" value="<?= htmlspecialchars($usuario['contra']) ?>" required />

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required />

                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required />

                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required />

                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" />

                <label for="fechadenacimiento">Fecha de nacimiento</label>
                <input type="date" id="fechadenacimiento" name="fechadenacimiento" value="<?= htmlspecialchars($usuario['fechadenacimiento']) ?>" />

                <button type="submit" class="btn-guardar">Guardar cambios</button>
            </form>
        </section>

        <section id="config" class="tab">
            <h1>Configuración</h1>
            <p>Aquí puedes poner las opciones de configuración del usuario.</p>
        </section>

        <section id="portafolio" class="tab">
            <h1>Portafolio</h1>
            <p>Aquí puedes mostrar el portafolio del usuario.</p>
        </section>
    </main>

    <script>
        function mostrarTab(evt, tabNombre) {
            evt.preventDefault();
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.sidebar nav a').forEach(link => link.classList.remove('active'));
            document.getElementById(tabNombre).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>