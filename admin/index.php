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
<body class="d-flex justify-content-center align-items-center vh-100">
  <form method="post" class="p-4 shadow rounded bg-light">
    <h2 class="mb-3">Panel Admin</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <div class="mb-3">
      <input type="text" name="username" class="form-control" placeholder="Usuario" required>
    </div>
    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Entrar</button>
  </form>
</body>
</html>