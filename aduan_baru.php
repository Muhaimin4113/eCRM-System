<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Sesi tamat. Sila login semula.'); window.location='login.php';</script>";
    exit;
}

$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama FROM staff WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['id_staff'] = $user['id_staff'];
} else {
    echo "<script>alert('Pengguna tidak dijumpai.'); window.location='login.php';</script>";
    exit;
}

$pelanggan = $conn->query("SELECT id_pelanggan, nama FROM pelanggan");

// Proses form
if (isset($_POST['submit'])) {
    $id_staff = $_SESSION['id_staff'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tajuk = trim($_POST['tajuk']);
    $kandungan = trim($_POST['kandungan']);
    $tarikh = date("Y-m-d H:i:s");
    $status = 'Baru';

    $stmt = $conn->prepare("INSERT INTO aduan (id_pelanggan, tajuk, kandungan, tarikh, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_pelanggan, $tajuk, $kandungan, $tarikh, $status);
    if ($stmt->execute()) {
        echo "<script>alert('Aduan berjaya direkod.'); window.location='aduan_baru.php';</script>";
    } else {
        echo "<script>alert('Gagal rekod aduan.');</script>";
    }
}

$senarai_aduan = $conn->query("SELECT a.id_aduan, p.nama AS nama_pelanggan, a.tajuk, a.kandungan, a.tarikh, a.status
    FROM aduan a
    JOIN pelanggan p ON a.id_pelanggan = p.id_pelanggan
    ORDER BY a.tarikh DESC");
?>

<!DOCTYPE html>
<html lang="ms">
    <head>
        <meta charset="UTF-8">
        <title>Aduan Baharu</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: url('assets/dawn.jpg') no-repeat center center fixed;
                background-size: cover;
                color: #fff;
            }

            .navbar {
                background-color: rgba(0, 105, 148, 0.95);
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

            .greeting {
                font-size: 18px;
                margin-left:auto;
            }

            .logout-btn {
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

            .logout-btn:hover {
                background-color: #c0392b;
            }

            .container {
                max-width: 1000px;
                margin: 120px auto 20px;
                padding: 30px;
                background: rgba(0, 0, 0, 0.6);
                border-radius: 16px;
                backdrop-filter: blur(4px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            }

            .container-3{
                max-width: 1000px;
                margin: 50px auto 120px;
                padding: 30px;
                background: rgba(0, 0, 0, 0.6);
                border-radius: 16px;
                backdrop-filter: blur(4px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            }

            .container h2, .container-3 h2 {
                text-align: center;
                margin-bottom: 25px;
            }

            label {
                display: block;
                margin-top: 10px;
            }

            input[type="text"], select, textarea {
                width: 979px;
                padding: 10px;
                border-radius: 8px;
                border: none;
                background: rgb(100,100,100);
                color: #fff;
                margin-bottom: 15px;
                font-size: 15px;
                font-family: 'Segoe UI', sans-serif;
            }

            .btn {
                margin-top: 10px;
                background-color: #00bcd4;
                padding: 12px;
                font-size: 16px;
                width: 100%;
                border-radius: 8px;
                border: none;
                color: white;
                cursor: pointer;
            }

            .btn:hover {
                background-color: #0097a7;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background-color: rgba(255,255,255,0.08);
                margin-top: 20px;
            }

            th, td {
                border: 1px solid rgba(255,255,255,0.3);
                padding: 10px;
                text-align: center;
                color: #fff;
            }

            th {
                background-color: rgba(255,255,255,0.2);
            }

            .status-Baru {
                color: #f39c12;
                font-weight: bold;
            }

            .status-Selesai {
                color: #27ae60;
                font-weight: bold;
            }

            .status-Sedang {
                color: #e67e22;
                font-weight: bold;
            }

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
        <div class="navbar">
            <h1>Aduan Baharu</h1>
            <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
            <a href="dashboard-support.php" class="logout-btn">Laman Utama</a>
        </div>

        <div class="container">
            <h2>Hantar Aduan Baharu</h2>
            <form method="POST">
                <!-- Papar Staff Bertugas sebagai info sahaja -->
                <p><strong>Staff Bertugas:</strong> <?= $user['nama'] ?? 'Tidak dikenalpasti'; ?></p>

                <label for="id_pelanggan">Nama Pelanggan:</label>
                <select name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while ($p = $pelanggan->fetch_assoc()): ?>
                        <option value="<?= $p['id_pelanggan'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="tajuk">Tajuk Aduan:</label>
                <input type="text" name="tajuk" required>

                <label for="kandungan"> Butiran Aduan:</label>
                <textarea name="kandungan" rows="5" required></textarea>

                <button type="submit" name="submit" class="btn">Hantar Aduan</button>
            </form>
        </div>

        <div class="container-3">
            <h2>Senarai Aduan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Pelanggan</th>
                        <th>Tajuk</th>
                        <th>kandungan</th>
                        <th>Tarikh</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($senarai_aduan && $senarai_aduan->num_rows > 0): ?>
                        <?php while ($row = $senarai_aduan->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td><?= htmlspecialchars($row['tajuk']) ?></td>
                                <td><?= htmlspecialchars($row['kandungan']) ?></td>
                                <td><?= date("d/m/Y", strtotime($row['tarikh'])) ?></td>
                                <td class="status-<?= $row['status'] ?>"><?= $row['status'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Tiada aduan direkodkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
        </div>
    </body>
</html>
