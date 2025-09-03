<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}

$menu_file = "../data/menu.json";
$menu = json_decode(file_get_contents($menu_file), true);

// === 1. Datos del formulario ===
$categoria = trim($_POST["categoria"]);
$nombre = trim($_POST["name"]);
$precio1 = trim($_POST["price1"]);
$precio2 = trim($_POST["price2"]);
$precio3 = trim($_POST["price3"]);
$alert   = trim($_POST["alert"]);
$descripcion = trim($_POST["description"]);
$allergensDes = trim($_POST["allergensDes"]);
$allergens = isset($_POST["allergens"]) ? $_POST["allergens"] : [];

// Crear categoría si no existe
if (!isset($menu[$categoria])) {
    $menu[$categoria] = [];
}

// === 2. Procesar alérgenos ===
$allergens_clean = [];
foreach ($allergens as $ruta) {
    if (!empty($ruta)) {
        $allergens_clean[] = $ruta;
    }
}

// Autogenerar descripción si está vacía
if (empty($allergensDes)) {
    $names = [];
    foreach ($allergens_clean as $ruta) {
        $base = pathinfo($ruta, PATHINFO_FILENAME);
        $names[] = ucfirst(str_replace(['_', '-'], ' ', $base));
    }
    $allergensDes = implode(', ', $names);
}

// === 3. Manejo de imagen ===
$ruta_imagen = ""; // valor por defecto si no hay imagen
if (!empty($_FILES["img"]["name"]) && isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
    $allowedExt = ['jpg','jpeg','png','gif','svg','webp'];

    if (in_array($ext, $allowedExt, true)) {
        $targetDirFs = __DIR__ . "/../assets/img/products/";
        if (!is_dir($targetDirFs)) mkdir($targetDirFs, 0755, true);

        // Nombre único y seguro
        $fileName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $targetFile = $targetDirFs . $fileName;

        if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
            $ruta_imagen = "assets/img/products/" . $fileName;
        } else {
            die("Error al mover la imagen subida.");
        }
    } else {
        die("Extensión de imagen no permitida: " . htmlspecialchars($ext));
    }
}

// === 4. Nuevo producto ===
$nuevo_item = [
    "name" => $nombre,
    "alert" => $alert,
    "allergensImg" => $allergens_clean,
    "allergensDes" => $allergensDes,
    "description" => $descripcion,
    "price1" => $precio1,
    "price2" => $precio2,
    "price3" => $precio3,
    "img" => $ruta_imagen
];

// Insertar en la categoría correspondiente
$menu[$categoria][] = $nuevo_item;

// === 5. Guardar JSON ===
$saved = file_put_contents(
    $menu_file,
    json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);
if ($saved === false) {
    die("No se pudo guardar el JSON (revisa permisos en {$menu_file}).");
}

// === 6. Redirigir ===
header("Location: dashboard.php");
exit;
