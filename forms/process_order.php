<?php
require 'koneksi.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!$conn) {
    error_log("Koneksi ke database gagal: " . mysqli_connect_error(), 3, 'error_log.txt');
    echo json_encode(['success' => false, 'errors' => ['Koneksi ke database gagal.']]);
    exit;
}

$response = ['success' => false, 'errors' => [], 'stock' => []];

if (!empty($data)) {
    foreach ($data as $item) {
        $id_masakan = mysqli_real_escape_string($conn, $item['id_masakan']);
        $quantity = (int)$item['quantity'];
        $price = (float)$item['price'];

        // Ambil nama masakan dan stock berdasarkan id_masakan
        $nameQuery = "SELECT nama_masakan, harga, stock_menu FROM masakan WHERE id_masakan = '$id_masakan'";
        $nameResult = mysqli_query($conn, $nameQuery);

        if ($nameResult && $nameRow = mysqli_fetch_assoc($nameResult)) {
            $nama_masakan = $nameRow['nama_masakan']; // Menyimpan nama masakan
            $price = (float)$nameRow['harga']; // Menyimpan harga masakan
            $stock_menu = (int)$nameRow['stock_menu']; // Menyimpan stok asli

            // Simpan data ke tabel orders
            $query = "INSERT INTO orders (id_masakan, quantity, price, nama_masakan, stock_menu) 
                      VALUES ('$id_masakan', '$quantity', $price, '$nama_masakan', $stock_menu)";
            if (!mysqli_query($conn, $query)) {
                $errorMessage = "Gagal menyimpan order untuk ID $id_masakan: " . mysqli_error($conn);
                $response['errors'][] = $errorMessage;
                error_log($errorMessage, 3, 'error_log.txt');
            } else {
                // Tambahkan stok ke dalam respons
                $response['stock'][] = ['id_masakan' => $id_masakan, 'nama_masakan' => $nama_masakan, 'stock_menu' => $stock_menu];
                
                // Log data masakan ke console
                error_log("ID Masakan: $id_masakan, Nama Masakan: $nama_masakan, Stok Menu: $stock_menu", 4);
            }
        } else {
            $errorMessage = "Masakan dengan ID $id_masakan tidak ditemukan.";
            $response['errors'][] = $errorMessage;
            error_log($errorMessage, 3, 'error_log.txt');
        }
    }

    if (empty($response['errors'])) {
        $response['success'] = true;
    }
} else {
    $errorMessage = "Data kosong atau format salah.";
    $response['errors'][] = $errorMessage;
    error_log($errorMessage, 3, 'error_log.txt');
}

echo json_encode($response);
?>
