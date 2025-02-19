<?php
include 'koneksi.php';

function showErrorAndExit($message, $redirectUrl) {
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
                    icon: 'error',
                    title: 'Oops...',
                    text: '$message',
                }).then(function() {
                    window.location.href = '$redirectUrl';
                });
            });
        </script>";
    exit();
}

$id_order = $_POST['id_order'] ?? null;
$price = $_POST['price'] ?? null;
$cash_amount = $_POST['cash_amount'] ?? null;

if ($id_order && $price) {
    $stmt = $conn->prepare("SELECT subtotal, tax, payment_with, status_order FROM transaksi WHERE id_order = ?");
    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $payment_with = $row['payment_with'];
        $status_order = $row['status_order'];
        $subtotal = (float)$row['subtotal'];
        $tax = (float)$row['tax'];
        $total_payment = $subtotal + $tax;
        $price = (float)$price;

        if ($payment_with === 'Cash') {
            if ($cash_amount < $total_payment) {
                showErrorAndExit('Saldo tidak cukup!', 'javascript:history.back()');
            } else {
                $change_amount = $cash_amount - $total_payment;
                $updateStmt = $conn->prepare("UPDATE transaksi SET status_order = 'Success' WHERE id_order = ?");
                $updateStmt->bind_param("s", $id_order);
                
                if ($updateStmt->execute()) {
                    header("Location: struck_order.php?id_order=$id_order&subtotal=$subtotal&tax=$tax&total_payment=$total_payment&cash_amount=$cash_amount&change_amount=$change_amount");
                    exit;
                }
                $updateStmt->close();
            }
        } else {
            if ($price >= $total_payment) {
                $updateStmt = $conn->prepare("UPDATE transaksi SET status_order = 'Success' WHERE id_order = ?");
                $updateStmt->bind_param("s", $id_order);
                
                if ($updateStmt->execute()) {
                    header("Location: struck_order.php?id_order=$id_order&subtotal=$subtotal&tax=$tax&total_payment=$total_payment");
                    exit;
                }
                $updateStmt->close();
            } else {
                showErrorAndExit('Pembayaran tidak mencukupi!', 'javascript:history.back()');
            }
        }
    } else {
        showErrorAndExit('Transaksi tidak ditemukan!', 'javascript:history.back()');
    }
    $stmt->close();
} else {
    showErrorAndExit('Data tidak lengkap!', 'javascript:history.back()');
}

$conn->close();
?>
