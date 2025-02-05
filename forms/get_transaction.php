<?php
include 'koneksi.php'; // File koneksi ke database

if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
    $query = "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
}
?>
