<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">

    <title>Meifang Restaurant - Process</title>
</head>
<body>
    
</body>
</html>
<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_order = $_POST['id_order'];
    $payment_with = $_POST['paymentMethods'];
    $total_payment = floatval(str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['total_payment']));
    $status_order = 'Pending'; // Status tetap "Pending" untuk semua metode pembayaran

    
    // Ambil detail pesanan dari order_details
    $sql = "SELECT * FROM order_details WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_masakan = $row['id_masakan']; // Ambil id_masakan
            $nama_masakan = $row['nama_masakan'];
            $quantity = (int)$row['quantity'];
            $price = (float)$row['price'];
            $user_role = $row['user_role'];
            $name = $row['name'];
            $no_meja = (int)$row['no_meja'];
            $type_order = $row['type_order'];

            // Simpan transaksi ke database
            $insertStmt = $conn->prepare("INSERT INTO transaksi (id_order, date, nama_masakan, quantity, price, user_role, name, no_meja, type_order, payment_with, total_payment, status_order) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("isidssissds", $id_order, $nama_masakan, $quantity, $price, $user_role, $name, $no_meja, $type_order, $payment_with, $total_payment, $status_order);
            $insertStmt->execute();
            $insertStmt->close();

            // Update stok jika status_order adalah "Cash" (di kasir)
            $sql_update_stock = "UPDATE masakan SET stock_menu = stock_menu - ? WHERE nama_masakan = ?";
            $stmt_update = $conn->prepare($sql_update_stock);
            $stmt_update->bind_param("is", $quantity, $nama_masakan);
            
            if (!$stmt_update->execute()) {
                echo "Error updating stock for " . $nama_masakan . ": " . $stmt_update->error . "\n";
            }
            
            $stmt_update->close();
            
        }
    }

    $stmt->close();

    // Hapus data dari order_details setelah transaksi berhasil disimpan
    $deleteStmt = $conn->prepare("DELETE FROM order_details WHERE id_order = ?");
    $deleteStmt->bind_param("i", $id_order);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Masukkan id_order ke tabel deleted_orders
    $sql_insert_deleted = "INSERT INTO deleted_orders (id_order) VALUES (?)";
    $stmt_insert_deleted = $conn->prepare($sql_insert_deleted);
    
    if (!$stmt_insert_deleted) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $stmt_insert_deleted->bind_param("i", $id_order);
    
    if (!$stmt_insert_deleted->execute()) {
        error_log("Error inserting into deleted_orders: " . $stmt_insert_deleted->error);
        exit();
    }
    
    $stmt_insert_deleted->close();
    $conn->close();

    // Redirect berdasarkan metode pembayaran
    if ($payment_with === 'Cash') {
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
                    icon: 'info',
                    title: 'Cash Payment',
                    text: 'Payment can be made at the cashier.',
                }).then(function() {
                    localStorage.removeItem('cart'); // Remove cart from localStorage
                    window.location.href = 'payment_cash.php?id_order=$id_order&total_payment=$total_payment&payment_with=$payment_with';
                });
            });
        </script>";
} else {
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
                    icon: 'success',
                    title: 'Non-Cash Payment',
                    text: 'Payment is being processed.',
                }).then(function() {
                    localStorage.removeItem('cart'); // Remove cart from localStorage
                    window.location.href = 'payment_noncash.php?id_order=$id_order&total_payment=$total_payment&payment_with=$payment_with';
                });
            });
        </script>";
}

} else {
    echo "<script>alert('Akses tidak diizinkan.'); window.location.href='order_menu.php';</script>";
}
?>
