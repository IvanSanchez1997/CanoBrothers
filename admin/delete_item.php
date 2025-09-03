<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}

$menu_file = "../data/menu.json";
$menu = json_decode(file_get_contents($menu_file), true);

$categoria = $_POST["categoria"] ?? null;
$index = $_POST["index"] ?? null;

if ($categoria === null || $index === null || !isset($menu[$categoria][$index])) {
    die("Producto no encontrado.");
}

// Obtener ruta de imagen antes de eliminar
$imgPath = "../" . $menu[$categoria][$index]["img"];

// Eliminar producto del array
unset($menu[$categoria][$index]);
$menu[$categoria] = array_values($menu[$categoria]); // Reindexar

// Guardar cambios en el JSON
file_put_contents($menu_file, json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Verificar si la imagen ya no la usa otro producto
$stillUsed = false;
foreach ($menu as $cat => $items) {
    foreach ($items as $item) {
        if ($item["img"] === str_replace("../", "", $imgPath)) {
            $stillUsed = true;
            break 2;
        }
    }
}

// Si no se usa en otro producto y existe en el servidor → eliminar archivo
if (!$stillUsed && file_exists($imgPath) && strpos($imgPath, "../assets/img/productos/") === 0) {
    unlink($imgPath);
}

header("Location: dashboard.php");
exit;
?>
