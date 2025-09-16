<?php
include('../includes/header.php');
?>

<?php
include_once(__DIR__ . '/../config/config.php');

$stmt = $pdo->query("SELECT * FROM tareas ORDER BY orden ASC");
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="/assets/css/StyleDB.css">

<div class="contenedor-tareas">
    <h2 class="titulo-seccion">📋 Mis Tareas</h2>

    <div class="acciones-superiores">
        <input type="text" id="busqueda" placeholder="Buscar por nombre, descripción o etiquetas..." onkeyup="filtrarBusqueda()">
        <button onclick="exportarCSV()">Exportar CSV</button>
        <button onclick="exportarPDF()">Exportar PDF</button>
    </div>

    <div id="listaTareas" class="sortable">
        <?php foreach ($tareas as $tarea): ?>
            <div class="tarjeta-tarea" data-id="<?= $tarea['id'] ?>"
                 data-titulo="<?= strtolower($tarea['titulo']) ?>"
                 data-descripcion="<?= strtolower($tarea['descripcion']) ?>"
                 data-etiquetas="<?= strtolower($tarea['etiquetas']) ?>">
                <h3 class="titulo-tarea"><?= htmlspecialchars($tarea['titulo']) ?></h3>
                <p><strong>Descripción:</strong> <?= $tarea['descripcion'] ?></p>
                <p><strong>Etiquetas:</strong> <?= $tarea['etiquetas'] ?></p>
                <p><strong>Fecha de entrega:</strong> <?= date('d/m/Y', strtotime($tarea['fecha'])) ?></p>
                <p><strong>Estado:</strong> <?= $tarea['estado'] ?></p>
                <p><strong>Calificación:</strong> <?= $tarea['calificacion'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function filtrarBusqueda() {
    const query = document.getElementById('busqueda').value.toLowerCase();
    const tareas = document.querySelectorAll('.tarjeta-tarea');
    tareas.forEach(t => {
        const titulo = t.dataset.titulo;
        const descripcion = t.dataset.descripcion;
        const etiquetas = t.dataset.etiquetas;
        const coincide = titulo.includes(query) || descripcion.includes(query) || etiquetas.includes(query);
        t.style.display = coincide ? 'block' : 'none';
    });
}

new Sortable(document.getElementById('listaTareas'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: function () {
        const orden = Array.from(document.querySelectorAll('.tarjeta-tarea')).map(t => t.dataset.id);
        fetch('guardar_orden.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ orden })
        });
    }
});

async function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    let y = 10;
    document.querySelectorAll('.tarjeta-tarea').forEach(t => {
        if (t.style.display !== 'none') {
            const titulo = t.querySelector('h3').textContent;
            const datos = Array.from(t.querySelectorAll('p')).map(p => p.textContent);
            doc.text(titulo, 10, y);
            y += 8;
            datos.forEach(d => {
                doc.text(d, 12, y);
                y += 6;
            });
            y += 10;
        }
    });
    doc.save('tareas.pdf');
}

function exportarCSV() {
    let csv = "Título,Descripción,Etiquetas,Fecha,Estado,Calificación\n";
    document.querySelectorAll('.tarjeta-tarea').forEach(t => {
        if (t.style.display !== 'none') {
            const datos = Array.from(t.querySelectorAll('p')).map(p => p.textContent.split(': ')[1]);
            csv += `${t.querySelector('h3').textContent},${datos.join(',')}\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'tareas.csv';
    link.click();
}
</script>

<?php include(__DIR__ . '/../includes/footer.php'); ?>


<?php
include('../includes/footer.php');
?>
