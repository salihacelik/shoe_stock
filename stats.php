<?php
session_start();
include("db.php");

// Toplam ürün sayısı
$result1 = $conn->query("SELECT SUM(stok) AS total_products FROM shoes");
$row1 = $result1->fetch_assoc();
$total_products = $row1['total_products'] ?? 0;

// Toplam farklı marka sayısı
$result2 = $conn->query("SELECT COUNT(DISTINCT marka) AS total_brands FROM shoes");
$row2 = $result2->fetch_assoc();
$total_brands = $row2['total_brands'];

echo json_encode([
    "total_products" => $total_products,
    "total_brands" => $total_brands
]);
?>

