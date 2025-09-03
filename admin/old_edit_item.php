<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}

$menu_file = "../data/menu.json";
$menu = json_decode(file_get_contents($menu_file), true);

$categoria = $_GET["categoria"] ?? null;
$index = $_GET["index"] ?? null;

if ($categoria === null || $index === null || !isset($menu[$categoria][$index])) {
    die("Producto no encontrado.");
}

$allergens = [
    'lactose' => 'assets/img/allergens/lactose.svg',
    'nuts' => 'assets/img/allergens/nuts.svg',
    'gluten' => 'assets/img/allergens/gluten.svg',
    'eggs' => 'assets/img/allergens/eggs.svg',
    'fish' => 'assets/img/allergens/fish.svg',
    'soy' => 'assets/img/allergens/soy.svg',
    'celery' => 'assets/img/allergens/celery.svg'
];

$item = $menu[$categoria][$index];

// Normalizar los alérgenos actuales del producto a un array de rutas
$existing_allergens = [];
if (!empty($item['allergensImg'])) {
        $existing_allergens = $item['allergensImg'];
}

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item['name'] = trim($_POST['name']);
    $item['description'] = trim($_POST['description']);
    $item['alert'] = trim($_POST['alert']);
    $item['allergensDes'] = trim($_POST['allergensDes']);
    $item['price1'] = trim($_POST['price1']);
    $item['price2'] = trim($_POST['price2']);
    $item['price3'] = trim($_POST['price3']);
    
        // Procesar alérgenos recibidos (checkboxes)
    $selected = isset($_POST['allergens']) ? $_POST['allergens'] : [];
    // Asegurarnos que solo se guarden rutas válidas (las definidas en $allergens)
    $allowed_values = array_values($allergens);
    $clean_selected = [];
    foreach ($selected as $val) {
        if (in_array($val, $allowed_values, true)) {
            $clean_selected[] = $val;
        }
    }
    $item['allergensImg'] = $clean_selected;

    // Si no han puesto descripción de alérgenos, autogenerarla desde filenames
    if (empty($item['allergensDes'])) {
        $names = [];
        foreach ($clean_selected as $ruta) {
            $base = pathinfo($ruta, PATHINFO_FILENAME);
            $names[] = ucfirst(str_replace(['_', '-'], ' ', $base));
        }
        $item['allergensDes'] = implode(', ', $names);
    }

    // Manejo de nueva imagen
    if (!empty($_FILES["img"]["name"])) {
        $targetDir = "../assets/img/products/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["img"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $targetFile)) {
            $menu[$categoria][$index]["img"] = "assets/img/products/" . $fileName;
        }
    }
    // Reemplazar el producto modificado en el menú
    $menu[$categoria][$index] = $item;

    // Guardar cambios
    file_put_contents($menu_file, json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h1>Editar producto en <?= htmlspecialchars($categoria) ?></h1>

  <form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6"><p>Nombre</p><input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" class="form-control" placeholder="Nombre" required></div>
    <div class="col-md-6"><p>Alerta</p><input type="text" name="alert" value="<?= htmlspecialchars($item['alert']) ?>" class="form-control" placeholder="Alerta">
      <div class="form-text">Por ejemplo: "Casero", "Nuevo", "Recomendado", "Casero y Nuevo" </div>
    </div>
    <div class="col-md-6"><p>Descripción</p><input type="text" name="description" value="<?= htmlspecialchars($item['description']) ?>" class="form-control" placeholder="Descripción"></div>

    <div class="col-md-6">
      <label class="form-label">Alérgenos (descripción)</label>
      <input type="text" name="allergensDes" value="<?= htmlspecialchars($item['allergensDes'] ?? '') ?>" class="form-control">
      <div class="form-text">Recomendación: dejar vacío siempre. Si lo dejas vacío se generará automáticamente desde los iconos seleccionados.</div>
    </div>
    <div class="col-md-12">
      <label class="form-label">Alérgenos (seleccionar)</label>
      <div class="d-flex flex-wrap gap-3">
        <?php foreach ($allergens as $key => $ruta): 
            $checked = in_array($ruta, $existing_allergens, true) ? 'checked' : '';
        ?>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="allergens[]" value="<?= htmlspecialchars($ruta) ?>" id="alergeno_<?= htmlspecialchars($key) ?>" <?= $checked ?>>
            <label class="form-check-label" for="alergeno_<?= htmlspecialchars($key) ?>">
              <img src="../<?= htmlspecialchars($ruta) ?>" alt="<?= htmlspecialchars($key) ?>" style="width:30px; height:30px; object-fit:contain;">
              <?= ucfirst(htmlspecialchars(str_replace(['_', '-'], ' ', $key))) ?>
            </label>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="col-md-12"><span>Precios:</span></div>
    <div class="col-md-2"><input type="text" name="price1" value="<?= htmlspecialchars($item['price1']) ?>" class="form-control" placeholder="Precio 1"></div>
    <div class="col-md-2"><input type="text" name="price2" value="<?= htmlspecialchars($item['price2']) ?>" class="form-control" placeholder="Precio 2"></div>
    <div class="col-md-2"><input type="text" name="price3" value="<?= htmlspecialchars($item['price3']) ?>" class="form-control" placeholder="Precio 3"></div>

    <div class="col-md-12">
      <label>Imagen actual:</label><br>
      <img src="../<?= $item['img'] ?>" alt="" width="120"><br>
      <input type="file" name="img" class="form-control mt-2" accept="image/*">
    </div>
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
      <a href="dashboard.php" class="btn btn-secondary">↩ Volver</a>
    </div>
  </form>
</body>
</html>
