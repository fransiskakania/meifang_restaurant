<?php
// Hubungkan ke database
include 'koneksi.php'; // Pastikan path sesuai dengan lokasi file koneksi Anda

// Hapus semua data dari tabel orders
$query = "DELETE FROM orders";
if (mysqli_query($conn, $query)) {
    // Redirect ke halaman order_menu.php setelah berhasil
    header("Location: ../forms/order_menu.php");
    exit();
} else {
    echo "Error deleting orders: " . mysqli_error($conn);
}

// Tutup koneksi
mysqli_close($conn);
?>
