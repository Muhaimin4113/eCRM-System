<?php
session_start();
require 'db_connection.php';

// Dapatkan nama pengguna dari sesi
$email = $_SESSION['email'];
$sql = "SELECT nama FROM staff WHERE email = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get the sales data based on selected time period (week, month, quarter, year)
$time_period = isset($_GET['time_period']) ? $_GET['time_period'] : 'month'; // Default to month

// Tarikh semasa
$current_date = date('Y-m-d');

// Dapatkan semua tahun yang ada dalam jualan
$years_result = $conn->query("SELECT DISTINCT YEAR(tarikh_jualan) as tahun FROM jualan ORDER BY tahun DESC");
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // default current year

// SQL query untuk statistik jualan
if ($time_period == 'week') {
    // Dapatkan tarikh jualan pertama dari database
    $sql_mula = "SELECT MIN(tarikh_jualan) AS mula_jualan FROM jualan WHERE YEAR(tarikh_jualan) = '$selected_year'";
    $result_mula = $conn->query($sql_mula);
    $row_mula = $result_mula->fetch_assoc();
    $tarikh_pertama = $row_mula['mula_jualan'];

    // Tetapkan ke hari Isnin minggu tersebut (WEEK mode 1 = minggu mula hari Isnin)
    $minggu_mula = date('Y-m-d', strtotime("monday this week", strtotime($tarikh_pertama)));

    // Tarikh akhir adalah hari ini
    $end_date = date('Y-m-d');

    // SQL mingguan
    $sql = "
        WITH RECURSIVE minggu_tempoh AS (
            SELECT DATE('$minggu_mula') AS minggu_mula
            UNION ALL
            SELECT DATE_ADD(minggu_mula, INTERVAL 1 WEEK)
            FROM minggu_tempoh
            WHERE minggu_mula < '$end_date'
            AND YEAR(minggu_mula)= '$selected_year'
        )
        SELECT 
            minggu_tempoh.minggu_mula,
            DATE_FORMAT(minggu_tempoh.minggu_mula, '%Y-%u') AS minggu_label,
            COALESCE(SUM(j.jumlah), 0) AS total_sales
        FROM minggu_tempoh
        LEFT JOIN jualan j
            ON WEEK(j.tarikh_jualan, 1) = WEEK(minggu_tempoh.minggu_mula, 1)
            AND YEAR(j.tarikh_jualan) = '$selected_year'
            AND j.status = 'completed'
        GROUP BY minggu_tempoh.minggu_mula
        ORDER BY minggu_tempoh.minggu_mula ASC;
    ";
} elseif ($time_period == 'month') {
    $sql = "SELECT MONTH(tarikh_jualan) as bulan, SUM(jumlah) as total_sales FROM jualan WHERE YEAR(tarikh_jualan) = '$selected_year' AND status = 'completed' GROUP BY MONTH(tarikh_jualan)";
} elseif ($time_period == 'quarter') {
    $sql = "SELECT QUARTER(tarikh_jualan) as suku, SUM(jumlah) as total_sales FROM jualan WHERE YEAR(tarikh_jualan) = '$selected_year' AND status = 'completed' GROUP BY QUARTER(tarikh_jualan)";
} else { // 'year'
    $sql = "SELECT YEAR(tarikh_jualan) as tahun, SUM(jumlah) as total_sales FROM jualan WHERE status = 'completed' GROUP BY YEAR(tarikh_jualan)";
}

$result = $conn->query($sql);

// Data untuk carta
$labels = [];
$data = [];
while ($row = $result->fetch_assoc()) {
    if ($time_period == 'week') {
        $labels[] = 'Minggu ' . $row['minggu_label'];
    } elseif ($time_period == 'month') {
        $labels[] = 'Bulan ' . $row['bulan'];
    } elseif ($time_period == 'quarter') {
        $labels[] = 'Suku ' . $row['suku'];
    } else {
        $labels[] = $row['tahun'];
    }
    $data[] = $row['total_sales'];
}

// --- Kira Top 5 Produk Berdasarkan Kuantiti Terjual dan Kira Keuntungan ---
$sql_top_products = "
    SELECT 
        produk.nama_produk, 
        SUM(jualan.kuantiti) AS total_kuantiti,
        SUM((produk.harga_jualan - produk.harga_modal) * jualan.kuantiti) AS total_keuntungan
    FROM jualan
    JOIN produk ON jualan.id_produk = produk.id_produk
    WHERE jualan.status = 'completed'
    GROUP BY jualan.id_produk
    ORDER BY total_kuantiti DESC
    LIMIT 5
";

$result_top_products = $conn->query($sql_top_products);

// Simpan data
$top_products = [];
$keseluruhan_keuntungan = 0;

while ($row = $result_top_products->fetch_assoc()) {
    $top_products[] = $row;
    $keseluruhan_keuntungan += $row['total_keuntungan'];
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Jualan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
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

        label {
            font-family: Arial, Helvetica, sans-serif;
            color: #fff;
            margin-right: 20px;
            margin-top: 11px;
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
            margin-top: 500px; /* To avoid navbar overlap */
        }

        /* Card styling */
        .card {
            background: rgba(255, 255, 255, 0.7);
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card:last-child {
            margin-top: 50px;
        }


        .card:not(:last-child) {
            margin-bottom: 16px;
        }

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

        /* Chart container */
        .chart-container {
            margin-top: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgb(0, 0, 0);
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
        }

        /* Form styling for time period selection */
        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        form select, form button {
            padding: 10px;
            border-radius: 6px;
            margin-right: 10px;
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
        }

        /* Button untuk print */
        .btn:hover {
            background-color: #0056b3;
        }

        .btn-print {
            background-color: rgba(255, 255, 255, 0.5);
            color: black;
            padding: 10px 20px;
            border-radius: 6px;
            border: 1px solid black;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print:hover {
            background-color:rgb(255, 255, 255);
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

    <!-- Main container -->
    <div class="container">

        <!-- Header -->
        <div class="header">
            <h2>Analisis Jualan</h2>
            <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
        </div>
    
        <!-- Time period selection -->
        <form method="GET" action="analisis-jualan.php">
            <label for="time_period">Pilih Tempoh: </label>
            <select name="time_period" id="time_period">
                <option value="week" <?php echo $time_period == 'week' ? 'selected' : ''; ?>>Minggu</option>
                <option value="month" <?php echo $time_period == 'month' ? 'selected' : ''; ?>>Bulan</option>
                <option value="quarter" <?php echo $time_period == 'quarter' ? 'selected' : ''; ?>>Suku Tahun</option>
                <option value="year" <?php echo $time_period == 'year' ? 'selected' : ''; ?>>Tahun</option>
            </select>

            <label for="year">Pilih Tahun: </label>
            <select name="year" id="year">
                <?php while ($yr = $years_result->fetch_assoc()): ?>
                    <option value="<?php echo $yr['tahun']; ?>" <?php echo $yr['tahun'] == $selected_year ? 'selected' : ''; ?>>
                        <?php echo $yr['tahun']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Lihat Statistik</button>
        </form>

        <!-- Display the chart -->
        <div class="chart-container">
            <canvas id="salesChart" width="400" height="200"></canvas>
        </div>

        <script>
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar', // Change bar or line
                data: {
                    labels: <?php echo json_encode($labels); ?>, // Labels (time period)
                    datasets: [{
                        label: 'Jumlah Jualan',
                        data: <?php echo json_encode($data); ?>, // Sales data
                        backgroundColor: 'rgba(4, 255, 255, 0.45)', // Less transparent
                        borderColor: 'rgb(13, 0, 255)', // Bold line
                        borderWidth: 3, // Make the line thicker
                    }]

                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true
                }
            });
        </script>

        <!-- Top 5 Trending Produk Card -->
        <div class="card">
            <h2 style="text-align:center;">Top 5 Produk Trending</h2>
            <table style="width:100%; margin-top:20px; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #007bff; color:white;">
                        <th style="padding:10px;">Produk</th>
                        <th style="padding:10px;">Kuantiti Terjual</th>
                        <th style="padding:10px;">Jumlah Keuntungan (RM)</th>
                        <th style="padding:10px;">% dari Keuntungan Keseluruhan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_products as $product): ?>
                    <tr style="text-align:center; background-color: rgba(255,255,255,0.9);">
                        <td style="padding:10px;"><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                        <td style="padding:10px;"><?php echo $product['total_kuantiti']; ?></td>
                        <td style="padding:10px;"><?php echo number_format($product['total_keuntungan'], 2); ?></td>
                        <td style="padding:10px;">
                            <?php 
                                $percentage = $keseluruhan_keuntungan > 0 ? ($product['total_keuntungan'] / $keseluruhan_keuntungan) * 100 : 0;
                                echo number_format($percentage, 2) . '%';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
