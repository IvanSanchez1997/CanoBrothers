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
/*$nueva_categoria = trim($_POST["nueva_categoria"]);*/
$nombre = trim($_POST["name"]);
$allergens = isset($_POST["allergens"]) ? $_POST["allergens"] : [];
$precio1 = trim($_POST["price1"]);
$precio2 = trim($_POST["price2"]);
$precio3 = trim($_POST["price3"]);

// Si hay nueva categoría, se usa esa
if (!empty($nueva_categoria)) {
    $categoria = $nueva_categoria;
    if (!isset($menu[$categoria])) {
        $menu[$categoria] = []; // Crear categoría nueva si no existe
    }
}

// === 2. Manejo de la imagen ===
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
    $upload_dir = "../assets/img/productos/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = basename($_FILES["imagen"]["name"]);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // Generar nombre único para evitar colisiones
    $new_filename = uniqid("prod_") . "." . $extension;
    $target_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_path)) {
        $ruta_imagen = "assets/img/productos/" . $new_filename;
    } else {
        die("Error al subir la imagen.");
    }
} /*else {
    die("No se ha seleccionado ninguna imagen.");
}*/

// === 3. Nuevo producto ===
$nuevo_item = [
    "name" => $nombre,
    "alert" => "",
    "allergensImg" => $allergens, // 👈 aquí va el array de rutas
    "allergensDes" => implode(", ", array_map(function($ruta) use ($allergens) {
        // Convertir ruta en texto simple (ej: lactosa.png -> Lactosa)
        return ucfirst(pathinfo($ruta, PATHINFO_FILENAME));
    }, $allergens)),
    "description" => "",
    "price1" => $precio1,
    "price2" => $precio2,
    "price3" => $precio3,
    "img" => $ruta_imagen
];

// Insertar en la categoría correspondiente
$menu[$categoria][] = $nuevo_item;

// === 4. Guardar JSON ===
file_put_contents($menu_file, json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: dashboard.php");
exit;
