<?php
$connection = new mysqli("localhost", "root", "", "apk_kasir");

session_start();
$user_id = $_SESSION['user_id'] ?? 1; // Sesuaikan dengan sistem autentikasi Anda

$query = "SELECT m.id_masakan, m.nama_masakan, m.harga, c.quantity 
          FROM cart c
          JOIN masakan m ON c.id_masakan = m.id_masakan
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total_items = 0;

while ($row = $result->fetch_assoc()) {
    $items[] = [
        'id_masakan' => $row['id_masakan'],
        'nama_masakan' => $row['nama_masakan'],
        'harga' => $row['harga'],
        'quantity' => $row['quantity']
    ];
    $total_items += $row['quantity'];
}

echo json_encode(['items' => $items, 'total_items' => $total_items]);
?>
