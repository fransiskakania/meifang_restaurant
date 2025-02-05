<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id_transaksi = $_GET['id'] ?? null;
if ($id_transaksi) {
    // Hapus transaksi berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
    $stmt->bind_param("i", $id_transaksi);
    $stmt->execute();
    $stmt->close();
}

header("Location: transaction_history.php");
exit();
?>
