<?php
// Menangkap data dari URL
if (isset($_GET['id_order']) && isset($_GET['total_payment']) && isset($_GET['payment_with'])) {
    $id_order = (int)$_GET['id_order'];
    $total_payment = $_GET['total_payment'];
    $payment_with = $_GET['payment_with'];

    // Proses data transaksi berdasarkan id_order
    $conn = new mysqli("localhost", "root", "", "apk_kasir");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id_order, total_payment, payment_with 
            FROM transaksi 
            WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_payment = $row['total_payment'];
        $payment_with = $row['payment_with'];
    } else {
        // Jika id_order tidak ditemukan, berikan pemberitahuan atau arahkan ke halaman lain
        echo "Transaksi tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
} else {
    // Jika tidak ada parameter yang ditemukan
    echo "<script>alert('ID Order tidak ditemukan.'); window.location.href='index#menu.php';</script>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meifang Restaurant - Payment Guide</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 710px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .step-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>


    <div class="container">
        <div class="card p-4">
            <h2 class="text-center mb-4">Payment Guide (Cash Method)</h2>
            <ol class="list-group list-group-numbered">
                <li class="list-group-item">After placing your order, please proceed to the cashier immediately.</li>
                <li class="list-group-item">If payment exceeds the specified time, the order will be canceled.</li>
                <li class="list-group-item">Bringing outside food or drinks is strictly prohibited.</li>
                <li class="list-group-item">Carrying sharp weapons, drugs, and other illegal substances is prohibited.</li>
                <li class="list-group-item">If you encounter any issues, please inform our staff for assistance.</li>
                <li class="list-group-item">We appreciate your cooperation and look forward to serving you. Enjoy your meal!</li>
            </ol>
            <div class="text-center mt-4">
                <button class="btn btn-primary" onclick="redirectToPayment()">I Agree</button>
            </div>
        </div>
    </div>
    <script>
        function redirectToPayment() {
            const id_order = "<?php echo $id_order; ?>";
            const total_payment = "<?php echo $total_payment; ?>";
            const payment_with = "<?php echo $payment_with; ?>";
            
            window.location.href = `payment_cash.php?id_order=${id_order}&total_payment=${total_payment}&payment_with=${payment_with}`;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
