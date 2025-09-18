<?php
include('../includes/header.php');
include_once(__DIR__ . '/../config/config.php');   

$curso_filtro = '';
if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $curso_filtro = $_GET['curso'];
}

$cursos = [];
$consultaCursos = $conn->query("SELECT DISTINCT curso FROM reentrenosdatos WHERE curso IS NOT NULL AND curso != '' ORDER BY curso ASC");
if ($consultaCursos && $consultaCursos->num_rows > 0) {
    while ($fila = $consultaCursos->fetch_assoc()) {
        $cursos[] = $fila['curso'];
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM reentrenosdatos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: " . basename(__FILE__) . (!empty($curso_filtro) ? "?curso=" . urlencode($curso_filtro) : ""));
        exit;
    }
}

$tareas = [];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM reentrenosdatos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $tareas[] = $fila;
            }
        }
    }
} elseif (!empty($curso_filtro)) {
    $stmt = $conn->prepare("SELECT * FROM reentrenosdatos WHERE curso = ? ORDER BY fecha ASC");
    if ($stmt) {
        $stmt->bind_param("s", $curso_filtro);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $tareas[] = $fila;
            }
        }
    }
}
?> 

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mis Tareas</title>
    <link rel="stylesheet" href="../../assets/css/Styledash.css" />
</head>

<body>
    <div class="contenedor-tareas">
        <h2 class="titulo-seccion">Mis Tareas</h2>

        <div class="acciones-superiores">
            <form action="" method="get">
                <select name="curso">
                    <option value="">Buscar por Curso</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?= htmlspecialchars($curso) ?>" <?= ($curso === $curso_filtro) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($curso) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">🔍 </button>
            </form>
        </div>

        <div id="listaTareas" class="sortable">
            <?php foreach ($tareas as $tarea): ?>
                <div class="tarjeta-tarea" data-id="<?= $tarea['id'] ?>">
                    <h3 class="titulo-tarea"><?= htmlspecialchars($tarea['titulo']) ?></h3>
                    <p><strong>Descripción:</strong> <?= $tarea['descripcion'] ?></p>
                    <p><strong>Fecha de entrega:</strong> <?= date('d/m/Y H:i', strtotime($tarea['fecha'])) ?></p>
                    <p><strong>Estado:</strong> <?= $tarea['estatus'] ?></p>
                    <p><strong>Curso:</strong> <?= $tarea['curso'] ?></p>

                    <div class="acciones-tarea">

                        <button onclick="exportarCSV()">Exportar CSV</button>
                        <button onclick="exportarPDF()">Exportar PDF</button>
                        <a href="nuevaTarea.php?id=<?= $tarea['id'] ?>" class="btn-editar">Editar</a>
                        <a href="?action=delete&id=<?= $tarea['id'] . (!empty($curso_filtro) ? '&curso=' . urlencode($curso_filtro) : "") ?>" class="btn-eliminar" onclick="return confirm('¿Deseas eliminar esta tarea?')">Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        new Sortable(document.getElementById('listaTareas'), {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const orden = Array.from(document.querySelectorAll('.tarjeta-tarea')).map(t => t.dataset.id);
                fetch('guardar_orden.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        orden
                    })
                });
            }
        });

        function getExportFilename(extension) {
            const cursoFiltro = new URLSearchParams(window.location.search).get('curso');
            const baseName = 'tareas';
            if (cursoFiltro) {
                return `${baseName}_${cursoFiltro}.${extension}`;
            }
            return `${baseName}.${extension}`;
        }

        async function exportarPDF() {
            const {
                jsPDF
            } = window.jspdf;
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
            doc.save(getExportFilename('pdf'));
        }

        function exportarCSV() {
            let csv = "Título,Descripción,Fecha,Estado,Curso\n";
            document.querySelectorAll('.tarjeta-tarea').forEach(t => {
                if (t.style.display !== 'none') {
                    const titulo = t.querySelector('h3').textContent.replace(/,/g, '');
                    const datos = Array.from(t.querySelectorAll('p')).map(p => {
                        const text = p.textContent.split(': ')[1];
                        return text ? `"${text.replace(/"/g, '""')}"` : '';
                    });
                    csv += `${titulo},${datos.join(',')}\n`;
                }
            });
            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = getExportFilename('csv');
            link.click();
        }
    </script>
</body>

</html>

<?php include(__DIR__ . '/../includes/footer.php'); ?> 