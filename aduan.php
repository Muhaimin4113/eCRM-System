<?php
session_start();
require 'db_connection.php';

// Dapatkan pengguna
$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama, role FROM staff WHERE email = '$email'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['id_staff'] = $user['id_staff'];
    $_SESSION['role'] = $user['role'];
} else {
    echo "Pengguna tidak dijumpai."; exit;
}

$role = $user['role'];
$id_staff = $user['id_staff'];

// Kemaskini Status (Hanya staff boleh kemaskini)
if ($role == 'staff' && isset($_POST['kemaskini'])) {
    $stmt = $conn->prepare("UPDATE aduan SET status=? WHERE id_aduan=?");
    $stmt->bind_param("si", $_POST['status'], $_POST['id_aduan']);
    $stmt->execute();
}

// Papar semua aduan
$aduan = $conn->query("SELECT a.*, p.nama FROM aduan a JOIN pelanggan p ON a.id_pelanggan = p.id_pelanggan ORDER BY a.tarikh DESC");
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pengurusan Aduan</title>
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
            padding: 9px 40px;
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
        }

        .navbar .greeting {
            font-size: 18px;
            color: #ffffffff;
            margin-left: auto;
        }

        .navbar .logout-btn {
            padding: 8px 16px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 60px;
        }

        .container {
            max-width: 1100px;
            margin: 100px auto 40px;
            background: rgba(0, 0, 0, 0.5);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        th, td {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 10px;
            text-align: center;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
        }

        th {
            background-color: rgba(255,255,255,0.2);
        }

        select, input[type="text"], textarea {
            width: 90%;
            padding: 8px;
            border-radius: 8px;
            border: none;
            background-color: rgb(84, 84, 84);
            color: #fff;
            font-weight: bold;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            padding: 8px 14px;
            background-color: #00bcd4;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0097a7;
        }

        .form-section {
            margin-top: 40px;
        }

        .form-section form {
            max-width: 600px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Pengurusan Aduan</h1>
        <div class="greeting">Selamat Datang, <?= $user['nama'] ?> 👋</div>
        <a href="dashboard-<?= $role ?>.php" class="logout-btn">Laman Utama</a>
    </div>

    <div class="container">
        <h2>Senarai Aduan Pelanggan</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Pengadu</th>
                <th>Tajuk</th>
                <th>Kandungan</th>
                <th>Status</th>
                <th>Tarikh</th>
                <?php if ($role == 'staff') echo "<th>Tindakan</th>"; ?>
            </tr>
            <?php while($row = $aduan->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id_aduan'] ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['tajuk']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['kandungan'])) ?></td>
                <td>
                    <?php if ($role == 'staff') { ?>
                        <form method="post">
                            <input type="hidden" name="id_aduan" value="<?= $row['id_aduan'] ?>">
                            <select name="status">
                                <option <?= $row['status'] == 'Baru' ? 'selected' : '' ?>>Baru</option>
                                <option <?= $row['status'] == 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
                                <option <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                    <?php } else {
                        echo htmlspecialchars($row['status']);
                    } ?>  
                </td>
                <td><?= date('d M Y, h:i A', strtotime($row['tarikh'])) ?></td>
                <?php if ($role == 'staff') echo "<td><button class='btn' name='kemaskini'>Simpan</button></form></td>"; ?>
            </tr>
            <?php } ?>
        </table>
    </div>

    <?php if ($role == 'support') { ?>
    <div class="container form-section">
        <h2>Tambah Aduan Baharu</h2>
        <form method="post">
            <input type="text" name="tajuk" placeholder="Tajuk Aduan" required>
            <textarea name="kandungan" placeholder="Butiran Aduan..." required></textarea>
            <button type="submit" name="tambah" class="btn">Hantar Aduan</button>
        </form>
    </div>
    <?php } ?>
</body>
</html>
