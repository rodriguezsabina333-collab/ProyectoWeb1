<?php
include('../includes/header.php'); 
include_once(__DIR__ . "/../config/conexion.php"); 

$estado = $_GET['estado'] ?? '';
$prioridad = $_GET['prioridad'] ?? '';
$etiqueta = $_GET['etiqueta'] ?? ''; 
$curso = $_GET['curso'] ?? ''; 
 
$eventos = [];  
 
if (!empty($_GET)) { 
    $sql = "SELECT * FROM reentrenosdatos WHERE 1";
    if ($estado) $sql .= " AND estatus = '$estado'";
    if ($prioridad) $sql .= " AND prioridad = '$prioridad'";
    if ($etiqueta) $sql .= " AND etiquetas LIKE '%$etiqueta%'";
    if ($curso) $sql .= " AND curso = '$curso'";
    $sql .= " ORDER BY fecha ASC";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventos[] = [
                'title' => $row['curso'] . " - " . $row['titulo'],
                'start' => $row['fecha'],
                'description' => $row['descripcion'],
                'color' => ($row['estatus'] === 'finalizado') ? '#28a745' : '#dc3545'
            ];
        }
    }
} 
?> 


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recordatorios</title>
    <link rel="stylesheet" href="../../assets/css/StyleRec.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
    <style> 
        #calendar {
            max-width: 900px;
            margin: 40px auto;
        }
    </style>
</head> 

<body>

    <div class="container mt-4">
        <h2 class="mb-3 text-center">Recordatorios con Filtros</h2>

        <form method="GET" class="row mb-4">
            <div class="col-md-3">
                <select name="estado" class="form-control">
                    <option value="">Estado</option>
                    <option value="pendiente" <?= $estado == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_proceso" <?= $estado == 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                    <option value="finalizado" <?= $estado == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="prioridad" class="form-control">
                    <option value="">Prioridad</option>
                    <option value="alta" <?= $prioridad == 'alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="media" <?= $prioridad == 'media' ? 'selected' : '' ?>>Media</option>
                  <option value="baja" <?= $prioridad == 'baja' ? 'selected' : '' ?>>Baja</option>
              </select> 
            </div> 
            <div class="col-md-3">
                <select name="curso" class="form-control">
                    <option value="">Curso</option>
                    <option value="Base de Datos" <?= $curso == 'Base de Datos' ? 'selected' : '' ?>>Base de Datos</option>
                    <option value="Filosofía" <?= $curso == 'Filosofía' ? 'selected' : '' ?>>Filosofía</option>
                    <option value="Matemáticas" <?= $curso == 'Matemáticas' ? 'selected' : '' ?>>Matemáticas</option>
                    <option value="Programación" <?= $curso == 'Programación' ? 'selected' : '' ?>>Programación</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="etiqueta" class="form-control" placeholder="Etiqueta..." value="<?= htmlspecialchars($etiqueta) ?>">
            </div>
            <div class="col-md-12 mt-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        <div id="calendar"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: <?= json_encode($eventos) ?>,
                eventClick: function(info) {
                    alert(info.event.title + "\n" + (info.event.extendedProps.description || "Sin descripción"));
                }
            });
            calendar.render();
        });
    </script>

    <?php include('../includes/footer.php'); ?>
</body> 
</html> 
