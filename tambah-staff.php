<?php
session_start();

date_default_timezone_set('Asia/Kuala_Lumpur');

/* database connection */
require 'db_connection.php';
require 'log-helper.php';

// Semak jika borang dihantar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jawatan = $_POST['jawatan'];
    $password = $_POST['password'];
    $status = $_POST['status'];

    // Sediakan query untuk masukkan data staf
    $sql = "INSERT INTO staff (nama, email, jawatan, password, status) 
            VALUES ('$nama', '$email', '$jawatan', '$password', '$status')";

    // Semak sama ada query berjaya
    if ($conn->query($sql) === TRUE) {
        // Jika berjaya, alihkan semula ke pengurusan-staf.php dengan mesej kejayaan
        header("Location: pengurusan-staf.php?message=Staf berjaya ditambah!");
        logAktiviti($conn, 'Menambah staff berserta maklumat');
        exit();
    } else {
        // Jika ada masalah dengan query
        header("Location: pengurusan-staf.php?message=Ralat semasa menambah staf!");
        exit();
    }
}
?>
