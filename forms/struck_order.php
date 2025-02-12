<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

// Pengecekan koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil id_order dari URL
$id_order = $_GET['id_order'] ?? null;
$cashAmount = $_GET['cash_amount'] ?? 0;
$changeAmount = $_GET['change_amount'] ?? 0;
if ($id_order) {
    // Ambil data transaksi berdasarkan ID Order
    $stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_order = ?");
    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        // Fetch the order details
        $order_stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_order = ?");
        $order_stmt->bind_param("s", $id_order);
        $order_stmt->execute();
        $order_details = $order_stmt->get_result();

        date_default_timezone_set('Asia/Jakarta'); // Set timezone to Indonesia (Jakarta)
    } else {
        echo "<p>Transaksi tidak ditemukan.</p>";
    }

    $stmt->close();
} else {
    echo "<p>ID Order tidak diberikan.</p>";
}

// Tutup koneksi database
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meifang Resto - Struck Order</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.5;
            text-align: center;
        }

        /* Receipt Container */
        .receipt {
            max-width: 400px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.9em;
        }

        .receipt-header img {
            width: 50px;
            margin: 0 auto;
        }

        .shop-info {
            font-weight: 600;
            font-size: 1.2em;
            margin: 5px 0;
        }

        .receipt-header,
        .receipt-footer {
            text-align: center;
            margin-bottom: 10px;
        }

        hr {
            border: none;
            border-top: 1px dashed #ddd;
            margin: 10px 0;
        }

        .receipt-details {
            text-align: left;
            margin-bottom: 10px;
        }

        .receipt-details div {
            display: flex;
            justify-content: space-between;
        }

        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .receipt-items th,
        .receipt-items td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px dashed #ddd;
        }

        .receipt-items th {
            font-weight: 600;
        }

        .total-section {
            font-weight: 600;
            font-size: 1em;
            text-align: right;
        }

        .total-section div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .thank-you {
            text-align: center;
            margin-top: 10px;
            font-size: 0.8em;
        }

        .highlight-green {
            color: green;
            font-weight: 600;
        }

        /* Print Optimization */
        @media print {
            body {
                background: none;
                color: #000;
            }

            .receipt {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>

    <script>
        // SweetAlert for successful payment
        Swal.fire({
            title: 'Payment Successful',
            text: 'Payment has been processed successfully!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>

    <div class="receipt">
        <!-- Header -->
        <div class="receipt-header">
            <img src="https://cdn-icons-png.flaticon.com/512/1533/1533161.png" alt="Shop Logo">
            <div class="shop-info">Meifang Resto</div>
            <div> Main Street, City, Country<br>No. Telp: 0812345678</div>
        </div>

        <hr>

        <!-- Receipt Details -->
        <div class="receipt-details">
            <div>
                <span>Id Order:</span>
                <span><?= htmlspecialchars($transaction['id_order']) ?></span>
            </div>
            <div><span>Date:</span><span><?= date('Y-m-d') ?></span></div>
            <div><span>Time:</span><span><?= date('H:i:s') ?></span></div>
            <div><span>Cashier:</span><span> <?= htmlspecialchars($transaction['user_role']) ?> </span></div>
            <div><span>Name:</span><span> <?= htmlspecialchars($transaction['name']) ?> </span></div>
        </div>

        <hr>

        <!-- Items Table -->
        <div class="receipt-items">
            <table>
                <tr>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
                <?php while ($detail = $order_details->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($detail['nama_masakan']) ?></td>
                    <td><?= htmlspecialchars($detail['quantity']) ?></td>
                    <td>Rp <?= number_format($detail['price'], 3) ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <!-- Total Section -->
        <div class="total-section">
    <div><span>Sub Total:</span><span>Rp <?= number_format($transaction['total_payment'], 3, ',', '.') ?></span></div>
    <div><span>Total:</span><span><strong style="color: blue;">Rp <?= number_format($transaction['total_payment'], 3, ',', '.') ?></strong></span></div>

    <?php if ($transaction['payment_with'] === 'Cash') : ?>
        <div><span>Cash Paid:</span><span>Rp <?= number_format($cashAmount, 3, ',', '.') ?></span></div>
        <div><span>Change:</span><span>Rp <?= number_format($changeAmount, 3, ',', '.') ?></span></div>
    <?php endif; ?>

    <div><span>Metode Pembayaran:</span><span><?= htmlspecialchars($transaction['payment_with']) ?></span></div>
</div>



        <hr>

        <!-- Thank You -->
        <div class="thank-you">
            Terima Kasih Telah Berbelanja<br>
            Link Kritik dan Saran:<br>
            <a href="./order_menu.php" style="text-decoration: none; color: blue;">e-receipt/S-00D39U-07G344G</a>
        </div>
    </div>
</body>
</html>
