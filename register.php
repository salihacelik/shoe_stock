<?php
session_start();
include("db.php"); // veritabanı bağlantısı

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = trim($_POST['password']);
    $email    = trim($_POST['email']);

    // fullname'den ad ve soyad ayır
    $ad = '';
    $soyad = '';
    $parts = explode(' ', $fullname, 2);
    $ad = $parts[0];
    if (isset($parts[1])) $soyad = $parts[1];

    // Kullanıcı adı veya e-posta var mı kontrol et
    $checkUser = $conn->prepare("SELECT * FROM users WHERE kullanici_adi=? OR mail=?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $error = "Kullanıcı adı veya e-posta zaten alınmış!";
    } else {
        // Şifreyi güvenli şekilde hash’le
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Yeni kullanıcı ekle
        $stmt = $conn->prepare("INSERT INTO users (kullanici_adi, ad, soyad, mail, sifre) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $ad, $soyad, $email, $hashedPassword);

        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $error = "Kayıt sırasında hata oluştu!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
   <style>
    body { 
        font-family: Arial; 
        background:#f2f2f2; 
        display:flex; 
        justify-content:center; 
        align-items:center; 
        height:100vh; 
        margin:0; 
    }

    .register-box { 
        background:white; 
        padding:40px; 
        border-radius:14px; 
        box-shadow:0 0 20px rgba(0,0,0,0.25); 
        width:400px; 
        border-top: 6px solid #FF6600; /* FLO turuncusu vurgusu */
    }

    h2 { 
        text-align:center; 
        margin-top:0; 
        margin-bottom:25px; 
        font-size:24px; 
        color:#FF6600; /* Başlık turuncu */
    }

    input { 
        width:100%; 
        padding:12px; 
        margin:12px 0; 
        border-radius:6px; 
        border:1px solid #ccc; 
        font-size:15px; 
        box-sizing:border-box; 
        transition: 0.3s; 
    }

    input:focus { 
        border-color: #FF6600; /* Odaklanınca turuncu */
        outline: none; 
        box-shadow: 0 0 5px rgba(255,102,0,0.5); 
    }

    button { 
        width:100%; 
        padding:12px; 
        border:none; 
        background:#FF6600; /* FLO turuncusu */
        color:white; 
        border-radius:6px; 
        cursor:pointer; 
        font-weight:600; 
        font-size:15px; 
        transition: 0.3s; 
    }

    button:hover { 
        background:#e65c00; /* Daha koyu turuncu hover */
    }

    .error { 
        color:#b00020; 
        text-align:center; 
        margin-top:8px; 
    }

    .info  { 
        color:#0a7a2f; 
        text-align:center; 
        margin-top:8px; 
    }

    .link  { 
        text-align:center; 
        margin-top:20px; 
        font-size:14px; 
    }

    .link a { 
        text-decoration:none; 
        color:#FF6600; /* Link turuncu */
        font-weight:500;
    }

    .link a:hover { 
        text-decoration:underline; 
    }
</style>

</head>
<body>
    <div class="register-box">
        <h2>Kayıt Ol</h2>
        <?php if(!empty($error)) echo '<div class="error">'.$error.'</div>'; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required>
            <input type="text" name="fullname" placeholder="Ad Soyad" required>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit">Kayıt Ol</button>
        </form>
        <div class="link">
            Zaten hesabın var mı? <a href="login.php">Giriş Yap</a>
        </div>
    </div>
</body>
</html>
