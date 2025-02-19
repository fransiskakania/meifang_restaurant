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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meifang Resto - Detail Orders</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .order-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .order-item {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .total-row {
            font-size: 1.2rem;
            font-weight: bold;
           
            padding: 15px;
            border-radius: 8px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container order-container">
        <h2 class="text-center mb-4">Detail Orders</h2>
        <div class="order-list">
            <?php foreach ($menuList as $menu) { ?>
            <div class="order-item d-flex justify-content-between">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($menu['nama_masakan']) ?></h5>
                    <small>Quantity: <?= $menu['quantity'] ?></small>
                </div>
                <div class="fw-bold">Rp <?= number_format($menu['price'], 3, ',', '.') ?></div>
            </div>
            <?php } ?>
        </div>
        <div class="total-row mt-3 d-flex justify-content-between">
    <div>Total Pembayaran:</div>
    <div class="fw-bold">Rp <?= number_format($totalPrice, 3, ',', '.') ?></div>
</div>

        <div class="d-flex justify-content-between mt-3">
            <a href="index.php" class="btn btn-danger"><i class="bi bi-house-door"></i> Home</a>
            <a href="order_pdf.php?id_order=<?= $id_order ?>" class="btn btn-primary"><i class="bi bi-file-earmark-pdf"></i> Save Data</a>
        </div>
    </div>
</body>
</html>