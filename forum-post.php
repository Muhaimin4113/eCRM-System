<?php
session_start();
require 'db_connection.php'; //Sambungan ke DB

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
}

if (!isset($_GET['id'])) {
    echo "Post tidak dijumpai.";
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

$id_post = intval($_GET['id']);

// Ambil post
$post_query = "SELECT forum_post.*, staff.nama FROM forum_post JOIN staff ON forum_post.id_staff = staff.id_staff WHERE id_post = $id_post";
$post_result = mysqli_query($conn, $post_query);

if (!$post_result || mysqli_num_rows($post_result) == 0) {
    echo "Post tidak dijumpai.";
    exit;
}

$post = mysqli_fetch_assoc($post_result);

// Ambil komen
$komen_query = "SELECT k.*, s.nama FROM forum_komen k JOIN staff s ON k.id_staff = s.id_staff WHERE k.id_post = $id_post ORDER BY k.tarikh ASC";
$komen_result = mysqli_query($conn, $komen_query);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Forum</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS anda -->
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('<?= $background ?>') no-repeat center center fixed;
            background-size: cover;
        }
        .navbar {
            background-color: #2c5364;
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
            margin-right: 60px;
        }

        .navbar .logout-btn:hover {
            background-color: #c0392b;
        }
        .forum-container {
            max-width: 800px;
            margin: 40px auto;
            margin-top: 100px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .forum-post {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
        }
        .forum-post h2 {
            margin-top: 0;
            color: #333;
        }
        .forum-post .meta {
            font-size: 0.9em;
            color: #777;
            margin-bottom: 10px;
        }
        .forum-post p {
            color: #444;
            white-space: pre-line;
        }
        .komen-section {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        }
        .komen {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .komen:last-child {
            border-bottom: none;
        }
        .komen .meta {
            font-size: 0.85em;
            color: #888;
        }
        form textarea {
            width: 97%;
            height: 100px;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: none; /* Halang resize manual */
            overflow-y: auto; /* Scroll bila kandungan panjang */
        }
        form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>Sistem CRM</h1>
        <div class="greeting">Selamat Datang, <?= htmlspecialchars($user['nama']) ?> 👋</div>
        <a href="<?= $dashboard ?>" class="logout-btn">Laman Utama</a>
    </div>

    <div class="forum-container">
        <div class="forum-post">
            <h2><?= htmlspecialchars($post['tajuk']) ?></h2>
            <div class="meta">Ditulis oleh <?= htmlspecialchars($post['nama']) ?> pada <?= date('d M Y, h:i A', strtotime($post['tarikh'])) ?></div>
            <p><?= nl2br(htmlspecialchars($post['kandungan'])) ?></p>
        </div>

        <div class="komen-section">
            <h3>Komen</h3>
            <?php while ($komen = mysqli_fetch_assoc($komen_result)) { ?>
                <div class="komen">
                    <div class="meta"><?= htmlspecialchars($komen['nama']) ?> - <?= date('d M Y, h:i A', strtotime($komen['tarikh'])) ?></div>
                    <div><?= nl2br(htmlspecialchars($komen['kandungan'])) ?></div>
                </div>
            <?php } ?>

            <form method="post" action="forum-tambah-komen.php">
                <input type="hidden" name="id_komen" value="<?= $id_post ?>">
                <textarea name="kandungan" placeholder="Tulis komen anda..." required></textarea>
                <button type="submit">Hantar Komen</button>
            </form>
        </div>
    </div>
</body>