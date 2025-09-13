<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/admin/includes/header.php');
$extra_css = '<link rel="stylesheet" href="/ProyectoWeb1/assets/css/StyleI.css">';


if (isset($_GET['registro']) && $_GET['registro'] === 'ok') {
    echo '<div class="alert alert-success text-center" style="margin-top: 20px;">
             ¡Registro exitoso! Te damos la bienvenida a Homework UVG, ' . htmlspecialchars($_SESSION['nombreUsuario']) . ' 
          </div>';
}
?>

<div class="container-fluid bg-primary text-white py-5">
    <div class="row align-items-center"> 
        <div class="col-md-7 text-center text-md-start px-5">
            <h1 class="display-3 fw-bold">BIENVENIDOS</h1>
            <h4 class="fw-light">FACULTAD DE INGENIERÍA</h4>
            <p class="mt-4">Plataforma Homework UVG para apoyar tu aprendizaje.</p> 
        </div>

        <div class="col-md-5 text-center">
            <img src="assets/img/logoU.png" alt="Estudiantes UVG" class="img-fluid rounded-3 shadow">
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row text-center">
        <div class="col-md-4">
            <h3>Cursos</h3>
            <p>Accede a material, guías y tareas de tus cursos.</p>
        </div>
        <div class="col-md-4">
            <h3>Tareas</h3>
            <p>Organiza y gestiona tus tareas de forma más sencilla.</p>
        </div>
        <div class="col-md-4">
            <h3>Recursos</h3>
            <p>Encuentra herramientas y recursos para tus estudios.</p>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/admin/includes/footer.php'); ?>


<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) alert.remove();
    }, 4000);
</script>