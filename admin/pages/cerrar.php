<?php
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/config.php');
}

session_start();
session_destroy();
header("Location: inicioSesion.php");
exit; 
?> 