<?php
session_start();

// Eğer kullanıcı login değilse login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Giriş yapan kullanıcı bilgisi
$fullname = $_SESSION['fullname'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
   <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: 
            url('img/shoes-bg.png') no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 50px;
        background-color: rgba(255, 102, 0, 0.85); /* FLO turuncusu */
        color: white;
    }

    .logout-btn {
        padding: 8px 15px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .logout-btn:hover {
        background: #a71d2a;
    }

    .button-container {
        margin-top: 100px;
        text-align: center;
    }

    .menu-btn {
        padding: 15px 30px;
        margin: 15px;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        background: #ff6600; /* FLO turuncusu */
        color: white;
        transition: 0.3s;
    }

    .menu-btn:hover {
        background: #e65c00;
    }

    /* İstatistik kutuları */
    #stats-container {
        display: flex;
        gap: 20px;
        margin: 50px auto;
        justify-content: center;
    }

    .stat-box {
        background-color: white;
        border-radius: 10px;
        padding: 20px 30px;
        font-size: 16px;
        font-weight: bold;
        color: black;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 2px solid #ff6600; /* FLO turuncusu çerçeve */
    }

    #total-products {
        color: #ff6600; /* FLO turuncusu */
        font-size: 18px;
    }

    #total-brands {
         color: #ff6600; /* FLO turuncusu */
        font-size: 18px;
    }
   </style>
</head>
<body>
    <div class="header">
        <h2>Hoşgeldiniz, <?php echo htmlspecialchars($fullname); ?></h2>
        <form method="POST" action="logout.php">
            <button class="logout-btn" type="submit">Çıkış Yap</button>
        </form>
    </div>

    <!-- Butonlar -->
    <div class="button-container">
        <button class="menu-btn" onclick="window.location.href='add_shoe.php'">Ürün Ekle</button>
        <button class="menu-btn" onclick="window.location.href='delete_shoe.php'">Ürün Sil</button>
        <button class="menu-btn" onclick="window.location.href='list_shoe.php'">Ürün Listele</button>
    </div>

    <!-- İstatistik kutuları -->
    <div id="stats-container">
        <div class="stat-box">
            Toplam Ürün Sayısı: <span id="total-products">0</span>
        </div>
        <div class="stat-box">
            Toplam Farklı Marka: <span id="total-brands">0</span>
        </div>
    </div>

<script>
function loadStats() {
    fetch("stats.php")
        .then(response => response.json())
        .then(data => {
            document.getElementById("total-products").textContent = data.total_products;
            document.getElementById("total-brands").textContent = data.total_brands;
        });
}

// Sayfa açıldığında yükle
loadStats();

// Her 5 saniyede bir güncelle
setInterval(loadStats, 5000);
</script>

</body>
</html>
