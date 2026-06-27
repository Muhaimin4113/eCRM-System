<?php
session_start();

/* database connection */
require 'db_connection.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT nama FROM staff WHERE email = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Dapatkan id_staff daripada URL
if (isset($_GET['id'])) {
    $id_staff = $_GET['id'];

    // Query untuk mendapatkan maklumat staf
    $sql = "SELECT id_staff, nama, email, jawatan, status FROM staff WHERE id_staff = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_staff);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
    } else {
        echo "Staf tidak dijumpai.";
        exit;
    }
} else {
    echo "ID staf tidak disediakan.";
    exit;
}

// Update maklumat staf jika borang dihantar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jawatan = $_POST['jawatan'];
    $status = $_POST['status'];

    // Query untuk mengemas kini maklumat staf
    $sql = "UPDATE staff SET nama = ?, email = ?, jawatan = ?, status = ? WHERE id_staff = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nama, $email, $jawatan, $status, $id_staff);

    if ($stmt->execute()) {
        echo "Maklumat staf berjaya dikemaskini.";
        header("Location: pengurusan-staf.php"); // Redirect ke pengurusan-staf.php selepas berjaya
        exit();
    } else {
        echo "Ralat: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staf</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #2c5364;
            padding: 15px;
            color: white;
            text-align: center;
            font-size: 18px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 30px;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin-top: 0;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Sistem CRM</h1>
        <div>Selamat Datang, <?php echo htmlspecialchars($user['nama']); ?> 👋</div>
    </div>

    <div class="container">
        <div class="card">
            <h3>Edit Maklumat Staf</h3>
            <form action="edit-staf.php?id=<?php echo $id_staff; ?>" method="POST">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($staff['nama']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required>

                <label for="jawatan">Jawatan:</label>
                <input type="text" id="jawatan" name="jawatan" value="<?php echo htmlspecialchars($staff['jawatan']); ?>" required>

                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="Aktif" <?php echo ($staff['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?php echo ($staff['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>

                <button type="submit">Kemaskini Staf</button>
            </form>
        </div>
    </div>
    
</body>
</html>
