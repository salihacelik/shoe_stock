<?php
session_start();

/* ===== Veritabanı Bağlantısı ===== */
$servername = "localhost";
$db_user    = "root";
$db_pass    = "";
$dbname     = "shoe_stock";

$conn = new mysqli($servername, $db_user, $db_pass, $dbname);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

/* Mesajlar */
$info  = "";
$error = "";
if (isset($_GET['timeout']))    $info = "Oturum süreniz doldu. Lütfen tekrar giriş yapın.";
if (isset($_GET['registered'])) $info = "Kayıt başarılı. Lütfen giriş yapın.";

/* ===== Form gönderildiyse giriş kontrolü ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user = trim($_POST['username'] ?? "");
    $pass = trim($_POST['password'] ?? "");

    $stmt = $conn->prepare("SELECT id, kullanici_adi, sifre, ad, soyad FROM users WHERE kullanici_adi = ?");
    if ($stmt) {
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            $ok = false;
            if (!empty($row['sifre']) && str_starts_with($row['sifre'], '$')) {
                // Hashli şifre kontrolü
                $ok = password_verify($pass, $row['sifre']);
            } else {
                // Düz metin şifre kontrolü (geçici)
                $ok = ($row['sifre'] === $pass);
            }

            if ($ok) {
                session_regenerate_id(true);
                $_SESSION['username']   = $row['kullanici_adi'];
                $_SESSION['fullname']   = $row['ad'] . ' ' . $row['soyad'];
                $_SESSION['login_time'] = time();
                header("Location: dashboard.php");
                exit(); // mutlaka ekle
            } else {
                $error = "Kullanıcı adı veya şifre hatalı!";
            }

        } else {
            $error = "Kullanıcı bulunamadı!";
        }

        $stmt->close();

    } else {
        $error = "Sorgu hazırlanırken hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Giriş</title>
    <style>
        body { font-family: Arial; background:#f2f2f2; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .login-box { background:white; padding:30px; border-radius:12px; box-shadow:0 0 15px rgba(0,0,0,0.2); width:320px; }
        h2 { text-align:center; margin-top:0; }
        input { width:100%; padding:10px; margin:10px 0 12px; border-radius:6px; border:1px solid #ccc; box-sizing:border-box; }
        button { width:100%; padding:11px; border:none; background:#ff6600; color:white; border-radius:6px; cursor:pointer; font-weight:600; }
        button:hover { background:#e65c00; }
        .error { color:#b00020; text-align:center; margin-top:8px; }
        .info  { color:#0a7a2f; text-align:center; margin-top:8px; }
        .link  { text-align:center; margin-top:10px; font-size:14px; }
        .link a { text-decoration:none; color:#007bff; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Kullanıcı Giriş</h2>

    <?php if ($info)  echo "<p class='info'>".htmlspecialchars($info)."</p>"; ?>
    <?php if ($error) echo "<p class='error'>".htmlspecialchars($error)."</p>"; ?>

    <form method="POST" autocomplete="off">
        <input type="text"     name="username" placeholder="Kullanıcı Adı" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <button type="submit">Giriş Yap</button>
    </form>

    <p class="link">Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
</div>
</body>
</html>
