<?php
// Start the session to retrieve totalPayment if using session
session_start();

// Initialize $totalPayment variable
$totalPayment = 0;

// Retrieve totalPayment from session or GET parameter
if (isset($_SESSION['totalPayment'])) {
    $totalPayment = $_SESSION['totalPayment'];
} elseif (isset($_GET['totalPayment'])) { // Alternatively, using GET parameter
    $totalPayment = (float) $_GET['totalPayment'];
}

// Database connection (assumed connection variable $conn is already established)
include 'koneksi.php';

// Dapatkan waktu sekarang dan atur zona waktu
date_default_timezone_set('Asia/Jakarta');

// Clone current date for deadline and subtract one day
$deadline = new DateTime();
$deadline->modify('+15 minutes'); // Memajukan deadline 15 menit

// Format bagian tanggal dan waktu untuk $dayDate dan $yearTime
$dayDate = $deadline->format('l, d F'); // Format hari, tanggal, dan bulan
$yearTime = $deadline->format('Y, H:i'); // Format tahun dan waktu

$id_order = isset($_GET['id_order']) ? htmlspecialchars($_GET['id_order']) : 'ID Order tidak ditemukan';

// Fetch payment method from the database based on id_order
$query = "SELECT payment_with FROM transaksi WHERE id_order = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_order);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Assuming there's only one transaction with the provided id_order
    $row = $result->fetch_assoc();
    $payment_with = $row['payment_with'];

    // Simpan deadline ke database
    $update_query = "UPDATE transaksi SET deadline = ? WHERE id_order = ?";
    $update_stmt = $conn->prepare($update_query);
    $deadlineFormatted = $deadline->format('Y-m-d H:i:s'); // Format deadline untuk database
    $update_stmt->bind_param("si", $deadlineFormatted, $id_order);

    if ($update_stmt->execute()) {
        echo "Deadline berhasil disimpan.";
    } else {
        echo "Gagal menyimpan deadline: " . $conn->error;
    }
} else {
    $payment_with = 'Payment method not found';
    echo "ID Order tidak ditemukan.";
}

if ($id_order) {
    // Ambil deadline dari tabel transaksi
    $query = "SELECT deadline, status_order FROM transaksi WHERE id_order = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $deadline = new DateTime($row['deadline']);
        $currentTime = new DateTime();

        // Periksa apakah pembayaran melewati deadline
        if ($currentTime > $deadline && $row['status_order'] != 'canceled') {
            // Update status_order menjadi 'canceled'
            $update_query = "UPDATE transaksi SET status_order = 'canceled' WHERE id_order = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("i", $id_order);

            if ($update_stmt->execute()) {
                echo "Status order telah diubah menjadi 'canceled'.";
            } else {
                echo "Gagal mengubah status order: " . $conn->error;
            }
        } else {
            echo "Pembayaran masih dalam batas waktu";
        }
    } else {
        echo "ID Order tidak ditemukan.";
    }
} else {
    echo "ID Order tidak diberikan.";
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Pembayaran</title>
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
    <h2 class="fw-bold mb-10 text-center">Tagihan Pembayaran</h2>
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
                    // Determine the payment method name and logo based on payment_with
                    switch (strtolower($payment_with)) {
                        case 'bca':
                            echo '<span><strong>BCA Virtual Account</strong></span>';
                            echo '<img src="../assets/img/payment/bca.png" alt="BCA Logo" style="height: 24px;">';
                            break;
                        case 'bri':
                            echo '<span><strong>BRI Virtual Account</strong></span>';
                            echo '<img src="../assets/img/payment/bri.png" alt="BRI Logo" style="height: 24px;">';
                            break;
                        case 'mandiri':
                            echo '<span><strong>Mandiri Virtual Account</strong></span>';
                            echo '<img src="../assets/img/payment/mandiri.png" alt="Mandiri Logo" style="height: 24px;">';
                            break;
                        case 'dana':
                            echo '<span><strong>DANA</strong></span>';
                            echo '<img src="../assets/img/payment/dana.png" alt="Dana Logo" style="height: 24px;">';
                            break;
                        case 'gopay':
                            echo '<span><strong>GoPay</strong></span>';
                            echo '<img src="../assets/img/payment/gopay.png" alt="GoPay Logo" style="height: 24px;">';
                            break;
                        case 'seabank':
                            echo '<span><strong>SeaBank</strong></span>';
                            echo '<img src="../assets/img/payment/seabank.png" alt="SeaBank Logo" style="height: 24px;">';
                            break;
                        default:
                            echo '<span><strong>Payment Method Not Found</strong></span>';
                            echo '<img src="../assets/img/payment/money.png" alt="Default Payment" style="height: 24px;">';
                            break;
                    }
                ?>
            </div>  
            <div class="card-body">
                <p class="mb-1">Nomor Virtual Account</p>
                <h5 class="bold">
                    <strong id="orderID"><?php echo $id_order; ?></strong>
                    <a href="#" class="copy-icon" onclick="copyText('orderID')">
                        <i class="far fa-copy"></i>
                        <span class="tooltip">Salin</span>
                    </a>
                </h5>
                <p class="mb-1 mt-3">Total Tagihan</p>
                <h4 class="bold d-flex align-items-center justify-content-between">
                    <span class="total-payment">
                        <strong id="totalPayment">Rp <?php echo number_format($totalPayment, 3, ',', '.'); ?></strong>
                        <a href="#" class="copy-icon" onclick="copyText('totalPayment')">
                            <i class="far fa-copy"></i>
                            <span class="tooltip">Salin</span>
                        </a>
                    </span>
                    <a href="digital_payment.php" class="detail-order">Detail Order</a>
                </h4>
            </div>
        </div>

        <div class="d-grid mt-3">
            <button class="btn btn-primary px-4" onclick="window.location.href='digital_payment.php'"><b>Payment</b></button>
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
