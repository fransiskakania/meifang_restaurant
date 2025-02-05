<?php
include './koneksi.php'; // Replace with your actual database connection file

if (isset($_GET['id_masakan'])) {
    $id_masakan = $_GET['id_masakan'];
    $query = "DELETE FROM masakan WHERE id_masakan = '$id_masakan'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: daftar_menu_1.php"); // Redirect back to the menu page after deletion
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>
