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

$sql = "SELECT * FROM shoes";

if (!empty($search)) {
    $sql .= " WHERE marka LIKE '%$search%' 
              OR numara LIKE '%$search%' 
              OR tip LIKE '%$search%'";
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
        
        .error { color:#b00020; text-align:center; margin-top:8px; }
        .info  { color:#0a7a2f; text-align:center; margin-top:8px; }
        .delete-btn { background-color: #edeaeaff; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .delete-btn:hover { opacity: 0.8; }
        .logout { color:red; text-decoration:none; font-weight:bold; margin-left:20px; }
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

    <?php
    if(isset($_SESSION['error'])) { echo "<div class='error'>{$_SESSION['error']}</div>"; unset($_SESSION['error']); }
    if(isset($_SESSION['success'])) { echo "<div class='info'>{$_SESSION['success']}</div>"; unset($_SESSION['success']); }
    ?>

    

    <form method="GET" action="list_shoe.php" class="search-form">
        <input type="text" name="search" placeholder="Marka, numara veya tip ara..." 
               value="<?php echo htmlspecialchars($search); ?>">
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
// 🔹 AJAX ile silme fonksiyonu
function deleteShoe(id, qty) {
    if (!confirm(qty + " adet silinsin mi?")) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_shoe.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status === 200) {
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
