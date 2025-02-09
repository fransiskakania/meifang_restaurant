<?php
include 'koneksi.php';

header('Content-Type: application/json'); // Set response sebagai JSON

$response = [];

if (isset($_GET['id_order'])) {
    $id_order = $_GET['id_order'];

    // Hapus order dari order_details
    $sql = "DELETE FROM order_details WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_order);

    if ($stmt->execute()) {
        $response['status'] = "success";
    } else {
        $response['status'] = "error";
    }

    $stmt->close();
}

$conn->close();

// Kembalikan JSON response
echo json_encode($response);
?>
