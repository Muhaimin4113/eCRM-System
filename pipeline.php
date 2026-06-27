<?php
session_start();
require 'db_connection.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama, role FROM staff WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0){
  $user = $result->fetch_assoc();
  $_SESSION['id_staff'] = $user['id_staff'];
  $_SESSION['role'] = $user['role'];
} else {
  echo "Pengguna tidak dijumpai.";
  exit;
}

// Kemaskini status pipeline
if (isset($_POST['kemaskini'])) {
    $stmt = $conn->prepare("UPDATE pipeline SET status_semasa=?, catatan=? WHERE id_pipeline=?");
    $stmt->bind_param("ssi", $_POST['status_semasa'], $_POST['catatan'], $_POST['id_pipeline']);
    $stmt->execute();
}

// Ambil data pipeline
$saluran = $conn->query("SELECT pipeline.id_pipeline, pelanggan.id_pelanggan, pelanggan.nama, pelanggan.email, pelanggan.no_telefon, pipeline.status_semasa, pipeline.catatan FROM pipeline JOIN pelanggan ON pipeline.id_pelanggan = pelanggan.id_pelanggan");
?>

<!DOCTYPE html>
<html lang=\"ms\">
<head>
    <meta charset=\"UTF-8\">
    <title>Jejak Status Pelanggan</title>
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
            padding: 15px 30px;
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

        select, textarea {
            padding: 6px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            background-color: rgb(64, 56, 56);
            color: rgb(255, 255, 255);
            text-shadow: 1px 1px 1px #000;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 8px;
            background-color: #00bcd4;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0097a7;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Jadual Maklumat Pipeline</h1>
        <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
        <a href="dashboard-staff.php" class="logout-btn">Laman Utama</a>
    </div>

    <div class="container">
        <h2>Jejak Status Pelanggan (Sales Pipeline)</h2>
        <table>
            <tr>
                <th>ID Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Email</th>
                <th>No. Telefon</th>
                <th>Status Semasa</th>
                <th>Catatan</th>
            </tr>
            <?php while ($row = $saluran->fetch_assoc()) { ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="id_pipeline" value="<?= $row['id_pipeline'] ?>">
                        <td><?= $row['id_pelanggan'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['no_telefon'] ?></td>
                        <td>
                            <select name="status_semasa">
                                <option value="Baru Daftar" <?= $row['status_semasa'] == 'Baru Daftar' ? 'selected' : '' ?>>Baru Daftar</option>
                                <option value="Hubungi Semula" <?= $row['status_semasa'] == 'Hubungi Semula' ? 'selected' : '' ?>>Hubungi Semula</option>
                                <option value="Dalam Perbincangan" <?= $row['status_semasa'] == 'Dalam Perbincangan' ? 'selected' : '' ?>>Dalam Perbincangan</option>
                                <option value="Tunggu Pembayaran" <?= $row['status_semasa'] == 'Tunggu Pembayaran' ? 'selected' : '' ?>>Tunggu Pembayaran</option>
                                <option value="Selesai" <?= $row['status_semasa'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Batal" <?= $row['status_semasa'] == 'Batal' ? 'selected' : '' ?>>Batal</option>
                            </select>
                        </td>
                        <td><textarea name="catatan" rows="2"><?= $row['catatan'] ?></textarea></td>
                    </form>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
