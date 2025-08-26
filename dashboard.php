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
            padding: 20px;
            text-align: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: url('img/shoes-bg.png') no-repeat center top;
            background-size: cover;
            background-color: #f2f2f2;
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
</body>
</html>