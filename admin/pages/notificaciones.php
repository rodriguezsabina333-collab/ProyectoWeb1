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

if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    $sqlDelete = "DELETE FROM reentrenosdatos WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $idEliminar);
    $stmtDelete->execute();
    header("Location: notificaciones.php");
    exit;
}

$hoy = date("Y-m-d H:i:s");
$sqlNotif = "SELECT * FROM reentrenosdatos ORDER BY fecha DESC";
$resultadoNotif = $conn->query($sqlNotif);

$pendientes = [];
$finalizadas = [];

while ($row = $resultadoNotif->fetch_assoc()) {
    if ($row['estatus'] === 'pendiente' && $row['fecha'] >= $hoy) {
        $pendientes[] = $row;
    } else {
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
</head>
<body>
    <div class="notificaciones-container">
        <h1>Notificaciones</h1>

        <section class="notificaciones-seccion">
            <h2>Pendientes por vencer</h2>
            <?php if (empty($pendientes)): ?>
                <p class="sin-notificaciones">No tienes notificaciones pendientes.</p>
            <?php else: ?>
                <?php foreach ($pendientes as $notif): ?>
                    <div class="notificacion">
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
            <h2>Finalizadas</h2>
            <?php if (empty($finalizadas)): ?>
                <p class="sin-notificaciones">No tienes notificaciones finalizadas.</p>
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
    </div>
</body>
</html>