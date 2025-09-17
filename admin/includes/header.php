<?php
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/conexion.php');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

?>

<!DOCTYPE html>
<html lang="es" data-tema="<?php echo isset($_SESSION['tema']) ? $_SESSION['tema'] : 'claro'; ?>">
<head> 

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProyectoWeb1</title>
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/StyleH.css">
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
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/exportar.php">Exportar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/recordatorios.php">Recordatorios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?php echo URL_BASE ?>admin/pages/cerrar.php">Cerrar Sesión</a>
                </li>
            </ul>
        </nav>

        <div class="main-content">
            <div class="container">
                <div class="row">