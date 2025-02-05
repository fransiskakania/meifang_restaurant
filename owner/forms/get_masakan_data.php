<?php
// Include file koneksi ke database
include 'koneksi2.php';

// Set header untuk menghasilkan output JSON
header('Content-Type: application/json');

try {
    // Query untuk mengambil semua data dari tabel `masakan`
    $query = "SELECT id_masakan, nama_masakan, harga, stock_menu FROM masakan";
    $result = mysqli_query($conn, $query);

    // Periksa apakah query berhasil
    if (!$result) {
        throw new Exception('Query gagal: ' . mysqli_error($conn));
    }

    $data = [];

    // Iterasi hasil query untuk membuat array data
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id_masakan' => $row['id_masakan'],
            'nama_masakan' => $row['nama_masakan'],
            'harga' => $row['harga'],
            'image' => $row['image'],
            'stock_menu' => $row['stock_menu']
        ];
    }

    // Kirim data sebagai JSON
    echo json_encode($data);
} catch (Exception $e) {
    // Jika terjadi kesalahan, kirim pesan error
    echo json_encode(['error' => $e->getMessage()]);
}

// Tutup koneksi database
mysqli_close($conn);
?>
