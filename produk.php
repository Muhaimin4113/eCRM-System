<?php
    session_start();
    require 'db_connection.php'; // fail sambungan DB

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

    // Tambah produk
    if (isset($_POST['tambah'])) {
        $stmt = $conn->prepare("INSERT INTO produk (nama_produk, kategori, harga_jualan, harga_modal, stok, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddis", $_POST['nama_produk'], $_POST['kategori'], $_POST['harga_jualan'], $_POST['harga_modal'], $_POST['stok'], $_POST['status']);
        $stmt->execute();
    }

    // Padam produk
    if (isset($_GET['padam'])) {
        $conn->query("DELETE FROM produk WHERE id_produk = " . $_GET['padam']);
    }

    // Kemaskini produk
    if (isset($_POST['kemaskini'])) {
        $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, kategori=?, harga_jualan=?, harga_modal=?, stok=?, status=? WHERE id_produk=?");
        $stmt->bind_param("ssddisi", $_POST['nama_produk'], $_POST['kategori'], $_POST['harga_jualan'], $_POST['harga_modal'], $_POST['stok'], $_POST['status'], $_POST['id_produk']);
        $stmt->execute();
    }

    // Kemaskini jualan
    if (isset($_POST['kemaskini2'])) {
        $stmt = $conn->prepare("UPDATE jualan SET status=? WHERE id_jualan=?");
        $stmt->bind_param("si", $_POST['status'], $_POST['id_jualan']);
        if ($stmt->execute()) {
            echo "<script>console.log('Status berjaya dikemaskini.')</script>";
        } else {
            echo "<script>console.error('Gagal kemaskini: " . $stmt->error . "')</script>";
        }
    }


    $produk = $conn->query("SELECT * FROM produk");
    $jualan = $conn->query("SELECT j.id_jualan, p.nama AS nama_pelanggan, j.kuantiti, j.jumlah, j.tarikh_jualan, j.status FROM jualan j
                            JOIN pelanggan p ON j.id_pelanggan = p.id_pelanggan ORDER BY j.tarikh_jualan ASC");
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pengurusan Produk</title>
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
            margin: 50px auto;
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

        .container table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.08);
            margin-bottom: 30px;
            font-size: 16px;
        }

        .container th, td {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 10px;
            text-align: center;
            color: #fff;
            font-weight: 600;
            text-shadow: 1px 1px 2px #000;
        }

        .container th {
            background-color: rgba(255,255,255,0.2);
            font-size: 17px;
        }

        .container input, select {
            width: 90%; /* bagi full guna dalam cell, biar ikut parent <td> */
            max-width: 100px; /* had maksimum supaya tak pecah container */
            min-width: 50px; /* supaya tak terlalu kecik */
            padding: 8px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255,255,255,0.2);
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            text-shadow: 1px 1px 1px #000;
            white-space: normal; /* benarkan text wrap jika panjang */
            word-break: break-word; /* pecahkan perkataan kalau terlalu panjang */
        }


        .container input::placeholder {
            color: #ccc;
        }

        .container .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background-color: #00bcd4;
            color: white;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 1px 1px 4px #000;
            transition: 0.3s;
        }

        .container .btn:hover {
            background-color: #0097a7;
        }

        .container .danger {
            background-color: #e74c3c;
        }

        .container .danger:hover {
            background-color:rgb(157, 42, 33);
        }

        .container .form-section {
            padding: 30px 0;
            text-align: center;
        }

        .container .form-section form {
            width: 100%;
            max-width: 500px;
            margin: 0 auto; /* center form horizontally */
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .container .form-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
            text-shadow: 1px 1px 5px #000;
        }

        .container .form-section input,
        .form-section select {
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            background-color: rgba(255,255,255,0.25);
            color: #fff;
            font-weight: 600;
            text-shadow: 1px 1px 1px #000;
            border: none;
        }

        .container .form-section input::placeholder {
            color: #ddd;
            font-weight: normal;
        }

        .container .form-section .btn {
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
        }

        .container-2 {
            max-width: 1100px;
            margin: 50px auto;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 16px;
            backdrop-filter: blur(1px);
            -webkit-backdrop-filter: blur(1px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
            padding: 30px;
        }

        .container-2 h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 26px;
            text-shadow: 1px 1px 4px #000;
        }

        .container-2 form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }

        .container-2 input,
        .container-2 select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            border: none;
            background-color: rgba(255,255,255,0.2);
            color: #fff;
            font-weight: 600;
            text-shadow: 1px 1px 1px #000;
            box-sizing: border-box;
        }

        .container-2 input::placeholder {
            color: #ccc;
        }

        .container-2 .btn {
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            background-color: #00bcd4;
            border: none;
            cursor: pointer;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            transition: 0.3s;
        }

        .container-2 .btn:hover {
            background-color: #0097a7;
        }

        select, textarea {
            padding: 6px;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            background-color: rgb(64, 56, 56);
            color: rgb(255, 255, 255);
            text-shadow: 1px 1px 1px #000;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Pengurusan Produk</h1>
        <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
        <a href="dashboard-staff.php" class="logout-btn">Laman Utama</a>
    </div>

    <div class="container">
        <h2>Pengurusan Produk</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga Jualan</th>
                <th>Harga Modal</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
            <?php while ($row = $produk->fetch_assoc()) { ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>">
                        <td><?= $row['id_produk'] ?></td>
                        <td><input type="text" name="nama_produk" value="<?= $row['nama_produk'] ?>"></td>
                        <td><input type="text" name="kategori" value="<?= $row['kategori'] ?>"></td>
                        <td><input type="number" step="1.00" name="harga_jualan" value="<?= $row['harga_jualan'] ?>"></td>
                        <td><input type="number" step="1.00" name="harga_modal" value="<?= $row['harga_modal'] ?>"></td>
                        <td><input type="number" name="stok" value="<?= $row['stok'] ?>"></td>
                        <td>
                            <select name="status">
                                <option value="aktif" <?= $row['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="tidak aktif" <?= $row['status'] == 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="kemaskini" class="btn">Simpan</button>
                            <a href="?padam=<?= $row['id_produk'] ?>" onclick="return confirm('Padam produk ini?')" class="btn danger">Padam</a>
                        </td>
                    </form>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="container-2">
        <h2>Tambah Produk Baharu</h2>
        <form method="POST">
            <input type="text" name="nama_produk" placeholder="Nama Produk" required>
            <input type="text" name="kategori" placeholder="Kategori" required>
            <input type="number" step="0.01" name="harga_jualan" placeholder="Harga Jualan (RM)" required>
            <input type="number" step="0.01" name="harga_modal" placeholder="Harga Modal (RM)" required>
            <input type="number" name="stok" placeholder="Stok" required>
            <select name="status" required>
                <option value="aktif">Aktif</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
            <button type="submit" name="tambah" class="btn">Tambah Produk</button>
        </form>
    </div>

    <div class="container">
        <h2>Pengurusan Jualan</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Kuantiti</th>
                <th>Jumlah (RM)</th>
                <th>Tarikh Jualan</th>
                <th>Status</th>
            </tr>

            <?php while ($row = $jualan->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_jualan'] ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                    <td><?= $row['kuantiti'] ?></td>
                    <td><?= number_format($row['jumlah'], 2) ?></td>
                    <td><?= date("d/m/Y", strtotime($row['tarikh_jualan'])) ?></td>
                    <td>
                        <form method="post" style="display: flex; gap: 4px;">
                            <input type="hidden" name="id_jualan" value="<?= $row['id_jualan'] ?>">
                            <select name="status">
                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="kemaskini2" class="button">Simpan</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
