<?php
session_start();
include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok" || !isset($_SESSION['nombreUsuario'])) {
    header("Location: inicioSesion.php");
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
    $telefono = !empty($_POST['telefono']) ? $conn->real_escape_string($_POST['telefono']) : null;
    $fechadenacimiento = !empty($_POST['fechadenacimiento']) ? $conn->real_escape_string($_POST['fechadenacimiento']) : null;
    $fotoPerfil = isset($_POST['foto_opcion']) ? $_POST['foto_opcion'] : $fotoPerfil;

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
    $stmtUpdate->bind_param("ssssssssi", $username, $contra, $nombre, $apellido, $correo, $telefono, $fechadenacimiento, $fotoPerfil, $id_usuario);

    if ($stmtUpdate->execute()) {
        $msg = "Perfil actualizado correctamente.";
        $usuario['foto_perfil'] = $fotoPerfil;
    } else {
        $error = "Error al actualizar: " . $stmtUpdate->error;
    }
}

// Contador de notificaciones (tareas que vencen hoy)
$sqlNotif = "SELECT COUNT(*) AS total FROM reentrenosdatos WHERE DATE(fecha) = CURDATE()";
$resNotif = $conn->query($sqlNotif);
$totalNotif = 0;
if ($resNotif && $rowNotif = $resNotif->fetch_assoc()) {
    $totalNotif = $rowNotif['total'];
}
?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="../../assets/css/StyleP.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head> 
<body>  
  
    <h2>Editar Perfil</h2> 
 
  <main class="form-container">
    <div class="iconos-top">
      <a href="configuracion.php"><i class="fas fa-cog"></i></a>
      <a href="notificaciones.php" class="notif-icon">
        <i class="fas fa-bell"></i>
        <?php if ($totalNotif > 0): ?>
          <span class="badge"><?php echo $totalNotif; ?></span>
        <?php endif; ?>
      </a>
     </div>
  
    <?php if (isset($msg)) echo "<p class='mensaje-exito'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='mensaje-error'>$error</p>"; ?>

    <form method="POST" action="perfil.php">
      <div class="perfil-info">
        <div class="imagen-circular">
          <img src="../../assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto perfil" class="foto-perfil" id="fotoPerfilPreview" />
        </div>
        <p class="texto-opcion">Selecciona tu imagen de perfil:</p>
        <div class="opciones-imagen">
          <label>
            <input type="radio" name="foto_opcion" value="femenino.png" <?= $usuario['foto_perfil'] === 'femenino.png' ? 'checked' : '' ?> />
            <img src="../../assets/img/femenino.png" alt="Femenino" class="miniatura" />
          </label>
          <label>
            <input type="radio" name="foto_opcion" value="masculino.png" <?= $usuario['foto_perfil'] === 'masculino.png' ? 'checked' : '' ?> />
            <img src="../../assets/img/masculino.png" alt="Masculino" class="miniatura" />
          </label>
        </div>
      </div>
 
      <div class="form-group"><label>Usuario</label><input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required /></div>
      <div class="form-group"><label>Contraseña</label><input type="password" name="contra" value="<?= htmlspecialchars($usuario['contra']) ?>" required /></div>
      <div class="form-group"><label>Nombre</label><input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required /></div>
      <div class="form-group"><label>Apellido</label><input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required /></div>
      <div class="form-group"><label>Correo</label><input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required /></div>
      <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" /></div>
      <div class="form-group"><label>Fecha de nacimiento</label><input type="date" name="fechadenacimiento" value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>" /></div>

      <div class="form-group">
        <button type="submit" class="btn-guardar">Guardar cambios</button>
      </div>
    </form>
  </main>

  <style>
    .notif-icon {
      position: relative;
      display: inline-block;
    }
    .notif-icon .badge {
      position: absolute;
      top: -6px;
      right: -6px;
      background: red;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 12px;
    }
  </style>

  <script>
    const radios = document.querySelectorAll('input[name="foto_opcion"]');
    const preview = document.getElementById('fotoPerfilPreview');

    radios.forEach(radio => {
      radio.addEventListener('change', () => {
        preview.src = `../../assets/img/${radio.value}`;
      });
    });
  </script>
</body>
</html>
