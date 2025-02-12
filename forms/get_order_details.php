<?php
include 'koneksi.php';

if (isset($_POST['id_order'])) {
    $id_order = $_POST['id_order'];
    
    $query = "SELECT total_payment, payment_with FROM transaksi WHERE id_order = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'price' => $row['total_payment'],
            'payment_with' => $row['payment_with']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
