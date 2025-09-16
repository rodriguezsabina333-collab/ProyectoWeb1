<?php
include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $curso = $_POST['curso'];
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];

    $stmt = $pdo->prepare("INSERT INTO tareas (curso, titulo, fecha, descripcion) VALUES (?, ?, ?, ?)");
    $stmt->execute([$curso, $titulo, $fecha, $descripcion]);

    header("Location: misTareas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Tareas</title>
<link rel="stylesheet" href="/assets/css/StyleDB.css">
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
    <input type="date" name="fecha" required>
    <label>Descripción de la tarea:</label>
    <textarea name="descripcion" rows="4" required></textarea>
    <button type="submit">Agregar</button>
</form>

<div class="volver-container">
    <a href="../../index.php" class="volver-btn">⬅ Volver</a>
</div>

</body>
</html>

<?php
include('../includes/footer.php');
?>




