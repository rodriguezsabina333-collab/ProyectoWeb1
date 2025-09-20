<?php
include_once(__DIR__ . "/../config/conexion.php");

header('Content-Type: application/json');

$datos = json_decode(file_get_contents("php://input"), true);

if (!isset($datos['id']) || !isset($datos['nueva_fecha'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$id = (int)$datos['id'];
$nueva_fecha = explode('T', $datos['nueva_fecha'])[0];

$fechaValida = DateTime::createFromFormat('Y-m-d', $nueva_fecha);
if (!$fechaValida || $fechaValida->format('Y-m-d') !== $nueva_fecha) {
    echo json_encode(['success' => false, 'error' => 'Formato de fecha inválido']);
    exit;
}

$stmt = $conn->prepare("UPDATE reentrenosdatos SET fecha = ? WHERE id = ?");
$stmt->bind_param("si", $nueva_fecha, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
?>
 
