<?php
include 'koneksi.php';

if (isset($_GET['id_order'])) {
    $id_order = $_GET['id_order'];

    $sql = "SELECT nama_masakan, quantity, price FROM transaksi WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    $menuList = [];
    $totalPrice = 0;

    while ($row = $result->fetch_assoc()) {
        $menuList[] = [
            'nama_masakan' => $row['nama_masakan'],
            'quantity' => $row['quantity'],
            'price' => $row['price'] * $row['quantity']
        ];
        $totalPrice += $row['price'] * $row['quantity'];
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('ID Order tidak ditemukan!'); window.location.href='order_menu.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Detail Order</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Menu Name</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menuList as $menu) { ?>
                <tr>
                    <td><?= htmlspecialchars($menu['nama_masakan']) ?></td>
                    <td><?= $menu['quantity'] ?></td>
                    <td>Rp <?= number_format($menu['price'], 3, ',', '.') ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total Pembayaran</th>
                    <th>Rp <?= number_format($totalPrice, 3, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>
        <a href="index.php#menu" class="btn btn-danger">Home</a>

    </div>
</body>
</html>
