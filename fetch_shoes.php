<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    echo "<tr><td colspan='5'>Giriş yapınız</td></tr>";
    exit();
}

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

if ($sort == "asc") {
    $sql .= " ORDER BY stok ASC";
} elseif ($sort == "desc") {
    $sql .= " ORDER BY stok DESC";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".htmlspecialchars($row['marka'])."</td>
                <td>".htmlspecialchars($row['numara'])."</td>
                <td>".htmlspecialchars($row['tip'])."</td>
                <td>".htmlspecialchars($row['stok'])."</td>
                <td>
                    <form method='POST' action='delete_shoe.php' onsubmit='return confirmDelete(this)'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='hidden' name='quantity' value='{$row['stok']}'>
                        
                    </form>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>Kayıt bulunamadı</td></tr>";
}
?>
