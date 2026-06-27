<?php
$host = "localhost";     // Alamat server MySQL
$user = "root";          // Username MySQL default untuk XAMPP
$pass = "";              // Password biasanya kosong untuk root di XAMPP
$dbname = "crm";         // Nama database

// Cipta sambungan
$conn = new mysqli($host, $user, $pass, $dbname);
mysqli_query($conn, "SET time_zone = '+08:00'");

// Semak sambungan
if ($conn->connect_error) {
    die("Gagal sambung ke pangkalan data: " . $conn->connect_error);
}
?>




