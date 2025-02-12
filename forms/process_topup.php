<?php
include 'koneksi.php';

// Ambil data dari form
$id_order = $_POST['id_order'] ?? null;
$price = $_POST['price'] ?? null;
$cash_amount = $_POST['cash_amount'] ?? null;

if ($id_order && $price) {
    // 1. Ambil data transaksi berdasarkan ID Order
    $stmt = $conn->prepare("SELECT payment_with, status_order, total_payment FROM transaksi WHERE id_order = ?");
    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $payment_with = $row['payment_with'];
        $status_order = $row['status_order'];
        $total_payment = (float)$row['total_payment']; // Konversi tipe data
        $price = (float)$price; // Konversi tipe data

        if ($payment_with === 'Cash') {
            if ($cash_amount && $cash_amount >= $total_payment) {
                $change_amount = $cash_amount - $total_payment;
        
                $updateStmt = $conn->prepare("UPDATE transaksi SET status_order = 'Success' WHERE id_order = ?");
                $updateStmt->bind_param("s", $id_order);
        
                if ($updateStmt->execute()) {
                    header("Location: struck_order.php?id_order=$id_order&cash_amount=$cash_amount&change_amount=$change_amount");
                    exit;
                }
                $updateStmt->close();
            }
        } else {
            // Jika bukan cash, gunakan metode biasa
            if ($price >= $total_payment) {
                $updateStmt = $conn->prepare("UPDATE transaksi SET status_order = 'Success' WHERE id_order = ?");
                $updateStmt->bind_param("s", $id_order);
                if ($updateStmt->execute()) {
                    header("Location: struck_order.php?id_order=$id_order");
                    exit;
                }
                $updateStmt->close();
            }
        }
    }
    $stmt->close();
}

// Tutup koneksi database
mysqli_close($conn);
?>
