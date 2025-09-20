<?php include('../includes/header.php'); ?>
<?php
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/conexion.php');

$sqlProximas = "SELECT id, titulo, descripcion, fecha
                FROM reentrenosdatos
                WHERE fecha > NOW()
                AND fecha <= DATE_ADD(NOW(), INTERVAL 5 MINUTE)";
$resProximas = $conn->query($sqlProximas);
$tareasProximas = [];
if ($resProximas && $resProximas->num_rows > 0) {
    while ($row = $resProximas->fetch_assoc()) {
        $row['fecha'] = date('c', strtotime($row['fecha']));
        $tareasProximas[] = $row;
    }
}

$sqlHoy = "SELECT id, titulo, descripcion, fecha
           FROM reentrenosdatos
           WHERE DATE(fecha) = CURDATE()";
$resHoy = $conn->query($sqlHoy);
$tareasHoy = [];
if ($resHoy && $resHoy->num_rows > 0) {
    while ($row = $resHoy->fetch_assoc()) {
        $tareasHoy[] = $row;
    }
}

$sqlVencidas = "SELECT id, titulo, descripcion, fecha
                FROM reentrenosdatos
                WHERE fecha < NOW()";
$resVencidas = $conn->query($sqlVencidas);
$vencidas = [];
if ($resVencidas && $resVencidas->num_rows > 0) {
    while ($row = $resVencidas->fetch_assoc()) {
        $vencidas[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="../../assets/css/StyleNot.css" />
</head>
<body>
    <h2>Notificaciones</h2>

    <label>
        <input type="checkbox" id="toggleNotifs"> Activar notificaciones
    </label>

    <div id="seccionNotificaciones">
        <div id="notificaciones">
            <h3>Historial de Notificaciones</h3>
            <div id="listaNotificaciones"></div>
        </div>

        <h3>Próximas a vencer hoy</h3>
        <div id="hoy">
            <?php if (!empty($tareasHoy)): ?>
                <?php foreach ($tareasHoy as $t): ?>
                    <div class="hoy">
                        <strong><?php echo htmlspecialchars($t['titulo']); ?></strong><br>
                        <?php echo htmlspecialchars($t['descripcion']); ?><br>
                        Vence: <?php echo date('Y-m-d H:i', strtotime($t['fecha'])); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tienes tareas próximas a vencer hoy.</p>
            <?php endif; ?>
        </div>

        <h3>Finalizadas y Vencidas</h3>
        <div id="vencidas">
            <?php if (!empty($vencidas)): ?>
                <?php foreach ($vencidas as $t): ?>
                    <div class="vencida">
                        <strong><?php echo htmlspecialchars($t['titulo']); ?></strong><br>
                        <?php echo htmlspecialchars($t['descripcion']); ?><br>
                        Fecha: <?php echo date('Y-m-d H:i', strtotime($t['fecha'])); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tienes notificaciones vencidas.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        localStorage.setItem('notificacionesHoy', 0);

        const tareas = <?php echo json_encode($tareasProximas, JSON_UNESCAPED_UNICODE); ?>;
        let notificaciones = JSON.parse(localStorage.getItem("notificaciones")) || [];
        let notificacionesActivas = localStorage.getItem("notificacionesActivas") !== "false";

        const listaNotificaciones = document.getElementById("listaNotificaciones");
        const seccionNotificaciones = document.getElementById("seccionNotificaciones");
        const toggleNotifs = document.getElementById("toggleNotifs");

        toggleNotifs.checked = notificacionesActivas;
        seccionNotificaciones.style.display = notificacionesActivas ? "block" : "none";

        toggleNotifs.addEventListener("change", () => {
            notificacionesActivas = toggleNotifs.checked;
            localStorage.setItem("notificacionesActivas", notificacionesActivas);

            if (!notificacionesActivas) {
                notificaciones = [];
                guardarHistorial();
                renderNotificaciones();
                seccionNotificaciones.style.display = "none";
            } else {
                seccionNotificaciones.style.display = "block";
                renderNotificaciones();
            }
        });

        function mostrarNotificacion(tarea) {
            if (!notificacionesActivas) return;

            const notif = {
                id: Date.now(),
                tareaId: tarea.id,
                titulo: tarea.titulo,
                descripcion: tarea.descripcion,
                estado: "no-leida",
                fecha: tarea.fecha
            };
            notificaciones.push(notif);
            guardarHistorial();
            renderNotificaciones();

            const ahora = new Date().getTime();
            const diferencia = new Date(tarea.fecha).getTime() - ahora;
            const margen = 30 * 1000;
            const unMinuto = 60 * 1000;

            if (Math.abs(diferencia - unMinuto) <= margen) {
                if (Notification.permission === "granted") {
                    new Notification("Tarea próxima a vencer", {
                        body: tarea.titulo,
                        icon: "https://cdn-icons-png.flaticon.com/512/1827/1827343.png"
                    });
                }
                alert(`Tarea próxima a vencer:\n${tarea.titulo}`);
            }
        }

        function renderNotificaciones() {
            listaNotificaciones.innerHTML = "";
            notificaciones.forEach(n => {
                const div = document.createElement("div");
                div.className = `notif ${n.estado}`;
                div.innerHTML = `
                    <strong>${n.titulo}</strong><br>
                    ${n.descripcion}<br>
                    Vence a las ${new Date(n.fecha).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}<br>
                    Estado: ${n.estado}<br>
                    <button onclick="marcarLeida(${n.id})">Aceptar</button>
                    <button onclick="marcarNoLeida(${n.id})">Cancelar</button>
                    <button onclick="eliminarNotificacion(${n.id})">Eliminar</button>
                `;
                listaNotificaciones.appendChild(div);
            });
        }

        function marcarLeida(id) {
            const notif = notificaciones.find(n => n.id === id);
            if (notif) notif.estado = "leida";
            guardarHistorial();
            renderNotificaciones();
        }

        function marcarNoLeida(id) {
            const notif = notificaciones.find(n => n.id === id);
            if (notif) notif.estado = "no-leida";
            guardarHistorial();
            renderNotificaciones();
        }

        function eliminarNotificacion(id) {
            notificaciones = notificaciones.filter(n => n.id !== id);
            guardarHistorial();
            renderNotificaciones();
        }

        function guardarHistorial() {
            localStorage.setItem("notificaciones", JSON.stringify(notificaciones));
        }

        function revisarTareas() {
            const ahora = new Date().getTime();
            tareas.forEach(t => {
                const vencimiento = new Date(t.fecha).getTime();
                const diferencia = vencimiento - ahora;
                const margen = 30 * 1000;
                const unMinuto = 60 * 1000;

                if (Math.abs(diferencia - unMinuto) <= margen) {
                    if (!notificaciones.some(n => n.tareaId == t.id)) {
                        mostrarNotificacion(t);
                    }
                }
            });
        }

        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        revisarTareas();
        renderNotificaciones();
        setInterval(revisarTareas, 5000);
    </script>
</body>
</html>
 