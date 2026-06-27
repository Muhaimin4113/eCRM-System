<?php
    session_start();
    require 'db_connection.php'; // Sambungan DB

    // Semak sama ada pengguna login
    if (!isset($_SESSION['email'])) {
        echo "<script>alert('Sesi tamat. Sila login semula.'); window.location='login.php';</script>";
        exit;
    }

    // Dapatkan maklumat staff berdasarkan email
    $email = $_SESSION['email'];
    $sql = "SELECT id_staff, nama, role FROM staff WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['id_staff'] = $user['id_staff'];
        $_SESSION['role'] = $user['role'];
    } else {
        echo "<script>alert('Pengguna tidak dijumpai.'); window.location='login.php';</script>";
        exit;
    }

    // Dapatkan senarai pelanggan & produk
    $pelanggan = $conn->query("SELECT id_pelanggan, nama FROM pelanggan");
    $produk    = $conn->query("SELECT id_produk, nama_produk, harga_jualan FROM produk");

    // Proses jika form dihantar
    if (isset($_POST['submit'])) {
        $id_staff     = $_SESSION['id_staff'];
        $id_pelanggan = (int) $_POST['id_pelanggan'];
        $id_produk    = (int) $_POST['id_produk'];
        $kuantiti     = (int) $_POST['kuantiti'];

        // Ambil harga dari DB (PENTING: jangan ambil dari form)
        $stmt = $conn->prepare("SELECT harga_jualan FROM produk WHERE id_produk = ?");
        $stmt->bind_param("i", $id_produk);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            echo "<script>alert('Produk tidak dijumpai.');</script>";
            exit;
        }

        $harga = (float)$row['harga_jualan'];
        $jumlah = $kuantiti * $harga;
        $tarikh = date("Y-m-d H:i:s");
        $status = 'pending';

        $stmt = $conn->prepare("INSERT INTO jualan (id_pelanggan, id_produk, kuantiti, harga, jumlah, tarikh_jualan, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiddsss", $id_pelanggan, $id_produk, $kuantiti, $harga, $jumlah, $tarikh, $status);

        if ($stmt->execute()) {
            echo "<script>alert('Jualan berjaya direkod.'); window.location='jual_baru.php';</script>";
        } else {
            echo "<script>alert('Gagal rekod jualan.');</script>";
        }
    }
    // Dapatkan data jualan untuk paparan read-only
    $jualan = $conn->query("SELECT j.id_jualan, p.nama AS nama_pelanggan, j.kuantiti, j.harga, j.jumlah, j.tarikh_jualan, j.status 
                        FROM jualan j
                        JOIN pelanggan p ON j.id_pelanggan = p.id_pelanggan
                        ORDER BY j.tarikh_jualan DESC");
?>


<!DOCTYPE html>
<html lang="ms">
    <head>
        <meta charset="UTF-8">
        <title>Rekod Jualan Baru</title>
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
                max-width: 700px;
                margin: 120px auto 70px auto;
                padding: 30px;
                background: rgba(0, 0, 0, 0.6);
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
            }

            .container-3 {
                max-width: 1100px;
                margin: 1px auto 100px auto;
                background: rgba(0, 0, 0, 0.55);
                border-radius: 16px;
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
                padding: 25px 30px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .container-3 h2 {
                text-align: center;
                margin-bottom: 20px;
                font-size: 26px;
                font-weight: 700;
                text-shadow: 1px 1px 5px rgba(0,0,0,0.8);
            }

            .container-3 table {
                width: 100%;
                border-collapse: collapse;
                background-color: rgba(255, 255, 255, 0.08);
                font-size: 15px;
            }

            .container-3 th, .container-3 td {
                border: 1px solid rgba(255,255,255,0.3);
                padding: 12px 10px;
                text-align: center;
                font-weight: 600;
                text-shadow: 1px 1px 2px #000;
                vertical-align: middle;
            }

            .container-3 th {
                background-color: rgba(255,255,255,0.2);
                font-size: 16px;
            }

            /* Zebra striping */
            .container-3 tr:nth-child(even) {
                background-color: rgba(255,255,255,0.05);
            }

            /* Tooltip warna status */
            .status-pending {
                color: #f39c12; /* amber */
                font-weight: 700;
                background-color: rgba(243, 156, 18, 0.15);
            }

            .status-completed {
                color: #27ae60; /* green */
                font-weight: 1500;
                background-color: rgba(243, 156, 18, 0.15);
            }

            .status-cancelled {
                color: #e74c3c; /* red */
                font-weight: 700;
                background-color: rgba(243, 156, 18, 0.15);
            }

            h2 {
                text-align: center;
                margin-bottom: 25px;
            }

            label {
                display: block;
            }

            select, input {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border-radius: 8px;
                border: none;
                font-weight: bold;
                background: rgb(100,100,100);
                color: #fff;
            }

            select, textarea {
                padding: 6px;
                border-radius: 6px;
                border: none;
                font-size: 14px;
                background-color: rgb(100, 100, 100);
                color: rgb(255, 255, 255);
                text-shadow: 1px 1px 1px #000;
            }

            .input-kuantiti {
                width: 100%;
                max-width: 670px; /* sama panjang macam dropdown */
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
            <h1>Rekod Jualan Baharu</h1>
            <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
            <a href="dashboard-support.php" class="logout-btn">Laman Utama</a>
        </div>

        <div class="container">
            <h2>Rekod Jualan Baharu</h2>
            <form method="POST">
                <label for="id_pelanggan">Nama Pelanggan:</label>
                <select name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while ($p = $pelanggan->fetch_assoc()): ?>
                        <option value="<?= $p['id_pelanggan'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="id_produk">Nama Produk:</label>
                <select name="id_produk" id="produkSelect" required onchange="updateHarga()">
                    <option value="">-- Pilih Produk --</option>
                    <?php while ($pr = $produk->fetch_assoc()): ?>
                        <option value="<?= $pr['id_produk'] ?>" data-harga="<?= $pr['harga_jualan'] ?>"><?= htmlspecialchars($pr['nama_produk']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label class="text" for="kuantiti">Kuantiti:</label>
                <input type="number" name="kuantiti" min="1" required class="input-kuantiti">

                <button type="submit" name="submit" class="btn">Hantar Jualan</button>
            </form>
        </div>

        <div class="container-3">
            <h2>Rekod Jualan Baharu</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Jualan</th>
                        <th>Nama Pelanggan</th>
                        <th>Kuantiti</th>
                        <th>Harga</th>
                        <th>Jumlah (RM)</th>
                        <th>Tarikh Jualan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jualan && $jualan->num_rows > 0): ?>
                        <?php while ($row = $jualan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_jualan'] ?></td>
                            <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                            <td><?= $row['kuantiti'] ?></td>
                            <td><?= number_format($row['harga'], 2) ?></td>
                            <td><?= number_format($row['jumlah'], 2) ?></td>
                            <td><?= date("d/m/Y", strtotime($row['tarikh_jualan'])) ?></td>
                            <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">Tiada rekod jualan ditemui.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
        </div>
    </body>
</html>
