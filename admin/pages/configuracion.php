<?php
session_start();
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok" || !isset($_SESSION['nombreUsuario'])) {
    header('Location:  '. URL_BASE . '/../pages/inicioSesion.php'); 
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'guardar_tema') {
    $tema = $_POST['tema'];
    
    $sqlTema = "UPDATE usuario SET tema = ? WHERE id_usuario = ?";
    $stmtTema = $conn->prepare($sqlTema);
    $stmtTema->bind_param("si", $tema, $id_usuario);
    $stmtTema->execute();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

?>

<?php include('../includes/header.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Configuración - Sistema de Tareas</title>
    <link rel="stylesheet" href="../../assets/css/StyleConf.css" />  
</head>
<body>
    <div class="config-container">
        <a href="<?php echo URL_BASE ?>admin/pages/perfil.php" class="back-button">← Volver al Perfil</a>
        
        <div class="config-header">
            <h1>Configuración del Sistema</h1>
            <p>Gestiona tus preferencias y ajustes del sistema</p>
        </div>
        
        <section id="configuracion">
            <form class="form-configuracion" method="POST" action="configuracion.php">
                <input type="hidden" name="form_type" value="configuracion">
                
                <div class="config-option">
                    <h3>Preferencias de Tema</h3>
                    <label for="tema">Modo de visualización:</label>
                    <select id="tema" name="tema">
                        <option value="claro" <?php echo (isset($usuario['tema']) && $usuario['tema'] == 'claro') ? 'selected' : ''; ?>>Modo Claro</option>
                        <option value="oscuro" <?php echo (isset($usuario['tema']) && $usuario['tema'] == 'oscuro') ? 'selected' : ''; ?>>Modo Oscuro</option>
                    </select>
                </div>

                <div class="config-option">
                    <h3>Preferencias de Notificaciones</h3>
                    <label>
                        <input type="checkbox" name="notificaciones" value="1" <?php echo (isset($usuario['notificaciones']) && $usuario['notificaciones'] == 1) ? 'checked' : ''; ?>>
                        Activar recordatorios de tareas
                    </label>
                    
                    <label for="tipo_notificacion">Método de notificación:</label>
                    <select id="tipo_notificacion" name="tipo_notificacion">
                        <option value="navegador" <?php echo (isset($usuario['tipo_notificacion']) && $usuario['tipo_notificacion'] == 'navegador') ? 'selected' : ''; ?>>Notificación del navegador</option>
                        <option value="email" <?php echo (isset($usuario['tipo_notificacion']) && $usuario['tipo_notificacion'] == 'email') ? 'selected' : ''; ?>>Correo electrónico</option>
                    </select>
                </div>
                
                <button type="submit">Guardar Cambios</button>
            </form>
        </section>
    </div>

    <script>
       
        document.getElementById('tema').addEventListener('change', function() {
            const selectedTheme = this.value;
            document.documentElement.setAttribute('data-tema', selectedTheme);
            localStorage.setItem('tema', selectedTheme);
            
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "configuracion.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("action=guardar_tema&tema=" + selectedTheme);
        });

        
        document.addEventListener('DOMContentLoaded', () => {
            const storedTheme = "<?php echo isset($usuario['tema']) ? $usuario['tema'] : 'claro'; ?>";
            localStorage.setItem('tema', storedTheme);
            document.documentElement.setAttribute('data-tema', storedTheme);
        });
    </script>
</body>
</html>
<?php include('../includes/footer.php'); ?>