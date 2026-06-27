<?php
    session_start();
    include 'db_connection.php';

    $email = $_SESSION['email'] ?? '';
    $sql = "SELECT nama FROM staff WHERE email = '$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Fetch semua pelanggan
    $pelanggan_list = $conn->query("SELECT id_pelanggan, nama FROM pelanggan ORDER BY nama");

    // Bila form dihantar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
        $id_pelanggan = $_POST['id_pelanggan'];
        $nama = $_POST['nama'];
        $email_pelanggan = $_POST['email'];
        $no_telefon = $_POST['no_telefon'];
        $alamat = $_POST['alamat'];

        $stmt = $conn->prepare("UPDATE pelanggan SET nama=?, email=?, no_telefon=?, alamat=? WHERE id_pelanggan=?");
        $stmt->bind_param("ssssi", $nama, $email_pelanggan, $no_telefon, $alamat, $id_pelanggan);

        if ($stmt->execute()) {
            echo "<script>alert('Maklumat pelanggan berjaya dikemaskini!'); window.location.href='edit_pelanggan.php';</script>";
        } else {
            echo "<script>alert('Gagal kemaskini.');</script>";
        }
        $stmt->close();
    }

    // Fetch maklumat pelanggan terpilih
    $selected_pelanggan = null;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $selected_pelanggan = $stmt->get_result()->fetch_assoc();
    }
?>

<!DOCTYPE html>
<html lang="ms">
    <head>
        <meta charset="UTF-8">
        <title>Edit Pelanggan</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: url('assets/dawn.jpg') no-repeat center center fixed;
                background-size: cover;
                margin: 0;
                padding: 0;
                color: #fff;
                display: flex;
                flex-direction: column;
                height: 140vh;
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

            header .logout {
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

            .logout:hover {
                background-color: #d32f2f;
            }

            .container {
                margin: 100px auto;
                width: 800px;
                background-color: rgba(0, 0, 0, 0.4);
                border-radius: 16px;
                padding: 60px;
                backdrop-filter: blur(5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            }

            h2 {
                text-align: center;
                margin-bottom: 30px;
            }

            label {
                display: block;
                margin-bottom: 6px;
                font-weight: bold;
            }

            input, textarea, select {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 8px;
                border: none;
                font-size: 16px;
                box-sizing: border-box;
            }

            input[type="submit"] {
                background-color: #0288d1;
                color: white;
                cursor: pointer;
                font-weight: bold;
            }

            input[type="submit"]:hover {
                background-color: #01579b;
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
        <header>
            <h1>Kemaskini Maklumat Pelanggan</h1>
            <div>
                Selamat Datang, <?php echo htmlspecialchars($user['nama']); ?> 👋
                <a href="dashboard-support.php" class="logout">Laman Utama</a>
            </div>
        </header>

        <div class="container">
            <h2>Pilih Pelanggan Untuk Edit</h2>

            <form method="get" action="">
                <label for="id">Nama Pelanggan:</label>
                <select name="id" id="id" onchange="this.form.submit()" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while ($row = $pelanggan_list->fetch_assoc()) { ?>
                    <option value="<?= $row['id_pelanggan'] ?>" <?= (isset($_GET['id']) && $_GET['id'] == $row['id_pelanggan']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nama']) ?>
                    </option>
                    <?php } ?>
                </select>
            </form>

            <?php if ($selected_pelanggan): ?>
            <hr>
            <h2>Edit Maklumat Pelanggan</h2>
            <form method="post" action="">
                <input type="hidden" name="id_pelanggan" value="<?= $selected_pelanggan['id_pelanggan'] ?>">

                <label for="nama">Nama:</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($selected_pelanggan['nama']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($selected_pelanggan['email']) ?>" required>

                <label for="no_telefon">No. Telefon:</label>
                <input type="text" name="no_telefon" value="<?= htmlspecialchars($selected_pelanggan['no_telefon']) ?>" required>

                <label for="alamat">Alamat:</label>
                <textarea name="alamat" rows="3" required><?= htmlspecialchars($selected_pelanggan['alamat']) ?></textarea>

                <input type="submit" name="update" value="Simpan Perubahan">
            </form>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>&copy; 2025 Sistem CRM. Semua Hak Cipta Terpelihara.</p>
        </div>
    </body>
</html>
