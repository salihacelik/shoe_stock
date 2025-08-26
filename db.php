<?php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP varsayılan şifre boş
$dbname = "shoe_stock"; // doğru veritabanı adı

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>