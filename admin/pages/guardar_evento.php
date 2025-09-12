<?php
$data = json_decode(file_get_contents("php://input"), true);
$conexion = new PDO('mysql:host=localhost;dbname=calendar', 'root', '');
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "INSERT INTO events (title, start, end, color) VALUES (?, ?, ?, ?)";
$consulta = $conexion->prepare($sql);
$consulta->execute([
  $data['title'],
  $data['start'],
  $data['end'],
  '#3788d8' // Color por defecto
]);

echo "✅ Recordatorio guardado correctamente";
?>