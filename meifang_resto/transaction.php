<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">

    <title>Meifang Restaurant - Transaction </title>
</head>
<body>
    
</body>
</html>
<?php

include 'koneksi.php';

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
                    confirmButtonColor: '#d33'
                }).then(function() {
                    window.location.href = '$redirectUrl';
                });
            });
        </script>";
    exit();
}

if (!isset($_GET['id_order']) || empty($_GET['id_order'])) {
    showErrorAndExit('ID Order tidak ditemukan!', 'cart.php');
}

$id_order = $_GET['id_order'];

// Periksa apakah id_order benar-benar ada di database
$check_order = $conn->prepare("SELECT COUNT(*) FROM order_details WHERE id_order = ?");
$check_order->bind_param("s", $id_order);
$check_order->execute();
$check_order->bind_result($order_count);
$check_order->fetch();
$check_order->close();

if ($order_count == 0) {
    showErrorAndExit('ID Order tidak valid!', 'cart.php');
}

// Ambil data pesanan berdasarkan id_order
$query = $conn->prepare("SELECT * FROM order_details WHERE id_order = ?");
$query->bind_param("s", $id_order);
$query->execute();
$result = $query->get_result();

// Ambil informasi umum pesanan
$query_info = $conn->prepare("SELECT tanggal, user_role, name, no_meja, type_order FROM order_details WHERE id_order = ? LIMIT 1");
$query_info->bind_param("s", $id_order);
$query_info->execute();
$result_info = $query_info->get_result();
$order_info = $result_info->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meifang - Transaction Details</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (Popper & Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="./css/transaction.css">

</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-3">Transaction Details</h2>
        
        <div class="row">
            <!-- Table Transaction Details -->
            <div class="col-md-8">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Menu Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_harga = 0;
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            $subtotal = $row['quantity'] * $row['price'];
                            $total_harga += $subtotal;
                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama_masakan']}</td>
                                <td>{$row['quantity']}</td>
                                <td>Rp " . number_format($row['price'], 3, ',', '.') . "</td>
                                <td>Rp " . number_format($subtotal, 3, ',', '.') . "</td>
                            </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th>Rp <?php echo number_format($total_harga, 3, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

          <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                <p><strong>Date:</strong> <?php echo htmlspecialchars($order_info['tanggal']); ?></p>
                    <p><strong>ID Order:</strong> <?php echo htmlspecialchars($id_order); ?></p>
                    <p><strong>User Role:</strong> <?php echo htmlspecialchars($order_info['user_role']); ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order_info['name']); ?></p>
                    <p><strong>No Table:</strong> <?php echo htmlspecialchars($order_info['no_meja']); ?></p>
                    <p><strong>Type Order:</strong> <?php echo htmlspecialchars($order_info['type_order']); ?></p>
                    <hr>
                    <h5 class="text-start">Total: Rp <?php echo number_format($total_harga, 3, ',', '.'); ?></h5>
                    <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#paymentModal">Confirm Payment</button>
                </div>
            </div>
        </div>

        </div>
        <button id="deletePaymentBtn" class="btn btn-danger mt-3">Delete Payment</button>

        <!-- Modal Payment -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="save_transaction.php" id="paymentForm">
                            <input type="hidden" name="id_order" value="<?php echo htmlspecialchars($id_order); ?>">
                            <input type="hidden" name="payment_with" id="paymentMethod">

                            <div class="container">

                                <!-- Cash Section -->
                                <div class="payment-category">
                                <b>Cash</b>
                                <label class="payment-option  selected d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('Cash', this)">
                                    <div>
                                        <img src="../assets/img/payment/money.png" alt="Cash">
                                        <span>Cash</span>
                                    </div>
                                    <input type="radio" id="paymentMethodsCash" name="paymentMethods" value="Cash" checked>
                                </label>
                            </div>

                                <!-- Transfer Virtual Account -->
                                <div class="payment-category">
                                    <b>Transfer Virtual Account</b>
                                    <label class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('BCA')">
                                        <div>
                                            <img src="../assets/img/payment/bca.png" alt="BCA">
                                            <span>BCA</span>
                                        </div>
                                        <input type="radio" id="paymentMethodsBCA" name="paymentMethods" value="BCA">
                                    </label>

                                    <label class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('BRI')">
                                        <div>
                                            <img src="../assets/img/payment/bri.png" alt="BRI">
                                            <span>BRI</span>
                                        </div>
                                        <input type="radio" id="paymentMethodsBRI" name="paymentMethods" value="BRI">
                                    </label>

                                    <label class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('Mandiri')">
                                        <div>
                                            <img src="../assets/img/payment/mandiri.png" alt="Mandiri">
                                            <span>Mandiri</span>
                                        </div>
                                        <input type="radio" id="paymentMethodsMandiri" name="paymentMethods" value="Mandiri">
                                    </label>
                                </div>

                                <!-- E-Wallet -->
                                <div class="payment-category">
                                    <b>E-Wallet</b>
                                    <label class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('Dana')">
                                        <div>
                                            <img src="../assets/img/payment/dana.png" alt="Dana">
                                            <span>Dana</span>
                                        </div>
                                        <input type="radio" id="paymentMethodsDana" name="paymentMethods" value="Dana">
                                    </label>

                                    <label class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('GoPay')">
                                        <div>
                                            <img src="../assets/img/payment/gopay.png" alt="GoPay">
                                            <span>GoPay</span>
                                        </div>
                                        <input type="radio" id="paymentMethodsGoPay" name="paymentMethods" value="GoPay">
                                    </label>

                                    <label class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('SeaBank')">
                                        <div>
                                            <img src="../assets/img/payment/seabank.png" alt="SeaBank">
                                            <span>SeaBank</span>
                                        </div>
                                        <input type="radio" id="paymentMethodsSeaBank" name="paymentMethods" value="SeaBank">
                                    </label>
                                </div>  

                                <!-- Total Payment -->
                                <div class="mb-4">
                                    <label class="form-label">Total Payment (Rp)</label>
                                    <input type="text" class="form-control" id="TotalPayment" name="total_payment" value="Rp<?php echo number_format($total_harga, 3, ',', '.'); ?>" readonly>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
    function selectPaymentMethod(method) {
        // Set nilai input hidden
        document.getElementById("paymentMethod").value = method;

        // Hapus class 'active' dari semua pilihan
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('active');
        });

        // Tambahkan class 'active' ke elemen yang diklik
        event.currentTarget.classList.add('active');
    }
    </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("deletePaymentBtn").addEventListener("click", function() {
    Swal.fire({
        title: "Are you sure?",
        text: "This action will delete the payment permanently.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Ambil ID Order dari PHP
            let id_order = "<?php echo htmlspecialchars($id_order); ?>";
            
            // Kirim request ke delete_payment.php
            fetch("delete_payment.php?id_order=" + id_order)
                .then(response => response.json())  // Parse JSON dari PHP
                .then(data => {
                    if (data.status === "success") {
                        localStorage.removeItem("cart"); // Hapus cart dari localStorage
                        Swal.fire({
                            title: "Deleted!",
                            text: "The payment has been successfully deleted.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "index.php"; // Redirect setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to delete payment.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
        }
    });
});
</script>


</body>

</html>

<?php
$query->close();
$query_info->close();
$conn->close();
?>
