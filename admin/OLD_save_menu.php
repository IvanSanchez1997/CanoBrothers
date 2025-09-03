<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}
$menuFile = __DIR__ . "/../data/menu.json";
$imgDir = __DIR__ . "/../assets/img/productos/";
if (!file_exists($imgDir)) {
    mkdir($imgDir, 0777, true);
}
$imgName = basename($_FILES["imagen"]["name"]);
$targetFile = $imgDir . $imgName;
if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $targetFile)) {
    $categoria = $_POST["categoria"];
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $imgPath = "assets/img/productos/" . $imgName;
    $menu = file_exists($menuFile) ? json_decode(file_get_contents($menuFile), true) : [];
    if (!isset($menu[$categoria])) {
        $menu[$categoria] = [];
    }
    $menu[$categoria][] = ["nombre" => $nombre, "precio" => $precio, "img" => $imgPath];
    file_put_contents($menuFile, json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: dashboard.php?success=1");
    exit;
} else {
    echo "Error al subir la imagen.";
}