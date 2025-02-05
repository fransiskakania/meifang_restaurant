<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'];

if (isset($_SESSION['cart'][$index])) {
    unset($_SESSION['cart'][$index]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
}
