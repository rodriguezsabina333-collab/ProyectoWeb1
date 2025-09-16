<?php
include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $curso = $_POST['curso'];
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $estatus = $_POST['estatus']; 
    $prioridad = $_POST['prioridad']; 
    $etiquetas = $_POST['etiquetas']; 

   
    $stmt = $pdo->prepare("INSERT INTO reentrenodatos (título, descripción, fecha, estatus, prioridad, etiquetas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $fecha, $estatus, $prioridad, $etiquetas]);

    header('Location:  '. URL_BASE . '/../pages/nuevaTarea.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Tareas</title>
    <link rel="stylesheet" href="../../assets/css/StylenueT.css">
</head>
<body>

<h1>Gestión de Tareas</h1>

<form method="POST">
    <input type="hidden" name="accion" value="crear"> 
    
    <label>Curso:</label>
    <input type="text" name="curso" required>  

    <label>Título:</label>
    <input type="text" name="titulo" required>

    <label>Fecha de entrega:</label> 
    <input type="datetime-local" name="fecha" required> 

    <label>Descripción de la tarea:</label>
    <textarea name="descripcion" rows="4" required></textarea>

    <label>Estado:</label>
    <select name="estatus" required>
        <option value="pendiente">Pendiente</option>
        <option value="completada">Completada</option>
    </select>

    <label>Prioridad:</label>
    <select name="prioridad" required>
        <option value="alta">Alta</option>
        <option value="media" selected>Media</option>
        <option value="baja">Baja</option>
    </select>

    <label>Etiquetas (separadas por comas):</label>
    <input type="text" name="etiquetas" placeholder="ej: trabajo, urgente, proyecto">

    <button type="submit">Agregar Tarea</button>
</form>
 
</body>
</html>

<?php
include('../includes/footer.php');
?> 



