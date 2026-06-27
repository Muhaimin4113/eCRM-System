<?php
session_start();
include 'db_connection.php';

$sql = "SELECT log.*, staff.nama FROM log_aktiviti AS log
        JOIN staff ON log.id_staff = staff.id_staff
        ORDER BY log.masa DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Log Aktiviti</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('asset/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: #fff;
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      min-height: 100vh;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.65);
      min-height: 100vh;
      padding: 40px 60px;
    }

    h1 {
      text-align: center;
      margin-bottom: 40px;
      font-size: 36px;
      color: #ffffff;
      letter-spacing: 2px;
    }

    .log-container {
      max-width: 1000px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      backdrop-filter: blur(10px);
    }

    .log-entry {
      border-bottom: 1px solid rgba(255,255,255,0.2);
      padding: 20px 0;
    }

    .log-entry:last-child {
      border-bottom: none;
    }

    .log-entry h3 {
      margin: 0;
      font-size: 20px;
      color: #fff;
    }

    .log-entry p {
      margin: 5px 0 0;
      color: #ddd;
      font-size: 15px;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 30px;
      background-color: #1a237e;
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #3949ab;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <a href="dashboard-admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
    <h1>Log Aktiviti Staf</h1>

    <div class="log-container">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="log-entry">
            <h3><?php echo htmlspecialchars($row['nama']); ?> (ID: <?php echo htmlspecialchars($row['id_staff']); ?>)</h3>
            <p><strong>Peranan:</strong> <?php echo htmlspecialchars($row['role']); ?></p>
            <p><strong>Tindakan:</strong> <?php echo htmlspecialchars($row['tindakan']); ?></p>
            <p><strong>Masa:</strong> <?php echo date('d/m/Y H:i:s', strtotime($row['masa'])); ?></p>
          </div>
        <?php endwhile; ?>
        <?php else: ?>
          <p>Tiada log aktiviti direkodkan.</p>
        <?php endif; ?>
    </div>
  </div>
</body>
</html>
