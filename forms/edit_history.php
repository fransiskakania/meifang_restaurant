<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id_transaksi = $_GET['id'] ?? null;
if (!$id_transaksi) {
    die("ID transaksi tidak valid.");
}

// Ambil data transaksi berdasarkan ID
$stmt = $conn->prepare("SELECT id_order, date, user_role, payment_with, total_payment, status_order FROM transaksi WHERE id_transaksi = ?");
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_order = $_POST['status_order'];

    // Update data transaksi
    $stmt = $conn->prepare("UPDATE transaksi SET status_order = ? WHERE id_transaksi = ?");
    $stmt->bind_param("si", $status_order, $id_transaksi);
    $stmt->execute();
    $stmt->close();

    header("Location: transaction_history.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi</title>
</head>
<body>
    <h2>Edit Status Transaksi</h2>
    <form method="POST">
        <label>Status Order:</label>
        <select name="status_order">
            <option value="pending" <?= ($transaction['status_order'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="success" <?= ($transaction['status_order'] == 'success') ? 'selected' : '' ?>>Success</option>
            <option value="canceled" <?= ($transaction['status_order'] == 'canceled') ? 'selected' : '' ?>>Canceled</option>
        </select>
        <button type="submit">Update</button>
    </form>
</body>
</html>
