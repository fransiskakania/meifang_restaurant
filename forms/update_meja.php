<?php
include 'koneksi.php'; // Sesuaikan dengan file koneksi Anda

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_meja = $_POST['nomor_meja'];
    $status_meja = $_POST['status_meja'];

    // Validasi
    if (empty($nomor_meja) || empty($status_meja)) {
        echo json_encode(["success" => false, "message" => "Data tidak boleh kosong"]);
        exit;
    }

    // Update hanya berdasarkan nomor_meja
    $query = "UPDATE meja SET status = ? WHERE nomor_meja = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status_meja, $nomor_meja);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal memperbarui data"]);
    }

    $stmt->close();
    $conn->close();
}
?>
