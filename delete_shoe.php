<?php
session_start();
include("db.php");

// Kullanıcı login değilse yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// POST ile stok düşürme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['quantity'])) {
    $id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);

    // Mevcut stok miktarını al
    $stmt = $conn->prepare("SELECT stok FROM shoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        $_SESSION['error'] = "Ürün bulunamadı.";
        header("Location: list_shoe.php");
        exit();
    }

    $row = $res->fetch_assoc();
    $current_stock = $row['stok'];

    if ($quantity > $current_stock) {
        $_SESSION['error'] = "Silmek istediğiniz miktar stoktan fazla.";
        header("Location: list_shoe.php");
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

    $_SESSION['success'] = "$quantity adet silindi.";
    header("Location: list_shoe.php");
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
    <button class="back-button" onclick="window.history.back()">← Geri</button>

    <div class="container">
        <div class="top-btn">
            <a href="list_shoe.php">Listele</a>
        </div>

        <h2 style="text-align:center; color:#ff6600;">Ürün Listesi</h2>

        <?php if(isset($_SESSION['success'])) { echo "<p style='color:green;'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
        <?php if(isset($_SESSION['error'])) { echo "<p style='color:red;'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); } ?>

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
                    <form method="post" action="list_shoe.php" onsubmit="return confirmDelete(this)">
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
