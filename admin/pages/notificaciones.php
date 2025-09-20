<?php include('../includes/header.php'); ?>
<?php
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/conexion.php');

// Notificaciones próximas a vencer (dentro de 2 horas)
$sqlProximas = "SELECT * 
                FROM retroendatos 
                WHERE fecha_vencimiento > NOW()
                AND fecha_vencimiento <= DATE_ADD(NOW(), INTERVAL 2 HOUR)";
$resProximas = $conn->query($sqlProximas);

// Notificaciones vencidas (ya pasó la hora de vencimiento)
$sqlVencidas = "SELECT * 
                FROM retroendatos 
                WHERE fecha_vencimiento < NOW()";
$resVencidas = $conn->query($sqlVencidas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="../../assets/css/StyleNot.css">
</head>
<body>
    <h2>Notificaciones</h2>

    <h3>No Leídas y Próximas a Vencer</h3>
    <?php if ($resProximas && $resProximas->num_rows > 0): ?>
        <?php while($row = $resProximas->fetch_assoc()): ?>
            <div>
                <strong><?php echo htmlspecialchars($row['titulo']); ?></strong><br>
                <?php echo htmlspecialchars($row['descripcion']); ?><br>
                Fecha: <?php echo $row['fecha_vencimiento']; ?>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No tienes notificaciones próximas a vencer.</p>
    <?php endif; ?>

    <h3>Finalizadas y Vencidas</h3>
    <?php if ($resVencidas && $resVencidas->num_rows > 0): ?>
        <?php while($row = $resVencidas->fetch_assoc()): ?>
            <div>
                <strong><?php echo htmlspecialchars($row['titulo']); ?></strong><br>
                <?php echo htmlspecialchars($row['descripcion']); ?><br>
                Fecha: <?php echo $row['fecha_vencimiento']; ?>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No tienes notificaciones vencidas.</p>
    <?php endif; ?>
</body>
</html>
