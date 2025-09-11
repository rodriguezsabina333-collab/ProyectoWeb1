<?php
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/conexion.php');
}

session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ". URL_BASE . "admin/pages/inicioSesion.php"); 
}else{
    if($_SESSION['usuario'] == "ok"){
        $nombreUsuario = $_SESSION["nombreUsuario"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProyectoWeb1</title>
    <link rel="stylesheet" href="<?php echo URL_BASE ?>/assets/css/bootstrap.min.css" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <ul class="nav navbar-nav">
        
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
    <br>
    <div>
        <div class="container">
            <div class="row">