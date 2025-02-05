<?php
include 'koneksi.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_transaksi = $_POST['id_transaksi'];
    $total_payment = $_POST['total_payment'];
    $status_order = $_POST['status_order'];

    $query = "UPDATE transaksi SET total_payment='$total_payment', status_order='$status_order' WHERE id_transaksi='$id_transaksi'";
    
    if ($conn->query($query) === TRUE) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
}
?>
