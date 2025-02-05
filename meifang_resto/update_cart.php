<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'];
$amount = $data['amount'];

if (isset($_SESSION['cart'][$index])) {
    $_SESSION['cart'][$index]['qty'] += $amount;
    
    if ($_SESSION['cart'][$index]['qty'] <= 0) {
        unset($_SESSION['cart'][$index]);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
}
