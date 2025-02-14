<?php
include 'koneksi.php'; // Pastikan koneksi database sudah benar

if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];

    $query = "SELECT * FROM transaksi WHERE id_transaksi = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_transaksi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }

    $stmt->close();
}
?>
