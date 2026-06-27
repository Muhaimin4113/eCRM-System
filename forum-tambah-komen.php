<?php
// forum-tambah-komen.php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['id_staff']) || !isset($_POST['kandungan'], $_POST['id_komen'])) {
    die("Akses tidak sah.");
}

$id_post = intval($_POST['id_komen']);
$id_staff = $_SESSION['id_staff'];
$kandungan = trim($_POST['kandungan']);

if ($kandungan === '') {
    die("Kandungan tidak boleh kosong.");
}

$stmt = $conn->prepare("INSERT INTO forum_komen (id_post, id_staff, kandungan) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id_post, $id_staff, $kandungan);
$stmt->execute();

header("Location: forum-post.php?id=$id_post");
exit();
?>
