<?php
include('../includes/header.php');
include_once(__DIR__ . "/../config/conexion.php");

$estado = $_GET['estado'] ?? '';
$prioridad = $_GET['prioridad'] ?? '';
$etiqueta = $_GET['etiqueta'] ?? '';
$curso = $_GET['curso'] ?? '';

$eventos = [];

if (!empty($_GET)) {
    $sql = "SELECT * FROM reentrenosdatos WHERE 1=1";

    if (!empty($estado)) {
        $estado = $conn->real_escape_string($estado);
        $sql .= " AND estatus = '$estado'";
    }

    if (!empty($prioridad)) {
        $prioridad = $conn->real_escape_string($prioridad);
        $sql .= " AND prioridad = '$prioridad'";
    }

    if (!empty($etiqueta)) {
        $etiqueta = $conn->real_escape_string($etiqueta);
        $sql .= " AND etiquetas LIKE '%$etiqueta%'";
    }

    if (!empty($curso)) {
        $curso = $conn->real_escape_string($curso);
        $sql .= " AND curso = '$curso'";
    }

    $sql .= " ORDER BY fecha ASC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventos[] = [
                'id' => $row['id'],
                'title' => $row['curso'] . " - " . $row['titulo'],
                'start' => $row['fecha'],
                'description' => $row['descripcion'],
                'estatus' => $row['estatus'],
                'curso' => $row['curso']
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
    <link rel="stylesheet" href="../../assets/css/StyleRec.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Igia&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
</head>
<body>
<div class="container mt-4">
<h2 class="titulo-recordatorios mb-3 text-center">Filtrar Recordatorios</h2>

    <form method="GET" class="row mb-4" id="formFiltros">
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
            <input type="text" name="etiqueta" class="form-control" placeholder="Etiqueta..." value="<?= htmlspecialchars($etiqueta) ?>">
        </div>

        <div class="col-md-3">
            <select name="curso" class="form-control" id="cursoSelect">
                <option value="">Curso</option>
                <option value="Base de Datos" <?= $curso == 'Base de Datos' ? 'selected' : '' ?>>Base de Datos</option>
                <option value="Filosofía" <?= $curso == 'Filosofía' ? 'selected' : '' ?>>Filosofía</option>
                <option value="Matemáticas" <?= $curso == 'Matemáticas' ? 'selected' : '' ?>>Matemáticas</option>
                <option value="Programación" <?= $curso == 'Programación' ? 'selected' : '' ?>>Programación</option>
            </select>
        </div>

     
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div id="calendar"></div>
</div>

<script>
    document.getElementById('cursoSelect').addEventListener('change', function () {
        document.getElementById('formFiltros').submit();
    });

    document.addEventListener('DOMContentLoaded', function () {
        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            locale: 'es',
            editable: true, 
            eventDrop: function(info) {
                let eventId = info.event.id;
                let newDate = info.event.startStr;
 
                fetch('actualizar_fecha.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: eventId,
                        nueva_fecha: newDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Fecha actualizada correctamente.");
                    } else {
                        alert("Error al actualizar: " + (data.error || ''));
                        info.revert();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Error de red al actualizar la fecha.");
                    info.revert();
                });
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: <?= json_encode($eventos, JSON_UNESCAPED_UNICODE) ?>,
            eventClick: function (info) {
                var eventId = info.event.id;
                var userAction = confirm(info.event.title + "\n\n" + (info.event.extendedProps.description || "Sin descripción") + "\n\nPresione 'Aceptar' para ir a la tarea.");
                if (userAction) {
                    if (eventId) {
                        window.location.href = `dashboard.php?id=${eventId}`;
                    } else {
                        alert("No se encontró el ID de la tarea.");
                    }
                }
            }
        });

        calendar.render();
    });
</script>

<?php include('../includes/footer.php'); ?>
</body>
</html>
