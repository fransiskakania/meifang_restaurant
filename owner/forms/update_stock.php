<?php
require 'koneksi.php'; // Pastikan koneksi sudah benar

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

// Periksa apakah data valid
if (!$data) {
    echo json_encode(['success' => false, 'errors' => ['Data JSON tidak valid.']]);
    exit;
}

// Pastikan koneksi ke database berhasil
if (!$conn) {
    echo json_encode(['success' => false, 'errors' => ['Koneksi ke database gagal.']]);
    exit;
}

$response = ['success' => false, 'errors' => []];

// Periksa apakah data yang diterima valid
if (isset($data['id_masakan'], $data['new_stock'], $data['quantity'])) {
    $id_masakan = mysqli_real_escape_string($conn, $data['id_masakan']);
    $new_stock = (int)$data['new_stock'];
    $quantity = (int)$data['quantity'];

    // Update stok di tabel masakan
    $updateQuery = "UPDATE masakan SET stock_menu = $new_stock WHERE id_masakan = '$id_masakan'";

    if (mysqli_query($conn, $updateQuery)) {
        // Insert perubahan stok ke tabel stock_menu
        $insertStockQuery = "INSERT INTO stock_menu (id_masakan, stok) VALUES ('$id_masakan', '$quantity')";
        
        if (mysqli_query($conn, $insertStockQuery)) {
            $response['success'] = true;
            echo json_encode($response);
            exit;
        } else {
            $errorMessage = "Gagal mencatat perubahan stok: " . mysqli_error($conn);
            $response['errors'][] = $errorMessage;
        }
    } else {
        $errorMessage = "Gagal memperbarui stok masakan: " . mysqli_error($conn);
        $response['errors'][] = $errorMessage;
    }
} else {
    $response['errors'][] = "Data yang diterima tidak valid (id_masakan, new_stock, atau quantity tidak ada).";
}

echo json_encode($response);
?>
