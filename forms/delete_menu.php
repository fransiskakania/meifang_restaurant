<?php
// Koneksi ke database
$conn = mysqli_connect('localhost', 'root', '', 'apk_kasir');

// Periksa koneksi
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Koneksi database gagal!']));
}

// Periksa apakah parameter id_masakan dikirim
if (isset($_GET['id_masakan'])) {
    $id_masakan = intval($_GET['id_masakan']); // Pastikan id_masakan adalah angka

    // Query untuk menghapus data berdasarkan id_masakan
    $query = "DELETE FROM masakan WHERE id_masakan = $id_masakan";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Menu berhasil dihapus.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus menu.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID masakan tidak ditemukan.']);
}

// Tutup koneksi
mysqli_close($conn);
?>
