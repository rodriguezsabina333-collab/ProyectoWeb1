<?php
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/config.php');
}

    $host = "localhost"; //Indico la ip del servidor
    $port = 3307;
    $user = "root";
    $pass = "admin";
    $dabase = "pruebas_web";

    $conn = new mysqli($host, $user, $pass, $dabase, $port);

    if($conn->connect_error){
        die("Conexion fallida" . $conn->connect_error);
    }
   // echo "Conexion Exitosa de la DB";

?> 

