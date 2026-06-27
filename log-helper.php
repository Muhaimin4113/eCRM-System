<?php
function logAktiviti($conn, $tindakan) {
    if (!isset($_SESSION)) session_start();

    if (isset($_SESSION['id_staff']) && isset($_SESSION['role'])) {
        $id_staff = $_SESSION['id_staff'];
        $peranan = $_SESSION['role'];
        $masa = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO log_aktiviti (id_staff, role, tindakan, masa) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_staff, $peranan, $tindakan, $masa);
        $stmt->execute();
        $stmt->close();
    }
}
?>
