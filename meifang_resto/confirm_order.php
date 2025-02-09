<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $user_role = $_POST['user_role'];
    $name = $_POST['name'];
    $no_meja = $_POST['no_meja'];
    $type_order = $_POST['type_order'];
    $cart_data = json_decode($_POST['cart_data'], true);

    // Generate id_order
    // Get the highest id_order from deleted_orders
    $query_deleted = $conn->prepare("SELECT MAX(id_order) AS highest_deleted_id_order FROM deleted_orders");
    $query_deleted->execute();
    $result_deleted = $query_deleted->get_result();
    $row_deleted = $result_deleted->fetch_assoc();
    $highest_deleted_id_order = $row_deleted['highest_deleted_id_order'] ?? null;

    // Get the highest id_order for the given date from order_details
    $query_details = $conn->prepare("SELECT MAX(id_order) AS highest_id_order FROM order_details WHERE tanggal = ?");
    $query_details->bind_param('s', $tanggal);
    $query_details->execute();
    $result_details = $query_details->get_result();
    $row_details = $result_details->fetch_assoc();
    $highest_order_details_id = $row_details['highest_id_order'] ?? null;

    // Determine the starting id_order
    if ($highest_deleted_id_order || $highest_order_details_id) {
        $max_id_order = max($highest_deleted_id_order, $highest_order_details_id);
        $last_sequence = substr($max_id_order, -3); // Extract last 3 digits
        $new_sequence = (int)$last_sequence + 1;    // Increment sequence
        $new_id_order = date("dmy", strtotime($tanggal)) . str_pad($new_sequence, 3, '0', STR_PAD_LEFT);
    } else {
        $new_id_order = date("dmy", strtotime($tanggal)) . '001';
    }

    // Insert each item in the cart into order_details
    if (!empty($cart_data)) {
        foreach ($cart_data as $item) {
            $id_masakan = $item['id'];
            $nama_masakan = $item['namaMasakan'];
            $quantity = $item['qty'];
            $price = $item['harga'];
        
            // Debugging: Cek apakah harga yang diterima benar
            error_log("ID: $id_masakan, Nama: $nama_masakan, Qty: $quantity, Harga: $price, Total: " . ($price * $quantity));
        
            $sql = "INSERT INTO order_details (id_order, tanggal, id_masakan, nama_masakan, quantity, price, user_role, name, no_meja, type_order) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisdsssss", $new_id_order, $tanggal, $id_masakan, $nama_masakan, $quantity, $price, $user_role, $name, $no_meja, $type_order);
        
            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
                exit;
            }
        }
        
        echo "<script>alert('Order berhasil dikonfirmasi dengan ID: $new_id_order'); window.location.href='transaction.php?id_order=$new_id_order';</script>";
    } else {
        echo "<script>alert('Keranjang kosong!'); window.location.href='cart.php';</script>";
    }
}

mysqli_close($conn);
?>
