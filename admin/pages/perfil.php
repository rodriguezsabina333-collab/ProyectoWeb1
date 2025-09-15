<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');


if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok") {
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


if (isset($_GET['tema'])) {
    $tema = $_GET['tema'] === 'oscuro' ? 'oscuro' : 'claro';
    $_SESSION['tema'] = $tema;
} elseif (!isset($_SESSION['tema'])) {
    $_SESSION['tema'] = 'claro';
}
$tema_actual = $_SESSION['tema'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $contra = $conn->real_escape_string($_POST['contra']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $fechanac = $conn->real_escape_string($_POST['fechanac']);

    $sqlUpdate = "UPDATE usuario SET 
        username = ?, 
        contra = ?, 
        nombre = ?, 
        apellido = ?, 
        correo = ?, 
        telefono = ?, 
        fechadenacimiento = ?
        WHERE id_usuario = ?";

    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sssssssi", $username, $contra, $nombre, $apellido, $correo, $telefono, $fechanac, $id_usuario);

    if ($stmtUpdate->execute()) {
        $msg = "Perfil actualizado correctamente.";
        
        $usuario['username'] = $username;
        $usuario['contra'] = $contra;
        $usuario['nombre'] = $nombre;
        $usuario['apellido'] = $apellido;
        $usuario['correo'] = $correo;
        $usuario['telefono'] = $telefono;
        $usuario['fechadenacimiento'] = $fechanac;
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
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/StyleP.css" />
</head>
<body class="<?= $tema_actual ?>">
    <div class="dashboard">
        <aside class="sidebar">
            <img src="<?= URL_BASE ?>/assets/img/<?= $usuario['foto_perfil'] ?? 'default.jpg' ?>" class="foto-perfil-mini" />
            <h2><?= strtoupper($usuario['nombre'] ?? 'Usuario') ?></h2>
            <nav>
                <a href="#perfil" class="active">Perfil</a>
                <a href="#config">Configuración</a>
                <a href="#portafolio">Portafolio</a>
                <a href="../logout.php">Salir</a>
            </nav>
        </aside>

        <main class="content">
            
            <section id="perfil" class="tab active">
                <h1><?= strtoupper($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h1>
                <div class="perfil-info">
                    <img src="<?= URL_BASE ?>/assets/img/<?= $usuario['foto_perfil'] ?? 'default.jpg' ?>" class="foto-perfil" />
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" name="nueva_foto" accept="image/*" />
                        <button type="submit" name="subir_foto" class="btn-editar-img">Editar imagen de perfil</button>
                    </form>
                </div>

                <form method="POST" action="perfil.php" class="form-perfil">
                    <label>Usuario:
                        <input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required />
                    </label>
                    <label>Contraseña:
                        <input type="text" name="contra" value="<?= htmlspecialchars($usuario['contra']) ?>" required />
                    </label>
                    <label>Nombre:
                        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required />
                    </label>
                    <label>Apellido:
                        <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required />
                    </label>
                    <label>Correo:
                        <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required />
                    </label>
                    <label>Teléfono:
                        <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" />
                    </label>
                    <label>Fecha de Nacimiento:
                        <input type="date" name="fechanac" value="<?= htmlspecialchars($usuario['fechadenacimiento']) ?>" />
                    </label>
                    <label>Biografía:
                        <textarea name="biografia"><?= htmlspecialchars($usuario['biografia'] ?? '') ?></textarea>
                    </label>
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                </form>
            </section>

          
            <section id="config" class="tab">
                <h2>Configuraciones del usuario</h2>
                <form method="POST" action="perfil.php">
                    <label>Idioma preferido:
                        <select name="idioma">
                            <option value="es" selected>Español (Latinoamérica)</option>
                            <option value="en">English</option>
                        </select>
                    </label>
                    <label>Zona horaria:
                        <select name="zona">
                            <option value="America/Guatemala" selected>Centroamérica</option>
                            <option value="America/Mexico_City">México</option>
                            <option value="America/New_York">EE.UU. Este</option>
                        </select>
                    </label>
                    <div class="tema-switch">
                        <p>Modo visual:</p>
                        <a href="perfil.php?tema=claro" class="<?= $tema_actual === 'claro' ? 'activo' : '' ?>">Claro</a> | 
                        <a href="perfil.php?tema=oscuro" class="<?= $tema_actual === 'oscuro' ? 'activo' : '' ?>">Oscuro</a>
                    </div>
                    <button type="submit" class="btn-guardar">Guardar configuración</button>
                </form>
            </section>

          
            <section id="portafolio" class="tab">
                <h2>Portafolios electrónicos</h2>
                <p>Próximamente podrás subir tus proyectos destacados aquí.</p>
            </section>
        </main>
    </div>

    <script>
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', e => {
                if (link.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
                    const target = link.getAttribute('href');
                    document.querySelector(target).classList.add('active');
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>