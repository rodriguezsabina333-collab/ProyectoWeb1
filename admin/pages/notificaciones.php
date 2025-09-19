<?php include('../includes/header.php'); ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("No hay usuario en sesión");
}
$idUsuario = $_SESSION['id_usuario'];

if (isset($_GET['marcar']) && is_numeric($_GET['marcar'])) {
    $sql = "UPDATE reentrenosdatos SET leida = 1 WHERE id = :id AND usuario_id = :usuario";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->execute([":id" => $_GET['marcar'], ":usuario" => $idUsuario]);
    }
    exit;
}

$ahora = date("Y-m-d H:i:s");
$proximaHora = date("Y-m-d H:i:s", strtotime("+1 hour"));

$sql = "SELECT id, titulo, fecha FROM reentrenosdatos 
        WHERE usuario_id = :usuario 
        AND fecha BETWEEN :ahora AND :proxima
        AND estatus = 'pendiente'
        AND leida = 0";

$stmt = $conn->prepare($sql);
$tareas = [];
if ($stmt) {
    $stmt->execute([":usuario" => $idUsuario, ":ahora" => $ahora, ":proxima" => $proximaHora]);
    $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones</title>
</head>
<body>
<script>
const notificaciones = <?php echo json_encode($tareas); ?>;
if (notificaciones.length > 0) {
    notificaciones.forEach(n => {
        if (confirm("⚠️ La tarea '" + n.titulo + "' está próxima a vencer.\nFecha: " + n.fecha)) {
            fetch("?marcar=" + n.id);
        }
    });
}
</script>
</body>
</html>


<?php include('../includes/footer.php'); ?>