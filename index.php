<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$extra_css = '<link rel="stylesheet" href="/ProyectoWeb1/assets/css/StyleInd.css">';

include(__DIR__ . '/admin/includes/header.php');

if (isset($_GET['registro']) && $_GET['registro'] === 'ok') {
    echo '<div class="alert alert-success text-center" style="margin-top: 20px;">
             ¡Registro exitoso! Te damos la bienvenida a Homework UVG, ' . htmlspecialchars($_SESSION['nombreUsuario']) . '
          </div>';
}
?>

</div>
</div>
</div>

<div class="pantalla-verde">
  <div class="contenido-bienvenida">
    <div class="texto">
      <h1>FACULTAD DE INGENIERÍA</h1>
      <h2>Plataforma Homework UVG para apoyar tu aprendizaje.</h2>
    </div>
    <div class="logo">
      <img src="assets/img/logoU.png" alt="UVG" class="img-fluid">
    </div>
  </div>
</div>

<div class="seccion-blanca">
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
</div>

<footer class="footer">
  <div class="footer-content">
    <p>© 2025 Homework UVG. Todos los derechos reservados.</p>
    <p>Facultad de Ingeniería | Universidad del Valle de Guatemala</p>
  </div>
</footer>

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) alert.remove();
    }, 4000);
</script> 
</body>
</html>


