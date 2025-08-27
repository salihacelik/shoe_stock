<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login gerekli']);
    exit();
}

// POST ile AJAX üzerinden silme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['quantity'])) {
    $id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);

    // Mevcut stok miktarını al
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

    echo json_encode(['status'=>'success','new_stock'=>$new_stock]);
    exit();
}

// Sayfayı listeleme için
$sql = "SELECT * FROM shoes";
$result = $conn->query($sql);
$shoes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shoes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Ürün Listele</title>
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

body {
    font-family: Arial;
    background-color: #f2f2f2;
}

.container {
    width: 90%;
    margin: 30px auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background-color: #ff6600;
    color: white;
}

input[type="number"] {
    width: 60px;
    padding: 5px;
}

button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
}

.top-btn {
    text-align: right;
    margin-bottom: 10px;
}

.top-btn a {
    background-color: #ff6600;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
}

.top-btn a:hover {
    opacity: 0.8;
}
.logout-btn {
    background-color: red;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
}

.logout-btn:hover {
    opacity: 0.8;
}

</style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <a href="dashboard.php">🏠 Anasayfa</a>
        <a href="javascript:history.back()">🔙 Geri</a>
    </div>
    <div class="header-right">
        <a href="logout.php" class="logout-btn">Çıkış Yap</a>

    </div>
</div>

<div class="container">
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
<td><?= $shoe['stok'] ?></td>
<td>
<form method="post" onsubmit="return confirmDelete(this)" data-id="<?= $shoe['id'] ?>">
<input type="number" name="quantity" placeholder="Adet" min="1" max="<?= $shoe['stok'] ?>" required>
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
    if(!confirm(qty + " adet silinsin mi?")) return false;

    const id = form.dataset.id;
    const quantity = qty;

    fetch("", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            location.reload(); // listeyi yenile
        } else {
            alert(data.message);
        }
    });

    return false; // formun normal submit olmasını engelle
}
</script>
</body>
</html>
