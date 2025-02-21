<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_meja = $_POST['nomor_meja'];
    $status = $_POST['status'];

    // Cek apakah nomor meja sudah ada
    $checkQuery = $conn->prepare("SELECT * FROM meja WHERE nomor_meja = ?");
    $checkQuery->bind_param("i", $nomor_meja);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Nomor meja sudah ada!"]);
    } else {
        $query = $conn->prepare("INSERT INTO meja (nomor_meja, status) VALUES (?, ?)");
        $query->bind_param("is", $nomor_meja, $status);
        if ($query->execute()) {
            echo json_encode(["success" => true, "message" => "Meja berhasil ditambahkan!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menambahkan meja."]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
