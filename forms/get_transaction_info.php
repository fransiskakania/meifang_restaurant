<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil id_order yang diteruskan via AJAX
$id_order = isset($_GET['id_order']) ? $_GET['id_order'] : '';

// Query untuk mengambil detail transaksi berdasarkan id_order, dan total_payment hanya sekali
$sql = "SELECT nama_masakan, quantity, price, total_payment FROM transaksi WHERE id_order = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_order);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $output = "<table class='table'>";
    $output .= "<tr><th>No.</th><th>Menu Name</th><th>Quantity</th><th>Price</th></tr>";

    $total_payment = 0;
    $no = 1; // Variabel untuk nomor urut
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr><td>" . $no++ . "</td>"; // Menambahkan nomor urut
        $output .= "<td>" . htmlspecialchars($row['nama_masakan']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        $output .= "<td>Rp " . number_format($row['price'], 3, ',', '.') . "</td></tr>";
        
        // Menambahkan total_payment untuk akumulasi
        $total_payment += $row['total_payment'];
    }

    // Menampilkan total_payment hanya sekali di bagian bawah tabel
    $output .= "<tr><td colspan='4' style='text-align:right;'><strong>Total : Rp " . number_format($total_payment, 3, ',', '.') . "</strong></td></tr>";
    $output .= "</table>";
    echo $output;
} else {
    echo "No details found for this order.";
}

$stmt->close();
$conn->close();
?>
