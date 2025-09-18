<?php
include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');

$mensaje = '';
$tarea = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM reentrenosdatos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado && $resultado->num_rows > 0) {
        $tarea = $resultado->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso = $_POST['curso'];
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $estatus = $_POST['estatus'];
    $prioridad = $_POST['prioridad'];
    $etiquetas = $_POST['etiquetas'];

    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $query = "INSERT INTO reentrenosdatos (curso, titulo, descripcion, fecha, estatus, prioridad, etiquetas) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $curso, $titulo, $descripcion, $fecha, $estatus, $prioridad, $etiquetas);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $mensaje = 'Tarea agregada con éxito';
        }
        $stmt->close();
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'editar' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $query = "UPDATE reentrenosdatos SET curso = ?, titulo = ?, descripcion = ?, fecha = ?, estatus = ?, prioridad = ?, etiquetas = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssi", $curso, $titulo, $descripcion, $fecha, $estatus, $prioridad, $etiquetas, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $mensaje = 'Tarea actualizada con éxito';
        }
        $stmt->close();
    }
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

<?php if ($mensaje): ?>
    <div class="mensaje-exito"><?php echo $mensaje; ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="accion" value="<?= $tarea ? 'editar' : 'crear' ?>">
    <?php if ($tarea): ?>
        <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
    <?php endif; ?>

    <label>Curso:</label>
    <select name="curso" required>
        <option value="">-- Selecciona un curso --</option>
        <option value="Base de Datos" <?= $tarea && $tarea['curso'] === 'Base de Datos' ? 'selected' : '' ?>>Base de Datos</option>
        <option value="Filosofía" <?= $tarea && $tarea['curso'] === 'Filosofía' ? 'selected' : '' ?>>Filosofía</option>
        <option value="Matemáticas" <?= $tarea && $tarea['curso'] === 'Matemáticas' ? 'selected' : '' ?>>Matemáticas</option>
        <option value="Programación" <?= $tarea && $tarea['curso'] === 'Programación' ? 'selected' : '' ?>>Programación</option>
    </select>

    <label>Título:</label>
    <input type="text" name="titulo" value="<?= $tarea ? htmlspecialchars($tarea['titulo']) : '' ?>" required>

    <label>Fecha de entrega:</label>
    <input type="datetime-local" name="fecha" value="<?= $tarea ? date('Y-m-d\TH:i', strtotime($tarea['fecha'])) : '' ?>" required>

    <label>Descripción de la tarea:</label>
    <textarea name="descripcion" rows="4" required><?= $tarea ? htmlspecialchars($tarea['descripcion']) : '' ?></textarea>

    <label>Estado:</label>
    <select name="estatus" required>
        <option value="pendiente" <?= $tarea && $tarea['estatus'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="en_proceso" <?= $tarea && $tarea['estatus'] === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
        <option value="finalizado" <?= $tarea && $tarea['estatus'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
    </select>

    <label>Prioridad:</label>
    <select name="prioridad" required>
        <option value="alta" <?= $tarea && $tarea['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
        <option value="media" <?= $tarea && $tarea['prioridad'] === 'media' ? 'selected' : '' ?>>Media</option>
        <option value="baja" <?= $tarea && $tarea['prioridad'] === 'baja' ? 'selected' : '' ?>>Baja</option>
    </select>

    <label>Etiquetas (separadas por comas):</label>
    <input type="text" name="etiquetas" value="<?= $tarea ? htmlspecialchars($tarea['etiquetas']) : '' ?>">

    <button type="submit"><?= $tarea ? 'Guardar Cambios' : 'Agregar Tarea' ?></button>
</form>

</body>
</html>

<?php
include('../includes/footer.php');
?>