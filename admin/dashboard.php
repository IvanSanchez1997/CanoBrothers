<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}

$menu_file = "../data/menu.json";
$menu = json_decode(file_get_contents($menu_file), true);

// Definir alérgenos disponibles con sus rutas
$allergens = [
    'lactose' => 'assets/img/allergens/lactose.svg',
    'nuts' => 'assets/img/allergens/nuts.svg',
    'gluten' => 'assets/img/allergens/gluten.svg',
    'eggs' => 'assets/img/allergens/eggs.svg',
    'fish' => 'assets/img/allergens/fish.svg',
    'soy' => 'assets/img/allergens/soy.svg',
    'celery' => 'assets/img/allergens/celery.svg'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h1 class="mb-4">Administrar Menú</h1>

  <!-- FORMULARIO PARA AÑADIR -->
  <form action="save_item.php" method="post" enctype="multipart/form-data" class="mb-5">
    <div class="row g-3">
      <div class="col-md-3"><input type="text" name="categoria" class="form-control" placeholder="Categoría" required></div>
      <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Nombre" required></div>
      <div class="col-md-3"><input type="text" name="singlePrice" class="form-control" placeholder="Precio Único"></div>
      <div class="col-md-3"><input type="text" name="price1" class="form-control" placeholder="Pequeño. Indicar divisa (€, $)"></div>
      <div class="col-md-3"><input type="text" name="price2" class="form-control" placeholder="Mediano. Indicar divisa (€, $)"></div>
      <div class="col-md-3"><input type="text" name="price3" class="form-control" placeholder="Grande. Indicar divisa (€, $)"></div>
      <div class="col-md-3"><p style="color: red;">Si rellena precio único no rellene los demás</p></div>
      <div class="col-md-12"><input type="text" name="description" class="form-control" placeholder="Descripción"></div>
      <div class="col-md-6"><input type="text" name="alert" class="form-control" placeholder="Alerta (ej: 'Casero', 'Nuevo', 'Recomendado', 'Casero y Nuevo')"></div>
      <div class="col-md-6"><input type="text" name="allergensDes" class="form-control" placeholder="Descripción alérgenos">
        <div class="form-text">Recomendación: dejar vacío siempre. Si lo dejas vacío se generará automáticamente desde los iconos seleccionados.</div>
      </div>

<div class="mb-3">
  <label class="form-label">Alérgenos</label>
  <div class="d-flex flex-wrap gap-3">
    <?php foreach ($allergens as $nombre => $ruta): ?>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="allergens[]" value="<?= $ruta ?>" id="alergeno_<?= $nombre ?>">
        <label class="form-check-label" for="alergeno_<?= $nombre ?>">
          <img src="../<?= $ruta ?>" alt="<?= $nombre ?>" style="width:50px; height:50px;"> <?= ucfirst($nombre) ?>
        </label>
      </div>
    <?php endforeach; ?>
  </div>
</div>
      <div class="col-md-6"><input type="file" name="img" class="form-control" accept="image/*"></div>
      <div class="col-md-6"><button type="submit" class="btn btn-success w-100">➕ Añadir</button></div>
    </div>
  </form>

  <a href="logout.php" class="btn btn-danger mb-4">Cerrar sesión</a>

  <!-- LISTADO DE PRODUCTOS -->
  <h2>Productos existentes</h2>
  <?php foreach ($menu as $categoria => $items): ?>
    <h3 class="mt-4"><?= htmlspecialchars($categoria) ?></h3>
    <ul class="list-group">
      <?php foreach ($items as $index => $item): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span>
            <img src="../<?= $item['img'] ?>" alt="" width="50" class="me-2">
            <strong><?= htmlspecialchars($item['name']) ?></strong> - 
            <?= $item['singlePrice'] ?> <?= $item['price1'] ?> <?= $item['price2'] ?> <?= $item['price3'] ?>
            <!-- Mostrar imágenes de alérgenos -->
            <?php 
            if (!empty($item['allergensImg'])) {
                if (is_array($item['allergensImg'])) {
                    foreach ($item['allergensImg'] as $allergen) {
                        if (!empty($allergen)) {
                            echo '<img src="../' . $allergen . '" width="20" class="ms-1">';
                        }
                    }
                } else if (!empty($item['allergensImg'])) {
                    echo '<img src="../' . $item['allergensImg'] . '" width="20" class="ms-1">';
                }
            }
            ?>
          </span>
          <div>
            <a href="edit_item.php?categoria=<?= urlencode($categoria) ?>&index=<?= $index ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
            <form action="delete_item.php" method="POST" style="display:inline;">
              <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
              <input type="hidden" name="index" value="<?= $index ?>">
              <button type="submit" class="btn btn-sm btn-danger">❌ Eliminar</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endforeach; ?>
</body>
</html>
