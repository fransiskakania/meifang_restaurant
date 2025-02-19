<?php
include 'koneksi.php';

// Fungsi untuk menampilkan SweetAlert dan menghentikan eksekusi
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
function showSuccessAndRedirect($message, $redirectUrl) {
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
                    icon: 'warning',  // Ganti 'success' menjadi 'warning'
                    title: 'Warning!',
                    text: '$message',
                }).then(function() {
                    window.location.href = '$redirectUrl';
                });
            });
        </script>";
    exit();
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest id_order dynamicallya
$sql_latest_order = "SELECT id_order FROM order_details ORDER BY tanggal DESC LIMIT 1";
$stmt_latest_order = $conn->prepare($sql_latest_order);
$stmt_latest_order->execute();
$result_latest_order = $stmt_latest_order->get_result();


if ($result_latest_order && $result_latest_order->num_rows > 0) {
    $row = $result_latest_order->fetch_assoc();
    $id_order = $row['id_order'];
} else {
    showErrorAndExit("Error: No recent orders found. Please place an order first.", "../index.php");
}
$stmt_latest_order->close();

// **Tambahan Validasi**: Jika tidak ada id_order, hentikan proses.
if (empty($id_order)) {
    showErrorAndExit("Invalid request! No order ID found.", "../index.php");
}

// Fetch all order details for the latest id_order
$sql = "SELECT user_role, tanggal, id_order, name, no_meja, nama_masakan, quantity, price, type_order 
        FROM order_details 
        WHERE id_order = ? 
        ORDER BY tanggal DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_order);
$stmt->execute();
$result = $stmt->get_result();

$order_details = [];
while ($row = $result->fetch_assoc()) {
    $order_details[] = $row;
}

$stmt->close();

if (empty($order_details)) {
    die("No order details found.");
}

 // Membersihkan dan mengonversi input ke float
 $subtotal = isset($_POST['subtotal']) ? (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['subtotal']) : 0;
 $tax = isset($_POST['tax']) ? (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['tax']) : 0;
 $totalPayment = isset($_POST['total']) ? (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['total']) : 0;
 $cashAmount = isset($_POST['cash_amount']) ? (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['cash_amount']) : 0;
 $changeAmount = isset($_POST['change_amount']) ? (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['change_amount']) : 0;
 $paymentMethod = isset($_POST['payment_with']) ? $_POST['payment_with'] : '';
// Pastikan payment method dikirim
$paymentMethod = isset($_POST['payment_with']) ? strtolower($_POST['payment_with']) : '';

if ($paymentMethod === 'cash') {
    if ($cashAmount <= 0) {
        showErrorAndExit("Please enter a valid cash amount.", "transaction_order.php");
    }

    if ($cashAmount < $totalPayment) {
        showErrorAndExit("Cash amount is less than the total payment. Please enter the correct amount.", "transaction_order.php");
    }

    if ($cashAmount >= $totalPayment) {
        $status_order = "Success";
        $change = $cashAmount - $totalPayment;

        // Tampilkan alert JavaScript dan redirect ke halaman success
    }
}



// Prepare the SQL insert query
$sql_insert = "INSERT INTO transaksi (user_role, date, no_meja, name, id_order, nama_masakan, quantity, price, type_order, status_order, payment_with, subtotal, tax, total_payment, cash_amount, change_amount) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);

// Retrieve additional inputs
$paymentMethod = isset($_POST['payment_with']) ? $_POST['payment_with'] : '';
$changeAmount = isset($_POST['change_amount']) ? (float)$_POST['change_amount'] : 0.00;

$status_order = (strtolower($paymentMethod) === 'cash') ? "Success" : "Pending";

// Check if payment method is "cash" and cash amount is empty
if (strtolower($paymentMethod) === 'cash' && empty($cashAmount)) {
    showErrorAndExit("Please enter the cash amount", "transaction_order.php"); // Redirect to order menu page with error message
}

// Bind and execute for each order detail
foreach ($order_details as $detail) {
    $stmt->bind_param(
        "ssssssidsssddddd",
        $detail['user_role'],
        $detail['tanggal'],
        $detail['no_meja'],
        $detail['name'],
        $detail['id_order'],
        $detail['nama_masakan'],
        $detail['quantity'],
        $detail['price'],
        $detail['type_order'],
        $status_order,
        $paymentMethod,
        $subtotal,      // Tambahkan subtotal
        $tax,      // Tambahkan subtotal
        $totalPayment,  // Total pembayaran
        $cashAmount,
        $changeAmount
    );
    $stmt->execute();
}

$sql_delete_order_details = "DELETE FROM order_details WHERE id_order = ?";
$stmt_delete = $conn->prepare($sql_delete_order_details);
$stmt_delete->bind_param("s", $detail['id_order']);

if ($stmt_delete->execute()) {
    // Debug: Pastikan id_order memiliki nilai
    if (empty($detail['id_order'])) {
        echo "Error: id_order is empty.";
        exit();
    }

    // Simpan ID yang terhapus
    $sql_insert_deleted = "INSERT INTO deleted_orders (id_order) VALUES (?)";
    $stmt_insert_deleted = $conn->prepare($sql_insert_deleted);

    if (!$stmt_insert_deleted) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $stmt_insert_deleted->bind_param("s", $detail['id_order']);
    if ($stmt_insert_deleted->execute()) {
        // No echo output, but the operation was successful.
        // Optionally, log a success message if needed.
    } else {
        // Log the error message to a file instead of displaying it
        error_log("Error inserting into deleted_orders: " . $stmt_insert_deleted->error, 3, "/path/to/your/logfile.log");
        exit();
    }
    

    $stmt_insert_deleted->close();
} else {
    echo "Error deleting order details: " . $stmt_delete->error;
    exit();
}

$stmt_delete->close();
// Redirect after all orders are processed
if ($status_order === "Pending") {
    showSuccessAndRedirect("Payment is pending! please confirm payment before due date", "noncash_payment.php?totalPayment=" . urlencode($totalPayment) . "&id_order=" . urlencode($detail['id_order']) . "&paymentMethod=" . urlencode($paymentMethod));
    exit();
}
elseif ($status_order === "Success") {
    // Pass data to cash_payment.php via session
    session_start();
    $_SESSION['order_details'] = $order_details;
    $_SESSION['subtotal'] = $subtotal;
    $_SESSION['tax'] = $tax;
    $_SESSION['total_payment'] = $totalPayment;
    $_SESSION['cash_amount'] = $cashAmount;
    $_SESSION['change_amount'] = $changeAmount;

    header("Location: cash_payment.php?id_order=" . urlencode($detail['id_order']) . "&totalPayment=" . urlencode($totalPayment));
    exit();
}

$stmt->close();
$conn->close();
?>
