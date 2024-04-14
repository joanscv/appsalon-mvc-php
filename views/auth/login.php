<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form class="formulario" action="/" method="post">
  <div class="campo">
    <label for="email">Email</label>
    <input 
        id="email" 
        type="email"
        placeholder="Tu Email"
        name="email">
  </div>
  <div class="campo">
    <label for="password">Password</label>
    <input 
        id="password" 
        type="password"
        placeholder="Tu Password"
        name="password">
  </div>
  <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
  <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
  <a href="/olvide">¿Olvidaste tu password?</a>
</div>