<?php
session_start();
include("db.php");

// Kullanıcı login değilse yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ürünleri grup bazlı çek (marka, numara, tip aynı olanları topla)
$result = $conn->query("SELECT marka, numara, tip, SUM(stok) AS total_stock, MIN(id) AS min_id 
                        FROM shoes 
                        GROUP BY marka, numara, tip 
                        ORDER BY marka ASC");
$shoes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Listele</title>
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
        body { font-family: Arial; background-color: #f2f2f2; }
        .container { width: 90%; margin: 30px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #ff6600; color: white; }
        input[type="number"] { width: 60px; padding: 5px; }
        button { background: none; border: none; cursor: pointer; font-size: 18px; }
        .top-btn { text-align: right; margin-bottom: 10px; }
        .top-btn a { background-color: #ff6600; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
        .top-btn a:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <!-- Geri Butonu -->
    <button class="back-button" onclick="window.history.back()">← Geri</button>

<div class="container">
    <div class="top-btn">
        <a href="list_shoe.php">Listele</a>
    </div>

    <h2 style="text-align:center; color:#ff6600;">Ürün Listesi</h2>

    <table>
        <tr>
            <th>Marka</th>
            <th>Numara</th>
            <th>Tip</th>
            <th>Stok</th>
            <th>Sil</th>
        </tr>

        <?php foreach ($shoes as $shoe): ?>
        <tr>
            <td><?= htmlspecialchars($shoe['marka']) ?></td>
            <td><?= htmlspecialchars($shoe['numara']) ?></td>
            <td><?= htmlspecialchars($shoe['tip']) ?></td>
            <td><?= $shoe['total_stock'] ?></td>
            <td>
                <form method="post" action="delete_shoe.php" onsubmit="return confirmDelete(this)">
                    <input type="hidden" name="id" value="<?= $shoe['min_id'] ?>">
                    <input type="number" name="quantity" placeholder="Adet" min="1" max="<?= $shoe['total_stock'] ?>" required>
                    <button type="submit">🗑️</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

<script>
function confirmDelete(form) {
    const qty = form.querySelector('input[name="quantity"]').value;
    return confirm(qty + " adet silinsin mi?");
}
</script>

</body>
</html>
