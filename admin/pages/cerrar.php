
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


// Cargar configuración si no está definida
if (!defined('NOMBRE_SITIO')) {
    include_once(__DIR__ . '/../config/config.php');
}
include_once(__DIR__ . '/../config/conexion.php');

// Verificar sesión activa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok" || !isset($_SESSION['nombreUsuario'])) {
    header('Location: ' . URL_BASE . '/../pages/inicioSesion.php');
    exit;
}

$nombreUsuario = $_SESSION['nombreUsuario'];

// Obtener datos del usuario
$sqlUsuario = "SELECT * FROM usuario WHERE username = ?";
$stmt = $conn->prepare($sqlUsuario);
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("⛔ Usuario no encontrado.");
}

$id_usuario = $usuario['id_usuario'];

// Consultar tareas pendientes próximas a vencer
$sqlTareas = "SELECT * FROM tareas WHERE id_usuario = ? AND estatus = 'pendiente' ORDER BY fecha ASC";
$stmt = $conn->prepare($sqlTareas);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultadoTareas = $stmt->get_result();

// Mostrar notificaciones
echo "<h2>🔔 Notificaciones de Tareas Próximas a Vencer</h2>";
echo "<div class='notificaciones'>";

$hoy = strtotime(date("Y-m-d"));
$tieneNotificaciones = false;

while ($tarea = $resultadoTareas->fetch_assoc()) {
    $fechaTarea = strtotime($tarea['fecha']);
    $diasRestantes = ($fechaTarea - $hoy) / (60 * 60 * 24);

    if ($diasRestantes <= 3 && $diasRestantes >= 0) {
        $tieneNotificaciones = true;

        $color = match(strtolower($tarea['prioridad'])) {
            'alta' => '#ff3300',
            'media' => '#ffcc00',
            'baja' => '#66cc66',
            default => '#ccc'
        };

        echo "<div style='border-left: 5px solid $color; padding: 10px; margin-bottom: 10px;'>";
        echo "<strong>{$tarea['nombre']}</strong><br>";
        echo "<small>Fecha límite: {$tarea['fecha']}</small><br>";
        echo "<em>Prioridad: {$tarea['prioridad']}</em><br>";
        echo "<em>Estatus: {$tarea['estatus']}</em><br>";
        echo "<a href='verTarea.php?id={$tarea['id_tarea']}'>👁️ Ver detalles</a>";
        echo "</div>";
    }
}

if (!$tieneNotificaciones) {
    echo "<p>✅ No tienes tareas próximas a vencer.</p>";
}

echo "</div>";

$stmt->close();
$conn->close();
?>
