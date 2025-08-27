<?php
session_start();
include("db.php");

// Kullanıcı login değilse yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ürünleri çek
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort   = isset($_GET['sort']) ? $_GET['sort'] : '';

if (!empty($search)) {
    $sql = "SELECT * FROM shoes 
            WHERE marka LIKE '%$search%' 
               OR numara LIKE '%$search%' 
               OR tip LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM shoes";
}

// 🔹 Sıralama eklendi
if ($sort == "asc") {
    $sql .= " ORDER BY stok ASC";
} elseif ($sort == "desc") {
    $sql .= " ORDER BY stok DESC";
}

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
        body { font-family: Arial; background-color: #f2f2f2; margin: 0; padding: 0; }
        .back-button { position: absolute; top: 20px; left: 20px; padding: 10px 15px; background-color: #ff6600; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; z-index: 1000; }
        .container { width: 90%; margin: 60px auto 30px auto; }
        h2 { text-align:center; color:#ff6600; margin-bottom: 15px; }
        .search-form { margin-bottom: 20px; text-align: center; }
        .search-form input, .search-form select { padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; margin-right: 5px; }
        .search-form input { width: 220px; }
        .search-form button { padding: 8px 15px; background-color: #ff6600; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .search-form button:hover { background-color: #e65c00; }
        .shoe-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .shoe-table th, .shoe-table td { padding: 12px 15px; text-align: center; border-bottom: 1px solid #ddd; }
        .shoe-table th { background-color: #ff6600; color: white; font-weight: bold; }
        .shoe-table tr:hover { background-color: #f9f9f9; }
        .top-btn { text-align: right; margin-bottom: 10px; }
        .top-btn a { background-color: #ff6600; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
        .top-btn a:hover { opacity: 0.8; }
        .error { color:#b00020; text-align:center; margin-top:8px; }
        .info  { color:#0a7a2f; text-align:center; margin-top:8px; }
        .delete-btn { background-color: #d9534f; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .delete-btn:hover { opacity: 0.8; }
    </style>
</head>
<body>

<button class="back-button" onclick="window.history.back()">← Geri</button>

<div class="container">

    <?php
    if(isset($_SESSION['error'])) { echo "<div class='error'>{$_SESSION['error']}</div>"; unset($_SESSION['error']); }
    if(isset($_SESSION['success'])) { echo "<div class='info'>{$_SESSION['success']}</div>"; unset($_SESSION['success']); }
    ?>

    <div class="top-btn">
        <a href="list_shoe.php">Listele</a>
    </div>

    <form method="GET" action="list_shoe.php" class="search-form">
        <input type="text" name="search" placeholder="Marka, numara veya tip ara..." 
               value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <select name="sort">
            <option value="">Sıralama Seç</option>
            <option value="asc" <?php if($sort=="asc") echo "selected"; ?>>Stok (Artan)</option>
            <option value="desc" <?php if($sort=="desc") echo "selected"; ?>>Stok (Azalan)</option>
        </select>
        <button type="submit">Ara</button>
    </form>

    <h2>Ürün Listesi</h2>

<table class="shoe-table">
    <thead>
        <tr>
            <th>Marka</th>
            <th>Numara</th>
            <th>Tip</th>
            <th>Stok</th>
            
        </tr>
    </thead>
    <tbody id="shoe-table-body">
        <?php if (!empty($shoes)): ?>
            <?php foreach ($shoes as $shoe): ?>
                <tr id="shoe-<?php echo $shoe['id']; ?>">
                    <td><?php echo htmlspecialchars($shoe['marka']); ?></td>
                    <td><?php echo htmlspecialchars($shoe['numara']); ?></td>
                    <td><?php echo htmlspecialchars($shoe['tip']); ?></td>
                    <td><?php echo htmlspecialchars($shoe['stok']); ?></td>
                    <td>
                        <button class="delete-btn" onclick="deleteShoe(<?php echo $shoe['id']; ?>, <?php echo $shoe['stok']; ?>)">Sil</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Kayıt bulunamadı</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

<script>
// Sayfa yüklendiğinde ürünleri çek
document.addEventListener("DOMContentLoaded", fetchShoes);

function fetchShoes(search = '', sort = '') {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `fetch_shoes.php?search=${search}&sort=${sort}`, true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById("shoe-table-body").innerHTML = this.responseText;
        }
    };
    xhr.send();
}

// Arama + sıralama formu AJAX ile çalışsın
const form = document.querySelector(".search-form");
form.addEventListener("submit", function(e) {
    e.preventDefault();
    const search = form.querySelector('input[name="search"]').value;
    const sort = form.querySelector('select[name="sort"]').value;
    fetchShoes(search, sort);
});

// 🔹 AJAX ile silme fonksiyonu
function deleteShoe(id, qty) {
    if (!confirm(qty + " adet silinsin mi?")) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_shoe.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status === 200) {
            // Başarılı ise satırı kaldır
            const row = document.getElementById("shoe-" + id);
            if (row) row.remove();
        } else {
            alert("Silme işlemi başarısız!");
        }
    };
    xhr.send("id=" + id);
}
</script>

</body>
</html>
