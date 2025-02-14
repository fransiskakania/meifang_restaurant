<?php
session_start();

if (!isset($_SESSION['order_details'])) {
    die("No payment data available.");
}
$order_details = $_SESSION['order_details'];
$totalPayment = $_SESSION['total_payment'];
$cashAmount = $_SESSION['cash_amount'];
$changeAmount = $_SESSION['change_amount'];

// Log session data to the browser console
echo "<script>
    console.log('Order Details:', " . json_encode($order_details) . ");
    console.log('Total Payment:', " . json_encode($totalPayment) . ");
    console.log('Cash Amount:', " . json_encode($cashAmount) . ");
    console.log('Change Amount:', " . json_encode($changeAmount) . ");
</script>";

date_default_timezone_set('Asia/Jakarta');

// Clear session after data retrieval
// unset($_SESSION['id_order'], $_SESSION['order_details'], $_SESSION['total_payment'], $_SESSION['cash_amount'], $_SESSION['change_amount']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Meifang Resto - Notification</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
    <!-- Google Font: Poppins -->
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
        <div>Jl. Pantai Boulevard, Jakarta Utara<br>No. Telp: 0822-4707-9268</div>
    </div>

    <hr>

    <!-- Receipt Details -->
    <div class="receipt-details">
        <div>
            <span>Id Order:</span>
            <?php if (!empty($order_details)): ?>
                <span><?= htmlspecialchars($order_details[0]['id_order']) ?></span>
            <?php endif; ?>
        </div>
        
        <div>
            <span>Date:</span>
            <span>
                <?php 
                    echo !empty($order_details) 
                        ? date("d-m-Y H:i:s", strtotime($order_details[0]['tanggal'])) 
                        : "No date available";
                ?>
            </span>
        </div>

        <?php if (!empty($order_details)): ?>
            <div><span>Cashier:</span> <span><?= htmlspecialchars($order_details[0]['user_role']) ?></span></div>
            <div><span>Name:</span> <span><?= htmlspecialchars($order_details[0]['name']) ?></span></div>
            <div><span>No Meja:</span> <span><?= htmlspecialchars($order_details[0]['no_meja']) ?></span></div>
        <?php endif; ?>
    </div>

    <hr>

    <!-- Items Table -->
    <div class="receipt-items">
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $detail): ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['nama_masakan']) ?></td>
                        <td><?= htmlspecialchars($detail['quantity']) ?></td>
                        <td>Rp <?= number_format($detail['price'], 3) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <!-- Total Section -->
    <div class="total-section">
        <div><span>Sub Total:</span> <span>Rp <?= number_format($totalPayment, 3) ?></span></div>
        <div><span>Total:</span> <span><strong style="color: blue;">Rp <?= number_format($totalPayment, 3) ?></strong></span></div>
        <div><span>Cash Paid:</span> <span>Rp <?= number_format($cashAmount, 3) ?></span></div>
        <div><span>Change:</span> <span>Rp <?= number_format($changeAmount, 3) ?></span></div>
    </div>

    <hr>

    <!-- Thank You Section -->
    <div class="thank-you">
        Terima Kasih Telah Berbelanja<br>
        Link Kritik dan Saran:<br>
        <a href="./order_menu.php" style="text-decoration: none; color: blue;">e-receipt/S-00D39U-07G344G</a>
    </div>
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tangkap elemen struk sebagai gambar
            html2canvas(document.querySelector("#receipt")).then(canvas => {
                // Konversi ke data URL (base64)
                let imageData = canvas.toDataURL("image/png");

                // Kirim gambar ke server menggunakan fetch API
                fetch("save_receipt_image.php", {
                    method: "POST",
                    body: JSON.stringify({ image: imageData }),
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Success", "Receipt saved successfully!", "success");
                    } else {
                        Swal.fire("Error", "Failed to save receipt!", "error");
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "An unexpected error occurred!", "error");
                });
            });
        });
    </script>
</body>
</html>
