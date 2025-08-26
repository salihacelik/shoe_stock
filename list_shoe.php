<?php
session_start();
include("db.php");

// Uyarı mesajı değişkeni
$message = "";

// Ürünleri çek
$sql = "SELECT marka, numara, tip, SUM(stok) as toplam_stok, MIN(id) as id FROM shoes GROUP BY marka, numara, tip ORDER BY marka ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Listesi</title>
    <style>
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
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background-color: #ff7f00; color: white; } /* Flo turuncusu */
        tr:nth-child(even) { background-color: #f9f9f9; }
        h2 { color: #ff7f00; text-align: center; }
        .btn { background-color: #ff7f00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 15px; }
        .btn:hover { background-color: #e66f00; }
        .error { color: #b00020; text-align: center; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <!-- Geri Butonu -->
    <button class="back-button" onclick="window.history.back()">← Geri</button>

<h2>Ayakkabı Listesi</h2>

<a href="add_shoe.php" class="btn">Yeni Ürün Ekle</a>

<?php
if ($result->num_rows > 0) {
    $hasValid = false;
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Marka</th>
                <th>Numara</th>
                <th>Tip</th>
                <th>Stok</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        // eksik veri kontrolü
        if (empty($row['marka']) || empty($row['numara']) || empty($row['tip'])) {
            $message = "Dikkat: Bazı ürünlerde eksik bilgi var, listelenmedi.";
            continue; // bu ürünü atla
        }
        $hasValid = true;
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['marka']}</td>
                <td>{$row['numara']}</td>
                <td>{$row['tip']}</td>
                <td>{$row['toplam_stok']}</td>
              </tr>";
    }
    echo "</table>";

    if (!$hasValid && !empty($message)) {
        echo "<div class='error'>{$message}</div>";
    }
} else {
    echo "<div class='error'>Hiç ürün yok</div>";
}

// eksik veri mesajı varsa göster
if (!empty($message)) {
    echo "<div class='error'>{$message}</div>";
}
?>

</body>
</html>
