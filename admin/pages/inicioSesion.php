<?php
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/config.php');
}

session_start();

if ($_POST) {
    $usuario = $_POST['txtUsuario'];
    $contra  = $_POST['txtContra'];

    try {
        $conexion = new PDO("mysql:host=localhost;dbname=pruebas_web;port=3307", "root", "admin");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sentenciaSQL = $conexion->prepare("SELECT * FROM usuario WHERE username = :usuario LIMIT 1");
        $sentenciaSQL->bindParam(":usuario", $usuario);
        $sentenciaSQL->execute();

        $usuarioBD = $sentenciaSQL->fetch(PDO::FETCH_ASSOC);

        if ($usuarioBD && $contra == $usuarioBD['contra']) {
            $_SESSION['usuario'] = "ok";
            $_SESSION['nombreUsuario'] = $usuarioBD['username'];

           
            header('Location: ' . URL_BASE . 'index.php');
            exit;
        } else {
            $mensaje = "Usuario o contraseña incorrectos";
        }
    } catch (Exception $e) {
        $mensaje = "Error en la conexión: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/StyleI_S.css" />
    <link rel="stylesheet" href="../../assets/css/StyleConf.css" />
</head>
<body>
   <h1 class="logo-text">Homework UVG</h1>
   
    <div class="login-container">
        <h2>Iniciar sesión en Homework UVG</h2>

        <?php if (isset($mensaje)) { ?>
            <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                <?php echo $mensaje ?>
            </div>
        <?php } ?>

        <form method="POST">
            <input 
                required 
                type="text" 
                name="txtUsuario" 
                id="txtUsuario" 
                placeholder="Usuario" 
            />
            <input 
                required 
                type="password" 
                name="txtContra" 
                id="txtContra" 
                placeholder="Contraseña" 
            />
            <button type="submit" class="login-btn">Iniciar sesión</button>
            <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
            <div class="divider"></div>
            <a href="<?php echo URL_BASE; ?>admin/pages/registro.php">
                <button type="button" class="create-btn">Crear cuenta nueva</button>
            </a>
        </form>
    </div>
</body>
</html>

