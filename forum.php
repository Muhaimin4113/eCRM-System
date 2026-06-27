<?php
session_start();
require 'db_connection.php';
require 'log-helper.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama, role FROM staff WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0){
    $user = $result->fetch_assoc();
    $_SESSION['id_staff'] = $user['id_staff'];
    $_SESSION['role'] = $user['role'];

    logAktiviti($conn, 'Mengakses Forum Staf');
} else {
    echo "Pengguna tidak dijumpai.";
    exit;
}

// Tetapkan background dan dashboard ikut role
$role = $_SESSION['role'];
switch ($role) {
    case 'admin':
        $background = 'assets/mecha.jpg';
        $dashboard = 'dashboard-admin.php';
        break;
    case 'staff':
        $background = 'assets/bg.jpg';
        $dashboard = 'dashboard-staff.php';
        break;
    case 'support':
        $background = 'assets/dawn.jpg';
        $dashboard = 'dashboard-support.php';
        break;
    default:
        $background = 'assets/mecha.jpg';
        $dashboard = 'dashboard-admin.php';
}

$sql = "SELECT p.*, s.nama FROM forum_post p JOIN staff s ON p.id_staff = s.id_staff ORDER BY p.tarikh DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Forum Staff</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('<?= $background ?>') no-repeat center center fixed;
            background-size: cover;
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
            margin-left: auto;
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

        .forum-container {
            margin: 80px auto 60px;
            width: 1000px;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 20px;
            border-radius: 12px;
        }

        .post {
            background: #fff;
            margin: 0 auto 20px;
            width: 890px;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .post .nama {
            font-weight: bold;
            color: #333;
        }

        .post .tarikh {
            font-size: 12px;
            color: #777;
        }

        .post .tajuk {
            font-size: 18px;
            margin: 10px 0;
            color: #007bff;
        }

        .post .isi {
            font-size: 14px;
            color: #444;
        }

        .post .butang {
            margin-top: 15px;
        }

        .post .butang button {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            margin-right: 15px;
        }

        .tambah-post {
            text-align: right;
            margin-bottom: 20px;
        }

        .tambah-post a {
            background: rgb(113, 113, 113);
            color: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Forum Staff</h1>
        <div class="greeting">Selamat Datang, <?= htmlspecialchars($user['nama']) ?> 👋</div>
        <a href="<?= $dashboard ?>" class="logout-btn">Laman Utama</a>
    </div>

    <div class="forum-container">
        <div class="tambah-post">
            <a href="forum-tambah-post.php">+ Buat Post Baru</a>
        </div>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="post">
            <div class="nama"><?= htmlspecialchars($row['nama']) ?></div>
            <div class="tarikh"><?= date('d M Y, h:i A', strtotime($row['tarikh'])) ?></div>
            <div class="tajuk"><?= htmlspecialchars($row['tajuk']) ?></div>
            <div class="isi"><?= nl2br(htmlspecialchars(substr($row['kandungan'], 0, 150))) ?>...</div>
            <div class="butang">
                <button onclick="window.location.href='forum-post.php?id=<?= $row['id_post'] ?>'">Baca Selanjutnya</button>
                <button onclick="copyLink(<?= $row['id_post'] ?>)">Kongsi</button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <script>
        function copyLink(id) {
            const currentPath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
            const link = `${window.location.origin}${currentPath}/forum-post.php?id=${id}`;
            navigator.clipboard.writeText(link)
                .then(() => alert("Pautan berjaya disalin ke papan klip!"))
                .catch(() => alert("Gagal salin pautan."));
        }
    </script>
</body>
</html>
