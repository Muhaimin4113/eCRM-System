<?php
session_start();
require 'db_connection.php'; // fail untuk connection ke DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Elak SQL injection guna prepared statement
    $stmt = $conn->prepare("SELECT * FROM staff WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Semak kalau ada matching user
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION["role"] = $user["role"];
        $_SESSION["email"] = $user["email"];

        switch ($_SESSION["role"]) {
            case "admin": header("Location: dashboard-admin.php"); break;
            case "staff": header("Location: dashboard-staff.php"); break;
            case "support": header("Location: dashboard-support.php"); break;
            default: header("Location: dashboard.php"); break;
        }
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Login CRM</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to top right, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 400px; /* adjust max-width to control the box size */
      display: flex;
      flex-direction: column;
      justify-content: center; /* Vertical center */
      align-items: center; /* Horizontal center */
    }
    h1 {
      margin-bottom: 20px;
      font-size: 28px;
      text-align: center;
    }
    form {
      width: 90%;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
    }
    button {
      padding: 10px 100px;
      background-color: rgba(58, 33, 200, 0.67);
      border: 10px;
      border-radius: 8px;
      color: white;
      font-size: 18px;
      cursor: pointer;
      margin-top: 10px;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
    .error {
      color: #ff6b6b;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h1>Sistem CRM Login</h1>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post">
      <input type="text" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Katalaluan" required>
      <button type="submit">Log Masuk</button>
    </form>
  </div>

</body>
</html>
