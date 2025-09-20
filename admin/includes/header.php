<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/../config/conexion.php');

$tema = 'claro';

if (isset($_SESSION['usuario']) && $_SESSION['usuario'] === "ok" && isset($_SESSION['nombreUsuario'])) {
    $nombreUsuario = $_SESSION['nombreUsuario'];
    $sql = "SELECT tema FROM usuario WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $tema = $fila['tema'];
    }
}

$currentFile = basename($_SERVER['PHP_SELF']);
$isIndex = $currentFile === 'index.php';
$navClass = $isIndex ? 'navbar-top' : 'sidebar';
$layoutClass = $isIndex ? 'layout-top' : 'layout';
?>

<!DOCTYPE html>
<html lang="es" data-tema="<?php echo $tema; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProyectoWeb1</title>
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/StyleH.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
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
                <li class="nav-item"> 
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/dashboard.php">
                        <i class="bi bi-card-checklist"></i> Mis Tareas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/nuevaTarea.php">
                        <i class="bi bi-plus-square"></i> Nueva Tarea
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/perfil.php">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE ?>admin/pages/recordatorios.php">
                        <i class="bi bi-calendar-check"></i> Recordatorios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?php echo URL_BASE ?>admin/pages/cerrar.php">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </nav>
        <div class="main-content">
            <div class="container">
                <div class="row">