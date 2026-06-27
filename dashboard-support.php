<?php  
session_start();
include 'db_connection.php';

$email = $_SESSION['email'] ?? '';
$sql = "SELECT nama FROM staff WHERE email = '$email'";
$sql2 = "SELECT COUNT(*) AS bil_aduan FROM aduan WHERE status = 'Baru'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$result2 = $conn->query($sql2);
$aduan = $result2 ? $result2->fetch_assoc() : ['bil_aduan' => 0];
?>

<!DOCTYPE html>
<html lang="ms">
  <head>
    <meta charset="UTF-8">
    <title>Dashboard Support</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
      body {
        font-family: 'Segoe UI', sans-serif;
        background: url('assets/dawn.jpg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
        color: #fff;
        display: flex;
        flex-direction: column;
        height: 100vh;
      }

      header {
        background-color: rgba(0, 105, 148, 0.95);
        color: white;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
      }

      header h1 {
        margin: 0;
        color: #fff;
        font-size: 24px;
      }

      header .greeting {
        font-size: 18px;
        color:rgb(255, 255, 255);
        margin-left:auto;
      }
      
      .container {
        width: 1200px;
        margin: 100px auto 50px auto; /* center & jarak dari header */
        background-color: rgba(0, 0, 0, 0.4);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        gap: 20px;
        backdrop-filter: blur(4px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      }


      .sidebar {
        width: 300px;
        background-color: rgba(255, 255, 255, 0.3);
        padding: 20px;
        box-sizing: border-box;
        height: 100vh;
        overflow-y: auto;
      }

      .main-content {
        width: 80%;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
        height: 100vh;
        overflow-y: auto;
      }

      .sidebar .card {
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 14px;
        padding: 10px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        margin-bottom: 10px;
        text-align: left;
        color: #003366;
        cursor: pointer;
      }

      .sidebar .card:hover {
        transform: translateX(-10px);
      }

      .sidebar .card i {
        font-size: 20px;
        margin-right: 10px;
        color: #0288d1;
      }

      .logout {
        background-color: #ef5350;
        color: white;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        margin-right: 50px;
      }

      .logout:hover {
        background-color: #d32f2f;
      }

      .card {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 14px;
        padding: 10px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        text-align: center;
        transition: transform 0.3s ease;
        color: #003366;
      }

      .card i {
        font-size: 36px;
        margin-bottom: 15px;
        color: #0288d1;
      }

      .card a {
        text-decoration: none;
        color: #0288d1;
        font-weight: bold;
      }

      /* Footer */
      .footer {
        background-color: rgba(0, 105, 148, 0.95);
        text-align: center;
        padding: 10px;
        position: fixed;
        width: 100%;
        bottom: 0;
      }
    </style>
  </head>
  
  <body>
    <header>
      <h1>Dashboard Support</h1>
      <div class="greeting"> Selamat Datang, <?php echo $user['nama']; ?> 👋
        <a href="logout.php" class="logout">Log Keluar</a>
      </div>
    </header>

    <div class="container">
      <!-- Sidebar Menu (Left Section) -->
      <div class="sidebar">
        <div class="card">
          <i class="fas fa-user-plus"></i>
          <a href="tambah_pelanggan.php">Tambah Pelanggan</a>
        </div>
        <div class="card">
          <i class="fas fa-user-edit"></i>
          <a href="edit_pelanggan.php">Edit Maklumat Pelanggan</a>
        </div>
        <div class="card">
          <i class="fas fa-stream"></i>
          <a href="pipeline-edit.php">Tambah dan Kemaskini Pipeline</a>
        </div>
        <div class="card">
          <i class="fas fa-cash-register"></i>
          <a href="jual_baru.php">Masukkan Jualan</a>
        </div>
        <div class="card">
          <i class="fas fa-box-open"></i>
          <a href="lihat_produk.php">Lihat Produk</a>
        </div>
        <div class="card">
          <i class="fas fa-comment-alt"></i>
          <a href="aduan_baru.php">Urusan Aduan</a>
        </div>
        <div class="card">
          <i class="fa-brands fa-rocketchat"></i>
          <a href="forum.php">Forum Staff</a>
        </div>
      </div>

      <!-- Main Content Area (Right Section) -->
      <div class="main-content">
        <!-- Aktiviti Hari Ini, Calendar, Time -->
        <div class="card">
          <h3>"Hari Baru, Ombak Baru. Kita Hadapi Bersama"</h3>
          <p id="current-time"></p>
          <div id="calendar"></div>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
          <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />
          <script>
            // JavaScript untuk memaparkan jam semasa
            function updateTime() {
              var now = new Date();
              var hours = now.getHours();
              var minutes = now.getMinutes();
              var seconds = now.getSeconds();

              var ampm = hours >= 12 ? 'PM' : 'AM';
              hours = hours % 12;
              hours = hours ? hours : 12; // kalau jam 0, tukar ke 12

              var day = now.getDate();
              var month = now.toLocaleString('default', { month: 'long' }); // contoh: April
              var year = now.getFullYear();

              var timeString = hours + ':' + 
                      (minutes < 10 ? '0' + minutes : minutes) + ':' + 
                      (seconds < 10 ? '0' + seconds : seconds) + ' ' + ampm;
              var dateString = day + ' ' + month + ' ' + year;

              document.getElementById('current-time').innerText = timeString + ' | ' + dateString;
            }
            setInterval(updateTime, 1000);


            // Inisialisasi calendar
            $(document).ready(function() {
              $('#calendar').fullCalendar({
                events: [
                  { title: 'Masukkan Aduan Baru', start: '2025-04-19T09:00:00' },
                  { title: 'Follow-up Pelanggan', start: '2025-04-19T14:00:00' }
                ]
              });
            });
          </script>
        </div>
        
        <br> <br>

        <!-- Bahagian Aduan -->
        <div class="card">
          <h3>Aduan Pelanggan</h3>
          <p>Jumlah Aduan Tertunggak: <?php echo htmlspecialchars($aduan['bil_aduan'] ?? '0'); ?></p>
          <a href="semak_aduan.php">Semak Aduan</a>
        </div>
      </div>
    </div>

    <br><br><br><br>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
    </div>
  </body>
</html>
