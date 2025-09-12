<?php
session_start();

// Mantener funcionalidad para agregar tareas (aunque no se muestren)
if (!isset($_SESSION['tareas'])) {
    $_SESSION['tareas'] = [];
}

if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $_SESSION['tareas'][] = [
        "curso" => $_POST['curso'],
        "titulo" => $_POST['titulo'],
        "fecha" => $_POST['fecha']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Tareas</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f8f4;
        margin: 0;
        padding: 0;
    }
    h1 {
        text-align: center;
        color: #2e7d32;
        padding: 20px 0;
    }
    form {
        width: 90%;
        margin: 20px auto;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    label {
        font-weight: bold;
        color: #2e7d32;
    }
    input {
        width: 100%;
        padding: 8px;
        margin: 5px 0 15px 0;
        border: 1px solid #c8e6c9;
        border-radius: 4px;
    }
    button, .volver-btn {
        background-color: #388e3c;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    button:hover, .volver-btn:hover {
        background-color: #2e7d32;
    }
    .volver-container {
        text-align: center;
        margin-bottom: 20px;
    }
</style>
</head>
<body>

<h1>📋 Gestión de Tareas</h1>

<!-- Formulario para agregar tarea -->
<form method="POST">
    <input type="hidden" name="accion" value="crear">
    <label>Curso:</label>
    <input type="text" name="curso" required>
    <label>Título:</label>
    <input type="text" name="titulo" required>
    <label>Fecha de entrega:</label>
    <input type="text" name="fecha" required>
    <button type="submit">Agregar</button>
</form>

<!-- Botón Volver -->
<div class="volver-container">
    <a href="../../index.php" class="volver-btn">⬅ Volver</a>
</div>

</body>
</html>





