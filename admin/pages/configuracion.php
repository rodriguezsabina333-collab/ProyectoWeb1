<?php
session_start();
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok" || !isset($_SESSION['nombreUsuario'])) {
    header('Location: ' . URL_BASE . '/../pages/inicioSesion.php');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'configuracion') {
    $tema = $_POST['tema'];

    $sqlTema = "UPDATE usuario SET tema = ? WHERE id_usuario = ?";
    $stmtTema = $conn->prepare($sqlTema);
    $stmtTema->bind_param("si", $tema, $id_usuario);
    $stmtTema->execute();

   
    header("Location: configuracion.php");
    exit;
}
?> 

<?php include('../includes/header.php'); ?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Configuración - Sistema de Tareas</title>
    <link rel="stylesheet" href="../../assets/css/StyleConf.css" />
</head>
<body>
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
                    <option value="claro" <?php echo ($usuario['tema'] == 'claro') ? 'selected' : ''; ?>>Modo Claro</option>
                    <option value="oscuro" <?php echo ($usuario['tema'] == 'oscuro') ? 'selected' : ''; ?>>Modo Oscuro</option>
                </select>
            </div>

            <div class="config-option">
                <button type="submit">Guardar Cambios</button>
            </div>
        </form>
    </section>
</body>
</html>
<?php include('../includes/footer.php'); ?>