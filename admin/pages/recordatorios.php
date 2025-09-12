<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "pruebas_web");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Si se envía un evento por POST (desde JavaScript)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['title']) && !empty($data['start']) && !empty($data['end']) && !empty($data['color'])) {
        $stmt = $conexion->prepare("INSERT INTO events (title, start, end, color) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data['title'], $data['start'], $data['end'], $data['color']);
        $success = $stmt->execute();
        $stmt->close();

        echo json_encode(["success" => $success]);
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        exit;
    }
}

// Si se quiere obtener los eventos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conexion->query("SELECT id, title, start, end, color FROM events");
    $eventos = [];

    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($eventos);
    exit;
}
?>
<!-- A partir de aquí comienza el HTML -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recordatorios</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js'></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        #calendar {
            max-width: 900px;
            margin: 40px auto;
        }
    </style>
</head>

<body>

    <h1 style="text-align: center;">📅 Mis Recordatorios</h1>
    <div id='calendar'></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'dayGridMonth',
                selectable: true,
                editable: false,
                events: 'recordatorios.php', // El mismo archivo PHP devuelve los eventos

                dateClick: function(info) {
                    let title = prompt('Título del evento:');
                    if (title) {
                        let color = prompt('Color en formato HEX (ej: #3788d8):', '#3788d8');
                        let start = info.dateStr + ' 00:00:00';
                        let end = info.dateStr + ' 23:59:59';

                        fetch('recordatorios.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    title,
                                    start,
                                    end,
                                    color
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Evento guardado correctamente.');
                                    calendar.refetchEvents();
                                } else {
                                    alert('Error al guardar: ' + (data.error || 'Desconocido'));
                                }
                            });
                    }
                }
            });

            calendar.render();
        });
    </script>

</body>

</html>