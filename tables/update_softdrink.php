<?php
include 'koneksi2.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_masakan = $_POST['nama_masakan'];
    $harga = $_POST['harga'];
    $status_masakan = $_POST['status_masakan'];
    $stock_menu = $_POST['stock_menu'];
    $note = $_POST['note'];
    
    $updateQuery = "UPDATE masakan SET nama_masakan='$nama_masakan', harga='$harga', status_masakan='$status_masakan', stock_menu='$stock_menu', note='$note' WHERE id_masakan='$id'";
    
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_file = "uploads/" . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $updateQuery .= ", image='$image'";
    }
    
    if (mysqli_query($conn, $updateQuery)) {
        header("Location: daftar_menu.php");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
