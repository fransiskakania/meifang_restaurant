<?php
include 'koneksi.php'; // Pastikan file koneksi database tersedia

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomor_meja'])) {
    $nomor_meja = intval($_POST['nomor_meja']);
    
    $stmt = $conn->prepare("DELETE FROM meja WHERE nomor_meja = ?");
    $stmt->bind_param("i", $nomor_meja);
    
    $response = [];
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
