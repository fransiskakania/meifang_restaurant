<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_masakan = $_POST['id_masakan'];
    $nama_masakan = $_POST['nama_masakan'];
    $harga = $_POST['harga'];
    $qty = $_POST['qty'];
    $image = $_POST['image'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id_masakan'] == $id_masakan) {
            $item['qty'] += $qty;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id_masakan' => $id_masakan,
            'nama_masakan' => $nama_masakan,
            'harga' => $harga,
            'qty' => $qty,
            'image' => $image
        ];
    }

    echo json_encode(["status" => "success", "message" => "Item added to cart"]);
}
?>
