<?php
session_start();
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== "ok" || !isset($_SESSION['nombreUsuario'])) {
    header('Location: ' . URL_BASE . '/../pages/inicioSesion.php');
    exit;
}

$nombreUsuario = $_SESSION['nombreUsuario'];

$sqlUsuario = "SELECT * FROM usuario WHERE username = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("s", $nombreUsuario);
$stmtUsuario->execute();
$resultadoUsuario = $stmtUsuario->get_result();
$usuario = $resultadoUsuario->fetch_assoc();

$id_usuario = $usuario['id_usuario'];

// Marcar notificación como leída
if (isset($_GET['aceptar'])) {
    $idAceptar = intval($_GET['aceptar']);
    $sqlAceptar = "UPDATE reentrenosdatos SET estatus = 'leido' WHERE id = ?";
    $stmtAceptar = $conn->prepare($sqlAceptar);
    $stmtAceptar->bind_param("i", $idAceptar);
    $stmtAceptar->execute();
    header("Location: notificaciones.php");
    exit;
}

// Eliminar notificación
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    $sqlDelete = "DELETE FROM reentrenosdatos WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $idEliminar);
    $stmtDelete->execute();
    header("Location: notificaciones.php");
    exit;
}

// Activar/desactivar notificaciones
if (isset($_POST['toggle_notificaciones'])) {
    $nuevoEstado = $_POST['estado'] === '1' ? 1 : 0;
    $sqlToggle = "UPDATE usuario SET notificaciones_activas = ? WHERE id_usuario = ?";
    $stmtToggle = $conn->prepare($sqlToggle);
    $stmtToggle->bind_param("ii", $nuevoEstado, $id_usuario);
    $stmtToggle->execute();
    header("Location: notificaciones.php");
    exit;
}

$hoy = date("Y-m-d H:i:s");
$mediaHoraDespues = date("Y-m-d H:i:s", strtotime("+30 minutes"));

$no_leidas = [];
$leidas = [];
$finalizadas = [];

if ($usuario['notificaciones_activas']) {
    // No leídas y próximas a vencer
    $sqlNoLeidas = "SELECT * FROM reentrenosdatos WHERE estatus = 'pendiente' AND fecha > ? AND fecha <= ? ORDER BY fecha ASC";
    $stmtNoLeidas = $conn->prepare($sqlNoLeidas);
    $stmtNoLeidas->bind_param("ss", $hoy, $mediaHoraDespues);
    $stmtNoLeidas->execute();
    $resultadoNoLeidas = $stmtNoLeidas->get_result();
    while ($row = $resultadoNoLeidas->fetch_assoc()) {
        $no_leidas[] = $row;
    }

    // Leídas
    $sqlLeidas = "SELECT * FROM reentrenosdatos WHERE estatus = 'leido' ORDER BY fecha DESC";
    $stmtLeidas = $conn->prepare($sqlLeidas);
    $stmtLeidas->execute();
    $resultadoLeidas = $stmtLeidas->get_result();
    while ($row = $resultadoLeidas->fetch_assoc()) {
        $leidas[] = $row;
    }

    // Finalizadas o vencidas
    $sqlFinalizadas = "SELECT * FROM reentrenosdatos WHERE estatus = 'finalizada' OR (estatus = 'pendiente' AND fecha <= ?) ORDER BY fecha DESC";
    $stmtFinalizadas = $conn->prepare($sqlFinalizadas);
    $stmtFinalizadas->bind_param("s", $hoy);
    $stmtFinalizadas->execute();
    $resultadoFinalizadas = $stmtFinalizadas->get_result();
    while ($row = $resultadoFinalizadas->fetch_assoc()) {
        $finalizadas[] = $row;
    }
}
?>

<?php include('../includes/header.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="../../assets/css/StyleNot.css" />
    <link rel="stylesheet" href="../../assets/css/switch.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="notificaciones-container">
    <h1>Notificaciones</h1>

    <form method="POST" class="switch-container" id="formToggle">
        <span class="switch-label">Mostrar notificaciones:</span>
        <label class="switch">
            <input type="checkbox" id="toggleSwitch" <?= $usuario['notificaciones_activas'] ? 'checked' : '' ?>>
            <span class="slider"></span>
        </label>
        <input type="hidden" name="estado" id="estadoInput" value="<?= $usuario['notificaciones_activas'] ?>">
        <input type="hidden" name="toggle_notificaciones" value="1">
    </form>

    <script>
        document.getElementById('toggleSwitch').addEventListener('change', function () {
            const estadoInput = document.getElementById('estadoInput');
            estadoInput.value = this.checked ? 1 : 0;
            document.getElementById('formToggle').submit();
        });

        // Mostrar notificaciones con SweetAlert2
        function showAlerts() {
            const notificacionesProximas = <?= json_encode($no_leidas); ?>;

            if (notificacionesProximas.length > 0) {
                notificacionesProximas.forEach(notif => {
                    Swal.fire({
                        title: "🔔 " + notif.titulo,
                        text: notif.descripcion,
                        icon: "info",
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Marcar como Leída",
                        denyButtonText: "Eliminar",
                        cancelButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `notificaciones.php?aceptar=${notif.id}`;
                        } else if (result.isDenied) {
                            window.location.href = `notificaciones.php?eliminar=${notif.id}`;
                        }
                    });
                });
            }
        }
        window.onload = showAlerts;
    </script>

    <?php if (!$usuario['notificaciones_activas']): ?>
        <p class="sin-notificaciones">🔕 Las notificaciones están desactivadas.</p>
    <?php else: ?>
        <section class="notificaciones-seccion">
            <h2>No Leídas y Próximas a Vencer</h2>
            <?php if (empty($no_leidas)): ?>
                <p class="sin-notificaciones">No tienes notificaciones pendientes.</p>
            <?php else: ?>
                <?php foreach ($no_leidas as $notif): ?>
                    <div class="notificacion no-leida">
                        <h3><?= htmlspecialchars($notif['titulo']) ?></h3>
                        <p><?= htmlspecialchars($notif['descripcion']) ?></p>
                        <small> Fecha: <?= $notif['fecha'] ?> | Estado: <?= $notif['estatus'] ?></small>
                        <div class="acciones">
                            <a href="notificaciones.php?aceptar=<?= $notif['id'] ?>" class="btn aceptar">Marcar como Leída</a>
                            <a href="notificaciones.php?eliminar=<?= $notif['id'] ?>" class="btn eliminar">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="notificaciones-seccion">
            <h2>Notificaciones Leídas</h2>
            <?php if (empty($leidas)): ?>
                <p class="sin-notificaciones">No tienes notificaciones leídas.</p>
            <?php else: ?>
                <?php foreach ($leidas as $notif): ?>
                    <div class="notificacion leida">
                        <h3><?= htmlspecialchars($notif['titulo']) ?></h3>
                        <p><?= htmlspecialchars($notif['descripcion']) ?></p>
                        <small> Fecha: <?= $notif['fecha'] ?> | Estado: <?= $notif['estatus'] ?></small>
                        <div class="acciones">
                            <a href="notificaciones.php?eliminar=<?= $notif['id'] ?>" class="btn eliminar">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="notificaciones-seccion">
            <h2>Finalizadas y Vencidas</h2>
            <?php if (empty($finalizadas)): ?>
                <p class="sin-notificaciones">No tienes notificaciones finalizadas o vencidas.</p>
            <?php else: ?>
                <?php foreach ($finalizadas as $notif): ?>
                    <div class="notificacion finalizada">
                        <h3><?= htmlspecialchars($notif['titulo']) ?></h3>
                        <p><?= htmlspecialchars($notif['descripcion']) ?></p>
                        <small> Fecha: <?= $notif['fecha'] ?> | Estado: <?= $notif['estatus'] ?></small>
                        <div class="acciones">
                            <a href="notificaciones.php?eliminar=<?= $notif['id'] ?>" class="btn eliminar">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?> 
</div>
</body>
</html>
