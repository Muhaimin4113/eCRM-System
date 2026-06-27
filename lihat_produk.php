<?php
session_start();
require 'db_connection.php';

// Semak jika login
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Sila login dahulu.'); window.location='login.php';</script>";
    exit;
}

// Dapatkan info staff
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id_staff, nama FROM staff WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Pengguna tidak dijumpai.'); window.location='login.php';</script>";
    exit;
}

$user = $result->fetch_assoc();

// Dapatkan semua produk
$produk = $conn->query("SELECT * FROM produk ORDER BY id_produk ASC");
?>

<!DOCTYPE html>
<html lang="ms">
    <head>
        <meta charset="UTF-8">
        <title>Lihat Produk</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: url('assets/dawn.jpg') no-repeat center center fixed;
                background-size: cover;
                color: #fff;
            }

            .navbar {
                background-color: rgba(0, 105, 148, 0.95);
                padding: 15px 30px;
                left: 0;
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
                color:rgb(255, 255, 255);
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
                max-width: 1000px;
                margin: 100px auto 100px auto;
                padding: 30px;
                background: rgba(0, 0, 0, 0.5);
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
            }

            h2 {
                text-align: center;
                margin-bottom: 25px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background-color: rgba(255, 255, 255, 0.05);
            }

            th, td {
                padding: 12px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                text-align: center;
                color: #fff;
                font-weight: 500;
            }

            th {
                background-color: rgba(255, 255, 255, 0.1);
                font-size: 16px;
            }

            tr:nth-child(even) {
                background-color: rgba(255, 255, 255, 0.03);
            }

            .status-aktif {
                color: #2ecc71;
                font-weight: bold;
            }

            .status-tidak {
                color: #e74c3c;
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
        <div class="navbar">
            <h1>Jadual Maklumat Produk</h1>
            <div class="greeting">Selamat Datang, <?php echo htmlspecialchars($user['nama']); ?> 👋</div>
            <a href="dashboard-support.php" class="logout-btn">Laman Utama</a>
        </div>

        <div class="container">
            <h2>Senarai Produk</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Jualan (RM)</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $produk->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_produk'] ?></td>
                            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td><?= number_format($row['harga_jualan'], 2) ?></td>
                            <!-- <td><?= number_format($row['harga_modal'], 2) ?></td> // optional -->
                            <td><?= $row['stok'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
        </div>
    </body>
</html>