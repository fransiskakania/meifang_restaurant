<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_order = $_POST['id_order'];
    $payment_with = $_POST['paymentMethods'];
    $total_payment = floatval(str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['total_payment']));
    $status_order = ($payment_with === 'Cash') ? 'Pending' : 'Success';

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
            $insertStmt = $conn->prepare("INSERT INTO transaksi (id_order, date, nama_masakan, quantity, price, user_role, name, no_meja, type_order, payment_with, total_payment, status_order) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("isidssissds", $id_order, $nama_masakan, $quantity, $price, $user_role, $name, $no_meja, $type_order, $payment_with, $total_payment, $status_order);
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

    // Masukkan id_order ke tabel deleted_orders
    $sql_insert_deleted = "INSERT INTO deleted_orders (id_order) VALUES (?)";
    $stmt_insert_deleted = $conn->prepare($sql_insert_deleted);

    if (!$stmt_insert_deleted) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $stmt_insert_deleted->bind_param("i", $id_order); 

    if (!$stmt_insert_deleted->execute()) {
        // Jika terjadi error, bisa langsung keluar atau ditangani dengan log
        error_log("Error inserting into deleted_orders: " . $stmt_insert_deleted->error);
        exit();
    }
    
    $stmt_insert_deleted->close();
    $conn->close(); // Pindahkan koneksi close ke sini setelah semua operasi selesai

    // Redirect berdasarkan metode pembayaran
    if ($payment_with === 'Cash') {
        echo "<script>
            alert('Pembayaran dapat dilakukan di kasir.');
            window.location.href = 'payment_cash.php?id_order=$id_order&total_payment=$total_payment&payment_with=$payment_with';
        </script>";
    } else {
        echo "<script>
            alert('Pembayaran berhasil diproses.');
            localStorage.removeItem('cart'); // Hapus cart dari localStorage
            window.location.href = 'payment_noncash.php?id_order=$id_order&total_payment=$total_payment&payment_with=$payment_with';
        </script>";
    }
} else {
    echo "<script>alert('Akses tidak diizinkan.'); window.location.href='order_menu.php';</script>";
}
?>
