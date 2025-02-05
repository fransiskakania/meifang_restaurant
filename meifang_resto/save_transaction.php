<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_order = $_POST['id_order'];
    $payment_with = $_POST['paymentMethods'];
    $total_payment = floatval(str_replace(['Rp', ',', '.'], '', $_POST['total_payment']));
    $cash_amount = isset($_POST['cash_amount']) ? floatval(str_replace(['Rp', ',', '.'], '', $_POST['cash_amount'])) : 0;
    $change_amount = isset($_POST['change_amount']) ? floatval(str_replace(['Rp', ',', '.'], '', $_POST['change_amount'])) : 0;
    $status_order = ($payment_with === 'Cash') ? 'Pending' : 'Paid';
    
    // Ambil detail pesanan dari order_details
    $sql = "SELECT * FROM order_details WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nama_masakan = $row['nama_masakan'];
            $quantity = (int)$row['quantity'];
            $price = (float)$row['price'];
            $user_role = $row['user_role'];
            $name = $row['name'];
            $no_meja = (int)$row['no_meja'];
            $type_order = $row['type_order'];
            
            // Simpan transaksi ke database
            $insertStmt = $conn->prepare("INSERT INTO transaksi (id_order, date, nama_masakan, quantity, price, user_role, name, no_meja, type_order, payment_with, total_payment, cash_amount, change_amount, status_order) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("isidssisssddd", $id_order, $nama_masakan, $quantity, $price, $user_role, $name, $no_meja, $type_order, $payment_with, $total_payment, $cash_amount, $change_amount, $status_order);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }

    $stmt->close();
    
    // Hapus data dari order_details setelah transaksi berhasil disimpan
    $deleteStmt = $conn->prepare("DELETE FROM order_details WHERE id_order = ?");
    $deleteStmt->bind_param("i", $id_order);
    $deleteStmt->execute();
    $deleteStmt->close();
    
    $conn->close();
    
    // Redirect sesuai metode pembayaran
    if ($payment_with === 'Cash') {
        echo "<script>alert('Pembayaran dapat dilakukan di kasir.'); window.location.href='transaction.php';</script>";
    } else {
        header("Location: noncash_payment.php?id_order=$id_order");
        exit();
    }
} else {
    echo "<script>alert('Akses tidak diizinkan.'); window.location.href='order_menu.php';</script>";
}
