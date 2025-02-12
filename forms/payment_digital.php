<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/meifang_resto_logo/2.svg" type="image/x-icon"/>
    <title>Meifang Resto - Proccess Payment </title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .swal2-popup {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

// Pengecekan koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data dari form
$id_order = $_POST['id_order'] ?? null;
$price = $_POST['price'] ?? null;

$notification = ""; // Variabel untuk menyimpan notifikasi

if ($id_order && $price) {
    // 1. Ambil data transaksi berdasarkan ID Order
    $stmt = $conn->prepare("SELECT payment_with, status_order, total_payment FROM transaksi WHERE id_order = ?");
    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $payment_with = $row['payment_with'];
        $status_order = $row['status_order'];
        $total_payment = (float)$row['total_payment']; // Konversi tipe data
        $price = (float)$price; // Konversi tipe data

        // 2. Periksa apakah jumlah yang di-top-up mencukupi
        if ($price >= $total_payment) {
            // Update status_order menjadi "Success"
            $updateStmt = $conn->prepare("UPDATE transaksi SET status_order = 'Success' WHERE id_order = ?");
            $updateStmt->bind_param("s", $id_order);
            if ($updateStmt->execute()) {
                $notification = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        html: 'Payment sebesar <b>Rp " . number_format($price, 3, ',', '.') . "</b> berhasil!<br>Status transaksi telah diperbarui menjadi \"Success\"',
                        willClose: () => {
                            window.location.href = 'struck_order.php?id_order=" . $id_order . "';
                        }
                    });
                </script>";
            } else {
                $notification = "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memperbarui status transaksi.',
                        willClose: () => {
                            window.location.href = 'digital_payment.php';
                        }
                    });
                </script>";
            }
            $updateStmt->close();
        } else {
            $notification = "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    html: 'Jumlah top-up sebesar <b>Rp " . number_format($price, 3, ',', '.') . "</b> tidak mencukupi untuk membayar transaksi sebesar <b>Rp " . number_format($total_payment, 2, ',', '.') . "</b>.'
                });
            </script>";
        }
    } else {
        $notification = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'ID Order tidak ditemukan.',
                willClose: () => {
                    window.location.href = 'digital_payment.php';
                }
            });
        </script>";
    }

    $stmt->close();
} else {
    $notification = "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Data yang dikirim tidak lengkap.',
            willClose: () => {
                window.location.href = 'digital_payment.php';
            }
        });
    </script>";
}

// Tutup koneksi database
mysqli_close($conn);

// Tampilkan notifikasi jika ada
if (!empty($notification)) {
    echo $notification;
}
?>


    <!-- SweetAlert JS -->
    </body>
</html>
