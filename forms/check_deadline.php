<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meifang Resto - Check Deadline</title>
    <link rel="icon" href="../assets/img/meifang_resto_logo/2.svg" type="image/x-icon" />

</head>
<body>
    
</body>
</html>
<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

// Ambil waktu saat ini
$current_time = date('Y-m-d H:i:s');

// Ambil semua pesanan dengan status 'pending' yang melewati deadline
$query = "SELECT id_order FROM transaksi WHERE status_order = 'pending' AND deadline < ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $current_time);
$stmt->execute();
$result = $stmt->get_result();

// Fungsi untuk menampilkan SweetAlert dan redirect
function showAlertAndExit($icon, $title, $message, $redirectUrl) {
    echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
        <style>
            .swal2-popup {
                font-family: 'Poppins', sans-serif;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$message',
                }).then(function() {
                    window.location.href = '$redirectUrl';
                });
            });
        </script>";
    exit();
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_order = $row['id_order'];

        // Ubah status menjadi 'canceled'
        $update_query = "UPDATE transaksi SET status_order = 'canceled' WHERE id_order = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $id_order);
        $update_stmt->execute();
    }
    showAlertAndExit('success', 'Pesanan Dibatalkan', 'Semua pesanan yang melewati batas waktu telah dibatalkan.', 'digital_payment.php');
} else {
    showAlertAndExit('info', 'Tidak Ada Pesanan', 'Tidak ada pesanan yang perlu dibatalkan.', 'digital_payment.php');
}
?>
