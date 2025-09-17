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
   
    if (isset($_POST['form_type'])) {
        $form_type = $_POST['form_type'];
        
        if ($form_type === 'perfil') {
        
            $username = $conn->real_escape_string($_POST['username']);
            $contra = $conn->real_escape_string($_POST['contra']);
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $apellido = $conn->real_escape_string($_POST['apellido']);
            $correo = $conn->real_escape_string($_POST['correo']);
            $telefono = $conn->real_escape_string($_POST['telefono']);
            $fechadenacimiento = $conn->real_escape_string($_POST['fechadenacimiento']);

        
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                
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
                fechadenacimiento = ?, 
                foto_perfil = ?
                WHERE id_usuario = ?";

            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssssssssi", $username, $contra, $nombre, $apellido, $correo, $telefono, $fechadenacimiento, $fotoPerfil, $id_usuario);

            if ($stmtUpdate->execute()) {
                $msg = "Perfil actualizado correctamente.";
               
                $usuario['username'] = $username;
                $usuario['contra'] = $contra;
                $usuario['nombre'] = $nombre;
                $usuario['apellido'] = $apellido;
                $usuario['correo'] = $correo;
                $usuario['telefono'] = $telefono;
                $usuario['fechadenacimiento'] = $fechadenacimiento;
                $usuario['foto_perfil'] = $fotoPerfil;
            } else {
                $error = "Error al actualizar: " . $stmtUpdate->error;
            }
        } elseif ($form_type === 'configuracion') {
       
            $tema = $conn->real_escape_string($_POST['tema']);
            $notificaciones = isset($_POST['notificaciones']) ? 1 : 0;
            $tipo_notificacion = $conn->real_escape_string($_POST['tipo_notificacion']);

            $sqlConfig = "UPDATE usuario SET tema = ?, notificaciones = ?, tipo_notificacion = ? WHERE id_usuario = ?";
            $stmtConfig = $conn->prepare($sqlConfig);
            $stmtConfig->bind_param("sisi", $tema, $notificaciones, $tipo_notificacion, $id_usuario);

            if ($stmtConfig->execute()) {
                $msg_config = "Configuración guardada correctamente.";
               
                $usuario['tema'] = $tema;
                $usuario['notificaciones'] = $notificaciones;
                $usuario['tipo_notificacion'] = $tipo_notificacion;
            } else {
                $error_config = "Error al guardar configuración: " . $stmtConfig->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../../assets/css/StylenueT.css">
   
</head> 
<body>
    <aside class="sidebar">
        <img src="../../assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto perfil" class="foto-perfil-mini" />
        <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
        <nav>
            <a href="#perfil" class="active">Perfil</a>
            <a href="<?php echo URL_BASE ?>admin/pages/configuracion.php>">Configuración</a> 
            <a href="#portafolio">Portafolio</a>
            <a href="<?php echo URL_BASE ?>index.php">Salir</a>
        </nav>
    </aside>

    <main class="dashboard"> 
      
        <section id="perfil" class="tab active">
            <h1><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h1>

            <form class="form-perfil" method="POST" action="perfil.php" enctype="multipart/form-data">  
                <input type="hidden" name="form_type" value="perfil">    
                     
                <div class="perfil-info">
                    <img src="../../assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto perfil" class="foto-perfil" />
                    <label for="input-foto" class="btn-editar-img" style="cursor: pointer;">Editar imagen de perfil</label>
                    <input type="file" id="input-foto" name="foto_perfil" accept="image/*" style="position:absolute; left:-9999px;" />
                </div>

                <?php if (isset($msg)) echo "<p class='mensaje-exito'>$msg</p>"; ?>
                <?php if (isset($error)) echo "<p class='mensaje-error'>$error</p>"; ?>

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

        
        
    
    </main>  

    <script>
       
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                
                
                document.querySelectorAll('.tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
               
                document.getElementById(targetId).classList.add('active');
                
                
                document.querySelectorAll('nav a').forEach(a => { 
                    a.classList.remove('active');
              }); 
                this.classList.add('active');
            });
        });
    </script>
</body>
</html> 