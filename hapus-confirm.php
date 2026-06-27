<?php
session_start();

/* Panggil sambungan pangkalan data */
require 'db_connection.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Jika tidak, redirect ke halaman login
    exit();
}

// Dapatkan ID staf dari URL
if (isset($_GET['id'])) {
    $id_staff = $_GET['id'];

    // Query untuk mendapatkan maklumat staf berdasarkan ID
    $sql = "SELECT nama FROM staff WHERE id_staff = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_staff);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $staff = $result->fetch_assoc();
        } else {
            echo "Staf tidak dijumpai.";
            exit();
        }
    } else {
        echo "Persediaan query gagal: " . $conn->error;
        exit();
    }
} else {
    echo "ID staf tidak dijumpai.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengurusan Staf - Sahkan Padam</title>
</head>
<body>

    <h3>Adakah anda pasti mahu memadam staf berikut?</h3>
    <p>Nama: <?php echo $staff['nama']; ?></p>
    <form action="hapus-staf.php" method="GET">
        <input type="hidden" name="id" value="<?php echo $id_staff; ?>">
        <button type="submit" name="confirm" value="yes">Ya, Padam</button>
        <a href="pengurusan-staf.php">Batal</a>
    </form>

</body>
</html>
