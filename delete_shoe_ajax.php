<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Giriş yapılmamış.']);
    exit();
}

if ($_SERVER['POST'] && isset($_POST['id'], $_POST['quantity'])) {
    $id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);

    $stmt = $conn->prepare("SELECT stok FROM shoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Ürün bulunamadı.']);
        exit();
    }

    $row = $res->fetch_assoc();
    $current_stock = $row['stok'];

    if ($quantity > $current_stock) {
        echo json_encode(['status' => 'error', 'message' => 'Silmek istediğiniz miktar stoktan fazla.']);
        exit();
    }

    $new_stock = $current_stock - $quantity;

    if ($new_stock > 0) {
        $stmt = $conn->prepare("UPDATE shoes SET stok = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("DELETE FROM shoes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => "$quantity adet silindi.", 'new_stock' => $new_stock]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
?>
