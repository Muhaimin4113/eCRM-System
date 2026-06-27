<?php
session_start();
require 'db_connection.php';

// Check if the confirmation action is set and delete the staff
if (isset($_GET['id']) && isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $id = $_GET['id'];

    // Query to delete staff
    $sql = "DELETE FROM staff WHERE id_staff = '$id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect with success action
        header("Location: pengurusan-staf.php?action=deleted");
        exit();
    } else {
        // Redirect with error action
        header("Location: pengurusan-staf.php?action=error");
        exit();
    }
}

// If confirmation is not set, show a confirmation page
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch staff info for display
    $sql = "SELECT nama FROM staff WHERE id_staff = '$id'";
    $result = $conn->query($sql);
    $staff = $result->fetch_assoc();
    
    echo "<script>
            if (confirm('Adakah anda pasti mahu memadamkan staf " . $staff['nama'] . "?')) {
                window.location.href = 'hapus-staf.php?id=" . $id . "&confirm=yes';
            } else {
                window.location.href = 'pengurusan-staf.php';
            }
          </script>";
}
?>
