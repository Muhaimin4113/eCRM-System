<?php 
session_start(); // Start the session

date_default_timezone_set('Asia/Kuala_Lumpur');

require 'db_connection.php';
require 'log-helper.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama, role FROM staff WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0){
  $user = $result->fetch_assoc();

  // Simpan ke sesi jika belum ada
  $_SESSION['id_staff'] = $user['id_staff'];
  $_SESSION['role'] = $user['role'];

  // Log aktiviti ini
  logAktiviti($conn, 'Mengakses Dashboard Admin');
} else {
  // Kalau tiada pengguna, redirect ke login page
  echo "Pengguna tidak dijumpai.";
  exit;
}

// Query to get the count of staff
$staff_count_sql = "SELECT COUNT(*) AS total_staff FROM staff";
$staff_count_result = $conn->query($staff_count_sql);
$staff_count = $staff_count_result->fetch_assoc()['total_staff'];

// Query to get the count of products
$product_count_sql = "SELECT COUNT(*) AS total_products FROM produk";
$product_count_result = $conn->query($product_count_sql);
$product_count = $product_count_result->fetch_assoc()['total_products'];

// Query to get the count of customers
$customer_count_sql = "SELECT COUNT(*) AS total_customers FROM pelanggan";
$customer_count_result = $conn->query($customer_count_sql);
$customer_count = $customer_count_result->fetch_assoc()['total_customers'];
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      margin: 0;
      padding: 0;
    }

    /* Navbar Styling */
    .navbar {
      background-color: #2c5364;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h1 {
      font-size: 24px;
      color: #fff;
      margin: 0;
    }

    .navbar .greeting {
      font-size: 18px;
      color: #ffd700;
      margin-left:auto;
    }

    .navbar .logout-btn {
      padding: 8px 16px;
      background-color: #e74c3c;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      text-decoration: none;
    }

    .navbar .logout-btn:hover {
      background-color: #c0392b;
    }

    /* Container for dashboard cards */
    .dashboard {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      padding: 20px;
      margin-top: 30px;
    }

    /* Individual dashboard cards */
    .card {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-20px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
    }

    .card h2 {
      font-size: 20px;
      margin-bottom: 10px;
    }

    .card p {
      font-size: 16px;
      color: #ccc;
    }

    .card button {
      padding: 10px 20px;
      background-color: #4caf50;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }

    .card button:hover {
      background-color: #45a049;
    }

    /* Quote Section Styling */
    .quote-section {
      text-align: center;
      margin-top: 40px;
      font-size: 18px;
      color: #f39c12;
      font-style: italic;
    }

    /* New cards below quote */
    .summary-dashboard {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      padding: 20px;
      margin-top: 30px;
    }

    .summary-card {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      text-align: center;
    }

    .summary-card h2 {
      font-size: 22px;
      margin-bottom: 10px;
    }

    .summary-card p {
      font-size: 18px;
      color: #fff;
      font-weight: bold;
    }

    /* Footer */
    .footer {
      background-color: #2c5364;
      text-align: center;
      padding: 10px;
      position: fixed;
      width: 100%;
      bottom: 0;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <div class="navbar">
    <h1>Sistem CRM</h1>
    <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
    <a href="logout.php" class="logout-btn">Logout</a> <!-- Logout Button -->
  </div>

  <!-- Dashboard -->
  <div class="dashboard">
    <div class="card">
      <h2>Pengurusan Staf</h2>
      <p>Lihat & Urus Staf</p>
      <button onclick="window.location.href='pengurusan-staf.php'">Lihat Staf</button>
    </div>
    <div class="card">
      <h2>Analisis Jualan</h2>
      <p>Lihat Statistik</p>
      <button onclick="window.location.href='analisis-jualan.php'">Lihat Analisis</button>
    </div>
    <div class="card">
      <h2>Log Aktiviti</h2>
      <p>Semak Aktiviti</p>
      <button onclick="window.location.href='log-aktiviti.php'">Lihat Log</button>
    </div>
    <div class="card">
      <h2>Forum Staf</h2>
      <p>Ruangan Interaksi Staf</p>
      <button onclick="window.location.href='forum.php'">Forum Staf</button>
    </div>
  </div>
  
  <!-- Quote Section -->
  <div class="quote-section">
    <p>Behind every smart decision lies organized data. Manage, monitor, and act – all from one dashboard</p>
  </div>
  
  <!-- New Cards for Staff, Products, and Customers -->
  <div class="summary-dashboard">
    <div class="summary-card">
      <h2>Jumlah Staf</h2>
      <p><?php echo $staff_count; ?></p>
    </div>
    <div class="summary-card">
      <h2>Jumlah Produk</h2>
      <p><?php echo $product_count; ?></p>
    </div>
    <div class="summary-card">
      <h2>Jumlah Pelanggan</h2>
      <p><?php echo $customer_count; ?></p>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
  </div>

</body>
</html>
