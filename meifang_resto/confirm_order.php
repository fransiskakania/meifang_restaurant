<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $user_role = $_POST['user_role'];
    $name = $_POST['name'];
    $no_meja = $_POST['no_meja'];
    $type_order = $_POST['type_order'];
    $cart_data = json_decode($_POST['cart_data'], true);
    $subtotal = 0;
    $tax = 5; // Pajak tetap, bisa diubah sesuai kebutuhan
    
    // Generate id_order
    $query_deleted = $conn->prepare("SELECT MAX(id_order) AS highest_deleted_id_order FROM deleted_orders");
    $query_deleted->execute();
    $result_deleted = $query_deleted->get_result();
    $row_deleted = $result_deleted->fetch_assoc();
    $highest_deleted_id_order = $row_deleted['highest_deleted_id_order'] ?? null;

    $query_details = $conn->prepare("SELECT MAX(id_order) AS highest_id_order FROM order_details WHERE tanggal = ?");
    $query_details->bind_param('s', $tanggal);
    $query_details->execute();
    $result_details = $query_details->get_result();
    $row_details = $result_details->fetch_assoc();
    $highest_order_details_id = $row_details['highest_id_order'] ?? null;

    if ($highest_deleted_id_order || $highest_order_details_id) {
        $max_id_order = max($highest_deleted_id_order, $highest_order_details_id);
        $last_sequence = substr($max_id_order, -3);
        $new_sequence = (int)$last_sequence + 1;
        $new_id_order = date("dmy", strtotime($tanggal)) . str_pad($new_sequence, 3, '0', STR_PAD_LEFT);
    } else {
        $new_id_order = date("dmy", strtotime($tanggal)) . '001';
    }
  
    foreach ($cart_data as $item) {
        $subtotal += $item['qty'] * $item['harga'];
    }
    
    $totalCost = $subtotal + $tax; // Total keseluruhan
    
    if (!empty($cart_data)) {
        foreach ($cart_data as $item) {
            $id_masakan = $item['id'];
            $nama_masakan = $item['namaMasakan'];
            $quantity = $item['qty'];
            $price = $item['harga'];

            $sql = "INSERT INTO order_details (id_order, tanggal, id_masakan, nama_masakan, quantity, subtotal, tax, price, user_role, name, no_meja, type_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisidddssis", $new_id_order, $tanggal, $id_masakan, $nama_masakan, $quantity, $subtotal, $tax, $price, $user_role, $name, $no_meja, $type_order);
    
            if (!$stmt->execute()) {
                header("Location: cart.php?status=error&message=" . urlencode($stmt->error));
                exit;
            }
        }

        // Redirect ke cart.php dengan status sukses
        header("Location: cart.php?status=success&id_order=$new_id_order");
        exit;
    } else {
        // Redirect ke cart.php dengan status keranjang kosong
        header("Location: cart.php?status=empty");
        exit;
    }
}

mysqli_close($conn);
?>
