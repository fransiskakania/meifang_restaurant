<?php
// Menghubungkan ke database
include 'koneksi.php'; // Pastikan file koneksi sudah benar

// Periksa apakah parameter id_user ada
if (isset($_GET['id_user'])) {
    $id_user = $_GET['id_user'];

    // Query untuk menghapus data pengguna
    $sql = "DELETE FROM user_manager WHERE id_user = '$id_user'";

    if (mysqli_query($conn, $sql)) {
        // Redirect dengan notifikasi sukses menggunakan SweetAlert
        header("Location: user_manager.php");
    } else {
        // Redirect dengan notifikasi error menggunakan SweetAlert
        $error_message = urlencode(mysqli_error($conn));
        header("Location: user_manager.php?status=error&message=$error_message");
    }

    // Tutup koneksi
    mysqli_close($conn);
} else {
    // Redirect jika id_user tidak ditemukan
    header("Location: user_manager.php?status=not_found");
}
