<?php 
session_start();

date_default_timezone_set('Asia/Kuala_Lumpur');

/* database connection */
require 'db_connection.php';
require 'log-helper.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT id_staff, nama, role FROM staff WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0){
    $user = $result->fetch_assoc();
  
    // Simpan ke sesi jika belum ada
    $_SESSION['id_staff'] = $user['id_staff'];
    $_SESSION['role'] = $user['role'];
  
    // Log aktiviti ini
    logAktiviti($conn, 'Mengakses Pengurusan Staf');
  } else {
    // Kalau tiada pengguna, redirect ke login page
    echo "Pengguna tidak dijumpai.";
    exit;
  }
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengurusan Staf</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        function confirmDelete() {
            // Show a confirmation popup
            return confirm('⚠️ Adakah anda pasti mahu memadamkan staf ini? Tindakan ini tidak boleh dibatalkan.');
        }
    </script>

    <!-- Include some basic CSS for styling -->
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            background: url('assets/mecha.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: black;
        }

        /* Navbar Styling */
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
        }

        .navbar .logout-btn:hover {
            background-color: #c0392b;
        }

        /* Container to center content */
        .container {
            width: 80%;
            max-width: 1200px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 500px;
        }

        /* Header styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .header .btn-back {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .header .btn-back:hover {
            background-color: #0056b3;
        }

        /* Button styling */
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Card styling */
        .card {
            background: rgba(255, 255, 255, 0.7);
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin-top: 0;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 2px solid rgb(0, 0, 0);
        }

        table th {
            background:rgb(2, 0, 143);
            color: white;
        }

        /* Button styling */
        button {
            background-color: rgb(2, 0, 143);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color:rgb(4, 0, 255);
        }

        /* Form Styling */
        form label {
            display: block;
            margin: 10px 0 5px;
        }

        form input, form select {
            width: 98%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Logout Button Styling */
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }
        
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>Sistem CRM</h1>
        <div class="greeting">Selamat Datang, <?php echo $user['nama']; ?> 👋</div>
        <a href="dashboard-admin.php" class="logout-btn">Laman Utama</a>
    </div>

    <!-- Main container to center content -->
    <div class="container">
    

        <!-- Card for displaying staff -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">SENARAI STAF</h3>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari staf 🔍" style="padding: 8px; width: 300px;">
            </div>

            <?php
            // Query untuk senarai staf
            $sql = "SELECT id_staff, nama, email, jawatan, password, role, status FROM staff";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nama</th><th>Email</th><th>Jawatan</th><th>Password</th><th>role</th><th>Status</th><th>Aksi</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['id_staff'] . "</td>
                            <td>" . $row['nama'] . "</td>
                            <td>" . $row['email'] . "</td>
                            <td>" . $row['jawatan'] . "</td>
                            <td>" . $row['role'] . "</td>
                            <td style='text-align: c<td><a href=enter; vertical-align: middle;'>🔒</td>
                            <td>" . $row['status'] . "</td>
                            <td><a href='edit-staf.php?id=" . $row['id_staff'] . "'>Edit</a> | 
                                <a href='hapus-staf.php?id=" . $row['id_staff'] . "'>Hapus</a></td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Tiada staf yang terdaftar.</p>";
            }
            ?>
        </div>
        
        <br> <br> <br>

        <!-- Card for adding new staff -->
        <div class="card tambah-staf-card" style="max-width: 500px; margin: 0 auto;">
            <h3 style="text-align: center;">Tambah Staf Baru</h3>
            <form action="tambah-staff.php" method="POST" style="display: flex; flex-direction: column; align-items: flex-start; padding: 0 10px;">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="jawatan">Jawatan:</label>
                <input type="text" id="jawatan" name="jawatan" required><br>

                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="support">Customer Support</option>
                </select><br>

                <label for="password">Password:</label>
                <input type="text" id="password" name="password" required><br>

                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select><br>

                <!-- Center the submit button -->
                <div style="width: 100%; text-align: center; margin-top: 20px;">
                    <button type="submit">Tambah Staf</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue, matchFound;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.querySelector("table");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {  // skip table header (i=0)
                td = tr[i].getElementsByTagName("td");
                matchFound = false;

                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                    
                        // Check kotak kosong
                        if (filter == ""){
                            td[j].style.backgroundColor = ""; // Buang highlight
                        } else if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            td[j].style.backgroundColor = "yellow"; //highlight
                            matchFound = true;
                        } else {
                            td[j].style.backgroundColor = ""; //Nothing
                        }
                    }
                } 


                // Show or hide the row based on match
                if (filter == "") {
                    tr[i].style.display = "";
                } else {
                    if (matchFound) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>