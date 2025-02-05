<?php
include 'koneksi.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id_order = $data['id_order'] ?? null;

if ($id_order) {
    // Mark the order as canceled or delete it from the database
    $sql = "DELETE FROM order_details WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_order);

    if ($stmt->execute()) {
        // Optionally, you can also delete the order itself if necessary
        $sqlOrder = "DELETE FROM orders WHERE id_order = ?";
        $stmtOrder = $conn->prepare($sqlOrder);
        $stmtOrder->bind_param("i", $id_order);
        $stmtOrder->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel the order.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
}
?>
