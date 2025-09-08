<?php
session_start();
$USER = "admin";
$PASS = "1234";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["username"] === $USER && $_POST["password"] === $PASS) {
        $_SESSION["logged_in"] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<div class="container">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <h2 class="text-center text-dark mt-5">Login</h2>
      <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
        <div class="text-center mb-5 text-dark">Helados Cano</div>
        <div class="card my-5">
          <form method="post" class="card-body cardbody-color p-lg-5">
            <div class="text-center">
              <img src="..\assets\img\fontpage\logo.png" class="img-fluid profile-image-pic img-thumbnail rounded-circle my-3"
                width="200px" alt="profile">
            </div>
            <div class="mb-3">
              <input type="text" name="username" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="mb-3">
              <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="text-center"><button type="submit" class="btn btn-color px-5 mb-5 w-100">Entrar</button>
            </div>
          </form>
        </div>
    </div>
  </div>
</div>
</body>
</html>