<?php  
session_start();
include 'db_connection.php'; // connection DB

// Dapatkan nama user
$email = $_SESSION['email'] ?? '';
$sql = "SELECT nama FROM staff WHERE email = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Submit maklumat pelanggan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email_pelanggan = $_POST['email'];
    $no_telefon = $_POST['no_telefon'];
    $alamat = $_POST['alamat'];
    $status = $_POST['status'];
    $tarikh_daftar = date("Y-m-d H:i:s"); // auto sistem

    $stmt = $conn->prepare("INSERT INTO pelanggan (nama, email, no_telefon, alamat, tarikh_daftar) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $email_pelanggan, $no_telefon, $alamat, $tarikh_daftar);

    if ($stmt->execute()) {
        echo "<script>alert('Pelanggan berjaya ditambah!'); window.location.href='tambah_pelanggan.php';</script>";
    } else {
        echo "<script>alert('Ralat menambah pelanggan.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Tambah Pelanggan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: url('assets/dawn.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    header {
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

    header h1 {
        margin: 0;
        color: #fff;
        font-size: 24px;
    }

    header .greeting {
        font-size: 18px;
        color: rgb(255, 255, 255);
        margin-left: auto;
    }

    header .logout-btn {
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

    .container {
        margin-top: 20px;
        width: 800px;
        margin: 120px auto 50px auto;
        background-color: rgba(0, 0, 0, 0.4);
        border-radius: 16px;
        padding: 30px;
        backdrop-filter: blur(5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    h2 {
        text-align: center;
        color: #fff;
        margin-bottom: 30px;
    }

    form label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
    }

    form input, form textarea, form select {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
        box-sizing: border-box;
        font-size: 16px;
    }

    form input[type="submit"] {
        background-color: #0288d1;
        color: white;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    form input[type="submit"]:hover {
      background-color: #01579b;
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

    .footer {
      background-color: #2c5364;
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
  <h1>Tambah Pelanggan Baharu</h1>
  <div>
    Selamat Datang, <?php echo htmlspecialchars($user['nama']); ?> 👋
    <a href="dashboard-support.php" class="logout">Laman Utama</a>
  </div>
</header>

<div class="container">
  <h2>Borang Tambah Pelanggan Baru</h2>
  <form method="post" action="">
    <label for="nama">Nama Pelanggan:</label>
    <input type="text" id="nama" name="nama" required>

    <label for="email">Emel:</label>
    <input type="email" id="email" name="email" required>

    <label for="no_telefon">No. Telefon:</label>
    <input type="text" id="no_telefon" name="no_telefon" required>

    <label for="alamat">Alamat:</label>
    <textarea id="alamat" name="alamat" rows="3" required></textarea>

    <input type="submit" value="Hantar">
  </form>
</div>

<div class="footer">
    <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
</div>

</body>
</html>
