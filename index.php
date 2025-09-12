<?php
// Incluye el header desde admin/includes
include(__DIR__ . '/admin/includes/header.php');
?>

<!-- Botón para cambiar de tema -->
<div class="text-end p-3">
    <button id="toggleTheme" class="btn btn-outline-success">🌙 Modo Oscuro</button>
</div>

<!-- Banner principal con degradado verde -->
<div class="container-fluid banner text-white py-5">
    <div class="row align-items-center">
        <!-- Texto de bienvenida -->
        <div class="col-md-7 text-center text-md-start px-5">
            <h1 class="display-3 fw-bold">BIENVENIDOS</h1>
            <h4 class="fw-light">FACULTAD DE INGENIERÍA</h4>
            <p class="mt-4">Plataforma <strong>Homework UVG</strong> para apoyar tu aprendizaje.</p>
            <a href="login.php" class="btn btn-light btn-lg mt-3 text-success fw-bold shadow-sm">Ingresar</a>
        </div>

        <!-- Imagen de estudiantes -->
        <div class="col-md-5 text-center">
            <img src="assets/images/estudiantes.png" alt="Estudiantes UVG" class="img-fluid rounded-3 shadow">
        </div>
    </div>
</div>

<!-- Sección de información -->
<div class="container my-5">
    <div class="row text-center">
        <div class="col-md-4">
            <h3 class="text-success">Cursos</h3>
            <p>Accede a material, guías y tareas de tus cursos.</p>
        </div>
        <div class="col-md-4">
            <h3 class="text-success">Tareas</h3>
            <p>Organiza y gestiona tus tareas de forma más sencilla.</p>
        </div>
        <div class="col-md-4">
            <h3 class="text-success">Recursos</h3>
            <p>Encuentra herramientas y recursos para tus estudios.</p>
        </div>
    </div>
</div>

<style>
    /* Estilo general */
    body {
        transition: background-color 0.3s, color 0.3s;
    }
    .banner {
        background: linear-gradient(90deg, #2e7d32, #66bb6a);
        transition: background 0.3s;
    }
    .btn-light {
        background-color: #c8e6c9;
        border: none;
    }
    .btn-light:hover {
        background-color: #a5d6a7;
    }

    /* ===== MODO OSCURO ===== */
    body.dark-mode {
        background-color: #121212;
        color: #e0e0e0;
    }
    body.dark-mode .banner {
        background: linear-gradient(90deg, #1b5e20, #388e3c);
    }
    body.dark-mode .btn-light {
        background-color: #424242;
        color: #fff;
    }
    body.dark-mode .btn-light:hover {
        background-color: #616161;
    }
</style>

<script>
    const toggleBtn = document.getElementById('toggleTheme');
    const body = document.body;

    // Cargar preferencia guardada
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        toggleBtn.textContent = '☀️ Modo Claro';
    }

    // Cambiar tema al hacer clic
    toggleBtn.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            toggleBtn.textContent = ' Modo Claro';
        } else {
            localStorage.setItem('theme', 'light');
            toggleBtn.textContent = ' Modo Oscuro';
        }
    });
</script>

<?php
// Incluye el footer desde admin/includes
include(__DIR__ . '/admin/includes/footer.php');
?>



