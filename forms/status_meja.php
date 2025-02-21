<?php
require 'koneksi.php'; // Sesuaikan dengan koneksi database

// Ambil semua nomor meja dari transaksi yang sedang berlangsung
$query = "SELECT DISTINCT no_meja FROM transaksi WHERE status_order != 'selesai'";
$result = $conn->query($query);

$updated = 0; // Untuk menghitung jumlah meja yang diperbarui

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $no_meja = $row['no_meja'];

        // Periksa apakah meja sudah not-available
        $checkQuery = $conn->prepare("SELECT status FROM meja WHERE nomor_meja = ?");
        $checkQuery->bind_param("i", $no_meja);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();
        $meja = $checkResult->fetch_assoc();

        if ($meja && $meja['status'] !== 'not-available') {
            // Update status meja menjadi not-available
            $updateTable = $conn->prepare("UPDATE meja SET status = 'not-available' WHERE nomor_meja = ?");
            $updateTable->bind_param("i", $no_meja);
            $updateTable->execute();
            $updated++; // Tambah hitungan meja yang diperbarui
        }
    }
}

// Kirimkan respons JSON untuk ditampilkan dalam alert
if ($updated > 0) {
    echo json_encode(["message" => "Status meja diperbarui menjadi 'Not Available' untuk $updated meja yang sedang digunakan"]);
} else {
    echo json_encode(["message" => "Tidak ada meja yang perlu diperbarui"]);
}
?>
