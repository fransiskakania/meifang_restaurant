<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Koneksi database gagal"]));
}

$id_order = $_POST['id_order'] ?? '';

if (!empty($id_order)) {
    // Ambil data transaksi berdasarkan ID Order
    $stmt = $conn->prepare("SELECT total_payment, payment_with FROM transaksi WHERE id_order = ?");
    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "price" => $row['total_payment'],
            "payment_with" => $row['payment_with']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "ID Order tidak ditemukan"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "ID Order kosong"]);
}

mysqli_close($conn);
?>
