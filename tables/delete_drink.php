<?php
include 'koneksi2.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM masakan WHERE id_masakan = '$id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: daftar_menu.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>
