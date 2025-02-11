<?php
include 'koneksi.php';

// Dapatkan waktu sekarang dan atur zona waktu
date_default_timezone_set('Asia/Jakarta');

// Clone current date for deadline and subtract one day
$deadline = new DateTime();
$deadline->modify('+15 minutes'); // Memajukan deadline 15 menit

// Format bagian tanggal dan waktu untuk $dayDate dan $yearTime
$dayDate = $deadline->format('l, d F'); // Format hari, tanggal, dan bulan
$yearTime = $deadline->format('Y, H:i'); // Format tahun dan waktu
if (isset($_GET['id_order']) && isset($_GET['total_payment'])) {
    $id_order = $_GET['id_order'];
    $total_payment = $_GET['total_payment'];
} else {
    echo "<script>alert('Data pembayaran tidak valid.'); window.location.href='index.php';</script>";
    exit;
}

if (isset($_GET['id_order']) && isset($_GET['total_payment']) && isset($_GET['payment_with'])) {
    $id_order = (int)$_GET['id_order'];
    $total_payment = $_GET['total_payment'];
    $payment_with = $_GET['payment_with'];
} else {
    // Jika id_order tidak ditemukan, redireksi ke halaman lain atau tampilkan error
    echo "<script>alert('Data pembayaran tidak valid'); window.location.href='order_menu.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meifang Restaurant - Payment Cash</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
         body {
            font-family: 'Poppins', sans-serif;
        }
        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .va-container {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background: #fff;
        }
        .deadline {
            font-weight: bold;
            color: #6c757d;
            text-align: center;
        }
        .btn-status {
            background-color: #00aeb5;
            color: #fff;
            font-weight: bold;
        }
        .btn-status:hover {
            background-color: #009399;
            color: #fff;
        }
        .copy-icon {
    position: relative;
    color: #007bff;
    font-size: 20px;
    text-decoration: none;
}

.copy-icon:hover {
    color: #0056b3;
}

.copy-icon .tooltip {
    visibility: hidden;
    background-color: #000;
    color: #fff;
    text-align: center;
    border-radius: 5px;
    padding: 5px;
    position: absolute;
    bottom: 125%; /* Adjust tooltip position */
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 12px;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s;
}

.copy-icon:hover .tooltip {
    visibility: visible;
    opacity: 1;
}

.total-payment {
    display: flex;
    align-items: center;
    gap: 10px; /* Space between the payment and copy icon */
}
.detail-order {
    color: #007bff;
    text-decoration: none; /* Remove underline */
    font-size: 15px;
    font-weight: bold;
    transition: color 0.3s ease; /* Smooth transition effect on hover */
}

.detail-order:hover {
    color:rgb(27, 105, 187); /* Darker blue color on hover for better UX */
}
.deadline{
    font-size: 20px;
    font-weight: bold;
}
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="fw-bold mb-10 text-center">Payment Invoice</h2>
    <div class="va-container shadow">
        <div class="deadline">
            <b>Payment Deadline</b>
            <p class="mb-0">
                <span style="font-weight: bold-700; color: black;"><?php echo $dayDate; ?></span><br>
                <span style="color: black;"><?php echo $yearTime; ?></span>
            </p>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
            <?php
    // Ambil metode pembayaran dari URL
    $payment_with = isset($_GET['payment_with']) ? $_GET['payment_with'] : '';

    // Determine the payment method name and logo based on payment_with
    switch (strtolower($payment_with)) {
        case 'cash':
            echo '<span><strong>Cash</strong></span>';
            echo '<img src="../assets/img/payment/money.png" alt="BCA Logo" style="height: 24px;">';
            break;
        default:
            echo '<span><strong>Metode pembayaran tidak diketahui</strong></span>';
            break;
    }
?>

            </div>  
            <div class="card-body">
                <p class="mb-1">No Order</p>
                <h5 class="bold">
                    <strong id="orderID"><?php echo $id_order; ?></strong>
                    <a href="#" class="copy-icon" onclick="copyText('orderID')">
                        <i class="far fa-copy"></i>
                        <span class="tooltip">Salin</span>
                    </a>
                </h5>
                <p class="mb-1 mt-3">Payment Invoice</p>
                <h4 class="bold d-flex align-items-center justify-content-between">
                    <span class="total-payment">
                        <strong id="totalPayment">Rp <?php echo number_format($total_payment, 3, ',', '.'); ?></strong>
                        <a href="#" class="copy-icon" onclick="copyText('totalPayment')">
                            <i class="far fa-copy"></i>
                            <span class="tooltip">Salin</span>
                        </a>
                    </span>
                    <a href="payment_guide.php" onclick="
                        localStorage.removeItem('cart'); // Hapus cart dari localStorage
                        window.location.href = 'payment_guide.php?id_order=<?php echo $id_order; ?>&total_payment=<?php echo $total_payment; ?>&payment_with=<?php echo $payment_with; ?>';
                        return false;" class="detail-order">
                        <i class="fas fa-info-circle"></i> Payment Guide
                    </a>

                </h4>
            </div>
        </div>

        <div class="d-grid mt-3">
    <button class="btn btn-primary px-4" onclick="window.location.href='detail_order.php?id_order=<?= $_GET['id_order'] ?>'">
        <b>Detail Order</b>
    </button>
</div>

    </div>
</div>

        <script>
function copyText(elementId) {
    // Get the text from the clicked element
    var textToCopy = document.getElementById(elementId).innerText;

    // Use the Clipboard API to copy the text
    navigator.clipboard.writeText(textToCopy).then(function() {
        // Optional: Provide feedback, e.g., change text to "Copied!"
        // alert("Text copied: " + textToCopy);
    }).catch(function(error) {
        console.error("Error copying text: ", error);
    });
}
</script>
    <script>
        // Redirect to transaction_order page
        function redirectToTransactionOrder() {
            window.location.href = 'transaction_order.php'; // Replace with your actual URL or route
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
