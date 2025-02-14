<?php
include 'koneksi.php'; // Pastikan koneksi database sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_transaksi = $_POST['edit_id_transaksi'];
    $total_payment = $_POST['edit_total_payment'];
    $status_order = $_POST['edit_status_order'];

    $query = "UPDATE transaksi SET total_payment = ?, status_order = ? WHERE id_transaksi = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dsi", $total_payment, $status_order, $id_transaksi);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
?>
