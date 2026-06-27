<?php
session_start();
require 'db_connection.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama, role FROM staff WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0){
  $user = $result->fetch_assoc();

  // Simpan ke sesi jika belum ada
  $_SESSION['id_staff'] = $user['id_staff'];
  $_SESSION['role'] = $user['role'];

} else {
  // Kalau tiada pengguna, redirect ke login page
  echo "Pengguna tidak dijumpai.";
  exit;
}

$pelanggan = $conn->query("SELECT * FROM pelanggan");

?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Maklumat Pelanggan</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('assets/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .navbar {
            background-color: #1a237e;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar h1 {
            font-size: 24px;
            color: #fff;
            margin: 0;
        }

        .navbar .greeting {
            font-size: 18px;
            color: #ffffffff;
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
            margin-right: 60px;
        }

        .navbar .logout-btn:hover {
            background-color: #c0392b;
        }

        .container {
            max-width: 1100px;
            margin: 100px auto 50px auto;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 16px;
            backdrop-filter: blur(1px);
            -webkit-backdrop-filter: blur(1px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
            padding: 30px;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.9);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.08);
            font-size: 16px;
        }

        th, td {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 10px;
            text-align: center;
            color: #fff;
            font-weight: 600;
            text-shadow: 1px 1px 2px #000;
        }

        th {
            background-color: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Maklumat Pelanggan</h1>
        <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
        <a href="dashboard-staff.php" class="logout-btn">Laman Utama</a>
    </div>

    <div class="container">
        <h2>Maklumat Pelanggan</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Penuh</th>
                <th>Email</th>
                <th>No. Telefon</th>
                <th>Alamat</th>
            </tr>
            <?php while ($row = $pelanggan->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id_pelanggan'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['no_telefon'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
