<?php
session_start();
require 'db_connection.php';
require 'log-helper.php';

$staff_id = $_SESSION['id_staff'] ?? null;

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT id_staff, role, nama FROM staff WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0){
  $user = $result->fetch_assoc();

  // Simpan ke sesi jika belum ada
  $_SESSION['id_staff'] = $user['id_staff'];
  $_SESSION['role'] = $user['role'];
  
  // Log aktiviti ini
  logAktiviti($conn, 'Mengakses Dashboard Staff');
} else {
  // Kalau tiada pengguna, redirect ke login page
  echo "Pengguna tidak dijumpai.";
  exit;
}

// Kira jumlah produk
$result_produk = $conn->query("SELECT COUNT(*) AS total_produk FROM produk");
$row_produk = $result_produk->fetch_assoc();
$total_produk = $row_produk['total_produk'];

// Kira jumlah pelanggan
$result_pelanggan = $conn->query("SELECT COUNT(*) AS total_pelanggan FROM pelanggan");
$row_pelanggan = $result_pelanggan->fetch_assoc();
$total_pelanggan = $row_pelanggan['total_pelanggan'];

// Kira jumlah aduan
$result_aduan = $conn->query("SELECT COUNT(*) AS total_aduan FROM aduan WHERE status = 'Baru'");
$row_aduan = $result_aduan->fetch_assoc();
$total_aduan = $row_aduan['total_aduan'];


if ($staff_id) {
    $stmt = $conn->prepare("SELECT nama FROM staff WHERE id_staff = ?");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $stmt->bind_result($nama);
    if ($stmt->fetch()) {
        $staff_name = $nama;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ms">
  <head>
    <meta charset="UTF-8">
    <title>Dashboard Staf</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
      
    <style>
      body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f9;
        color: #2c3e50;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      header {
        background-color: #1a237e;
        padding: 20px 40px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }

      header h1 {
        font-size: 26px;
        margin: 0;
        flex: 1;
        letter-spacing: 2px;
      }

      header .header-right {
        display: flex;
        align-items: center;
      }

      header .header-right h2 {
        margin: 0 20px 0 0;
        font-size: 18px;
      }

      header .header-right a {
        color: #fff;
        text-decoration: none;
        padding: 8px 15px;
        background-color: #e74c3c;
        border-radius: 5px;
        transition: background-color 0.3s ease;
      }

      header .header-right a:hover {
        background-color: #c0392b;
      }

      .container {
        padding: 40px 60px;
        flex-grow: 1;
      }

      .grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: repeat(2, auto);
        gap: 30px;
        justify-items: stretch;
      }

      .card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        transition: all 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: auto;
      }

      .card:hover {
        transform: translateY(-20px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      }

      .card i {
        font-size: 36px;
        color: #1a237e;
        margin-bottom: 15px;
      }

      .card h2 {
        margin: 0 0 10px;
        font-size: 20px;
      }

      .card p {
        font-size: 14px;
        color: #555;
        flex: 1;
      }

      .card a {
        display: inline-block;
        margin-top: 20px;
        color: #fff;
        background-color: #1a237e;
        text-decoration: none;
        font-weight: bold;
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
      }

      .card a:hover {
        background-color: #3f51b5;
      }

      .footer {
        background-color: #2c3e50;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 14px;
        margin-top: auto;
      }

      .footer .stats {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
        font-size: 16px;
      }

      .footer .stats div {
        text-align: center;
      }
    </style>
  </head>
  
  <body>
    <header>
      <h1>Dashboard Staff</h1>
      <div class="header-right">
        <h2>Selamat Datang, <?php echo isset($user['nama']) ? $user['nama'] : 'Pengguna'; ?> 👋</h2>
        <a href="logout.php">Log Keluar</a>
      </div>
    </header>

    <div class="container">
      <div class="grid">
        <div class="card">
          <i class="fas fa-box"></i>
          <h2>Pengurusan Produk & Jualan</h2>
          <p>Kemaskini maklumat produk dan jejak prestasi jualan.</p>
          <a href="produk.php">Urus Produk</a>
        </div>

        <div class="card">
          <i class="fas fa-users"></i>
          <h2>Senarai Pelanggan</h2>
          <p>Lihat dan semak info pelanggan.</p>
          <a href="pelanggan.php">Lihat Pelanggan</a>
        </div>

        <div class="card">
          <i class="fa-solid fa-headset"></i>
          <h2>Aduan</h2>
          <p>Semak dan balas maklum balas aduan pelanggan.</p>
          <a href="aduan.php">Lihat Aduan</a>
        </div>

        <div class="card">
          <i class="fas fa-chart-line"></i>
          <h2>Analisis Jualan</h2>
          <p>Lihat statistik jualan.</p>
          <a href="analisis-jualan2.php">Lihat Analisis</a>
        </div>

        <div class="card">
          <i class="fas fa-stream"></i>
          <h2>Pemantauan Saluran Jualan</h2>
          <p>Jejak status pelanggan (pipeline).</p>
          <a href="pipeline.php">Pantau Saluran</a>
        </div>

        <div class="card">
          <i class="fa-brands fa-rocketchat"></i>
          <h2>Forum Staf</h2>
          <p>Ruangan interaksi staff.</p>
          <a href="forum.php">Forum Staf</a>
        </div>
      </div>
    </div>

    <div class="footer">
      <p>&copy; 2025 Sistem CRM. Semua hak cipta dilindungi.</p>
      <div class="stats">
        <div>
          <h3><?php echo $total_produk; ?></h3>
          <p>Produk</p>
        </div>
        <div>
          <h3><?php echo $total_pelanggan; ?></h3>
          <p>Pelanggan</p>
        </div>
        <div>
          <h3><?php echo $total_aduan; ?></h3>
          <p>Aduan Baru</p>
        </div>
      </div>
    </div>
  </body>
</html>
