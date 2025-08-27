<?php
session_start();

/* ===== Veritabanı Bağlantısı ===== */
$servername = "localhost";
$db_user    = "root";
$db_pass    = "";
$dbname     = "shoe_stock"; // doğru DB adı

$conn = new mysqli($servername, $db_user, $db_pass, $dbname);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

/* ===== Form gönderildiyse ürün ekleme ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brand = trim($_POST['brand'] ?? "");
    $size  = trim($_POST['size'] ?? "");
    $stock = intval($_POST['stock'] ?? 0);
    $type  = trim($_POST['type'] ?? "");

    if ($brand && $size) {
        $stmt = $conn->prepare("INSERT INTO shoes (brand, size, stock, type) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssis", $brand, $size, $stock, $type);
            if ($stmt->execute()) {
                $message = "Ürün başarıyla eklendi!";
            } else {
                $message = "Ürün eklenirken hata oluştu: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Sorgu hazırlanamadı: " . $conn->error;
        }
    } else {
        $message = "Marka ve beden alanları zorunludur!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Ekle</title>
    <style>
    /* Geri ve Dashboard/Anasayfa & Logout Butonları */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 10px 0;
}
.header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.header-left a {
    text-decoration: none;
    color: white;
    background-color: #ff6600;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 14px;
}
.header-left a:hover {
    opacity: 0.8;
}
.header-right a {
    text-decoration: none;
    color: white;
    background-color: #ff6600;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 14px;
}
.header-right a:hover {
    opacity: 0.8;
}

/* Önceki CSS */
.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    padding: 10px 15px;
    background-color: #ff6600; /* FLO turuncusu */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    z-index: 1000;
}


        body { font-family: Arial, sans-serif; padding: 20px; background:#f2f2f2; }
        .form-box { background:white; padding:20px; border-radius:10px; width: 350px; margin:auto; }
        input { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ccc; }
        button { width:100%; padding:11px; background:#ff6600; color:white; border:none; border-radius:6px; cursor:pointer; }
        button:hover { background:#e65c00; }
        .message { text-align:center; margin-top:10px; color:green; }
    </style>
</head>
<body>
    <!-- Geri Butonu -->
<div class="header">
    <div class="header-left">
        <a href="dashboard.php">🏠 Anasayfa</a>
        <a href="javascript:history.back()">🔙 Geri</a>
    </div>
    <div class="header-right">
        <a href="logout.php" class="logout-btn">Çıkış Yap</a>

    </div>
</div>
    <div class="form-box">
        <h2>Ürün Ekle</h2>
        <?php if (!empty($message)) echo "<p class='message'>".htmlspecialchars($message)."</p>"; ?>
        <form method="POST">
            <input type="text" name="brand" placeholder="Marka" required>
            <input type="text" name="size" placeholder="Beden" required>
            <input type="number" name="stock" placeholder="Stok" min="0" value="0">
            <input type="text" name="type" placeholder="Tip">
            <button type="submit">Ekle</button>
        </form>
        <!-- Ürünleri Listele Butonu -->
<div style="margin-top: 20px; text-align: center;">
    <button type="button" onclick="window.location.href='list_shoe.php'" 
            style="padding: 12px 25px; background-color: #ff6600; color: white; border: none; border-radius: 6px; cursor: pointer;">
        Ürünleri Listele
    </button>
</div>

    </div>
</body>
</html>