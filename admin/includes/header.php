<?php
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/conexion.php');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejo de cambio de tema
if (isset($_GET['tema'])) {
    $_SESSION['tema'] = ($_GET['tema'] === 'oscuro') ? 'oscuro' : 'claro';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // Limpia el GET
    exit;
}

if (!isset($_SESSION['usuario'])) {
    header("Location: " . URL_BASE . "admin/pages/inicioSesion.php");
    exit;
} else {
    if ($_SESSION['usuario'] === "ok") {
        $nombreUsuario = $_SESSION["nombreUsuario"];
    }
}

$currentFile = basename($_SERVER['PHP_SELF']);
$isIndex = $currentFile === 'index.php';
$navClass = $isIndex ? 'navbar-top' : 'sidebar';
$layoutClass = $isIndex ? 'layout-top' : 'layout';

// Tema activo
$tema = isset($_SESSION['tema']) ? $_SESSION['tema'] : 'oscuro';
?>

<!DOCTYPE html>
<html lang="es" data-tema="<?php echo $tema; ?>">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProyectoWeb1</title>
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/StyleH.css">
    <link rel="stylesheet" href="../../assets/css/StyleConf.css" />
</head>

<body>
    <div class="<?php echo $layoutClass; ?>">

        <nav class="<?php echo $navClass; ?>">
            <div class="logo">UVG</div>

            <ul class="nav-links">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>index.php">
                        <i class="bi bi-house-door-fill"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/dashboard.php">Mis Tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/nuevaTarea.php">Nueva Tarea</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/perfil.php">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/configuracion.php">Configuración</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/recordatorios.php">Recordatorios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?php echo URL_BASE ?>admin/pages/cerrar.php">Cerrar Sesión</a>
                </li>
                <!-- Botón cambiar tema -->
                <li class="nav-item">
                    <?php if ($tema === 'claro'): ?>
                        <a class="nav-link" href="?tema=oscuro">
                            <i class="bi bi-moon-fill"></i> Oscuro
                        </a>
                    <?php else: ?>
                        <a class="nav-link" href="?tema=claro">
                            <i class="bi bi-sun-fill"></i> Claro
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>

        <div class="main-content">
            <div class="container">
                <div class="row">
