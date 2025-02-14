<?php
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke Jakarta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = date("Y-m-d H:i:s"); // Format tanggal dengan waktu
    $user_role = $_POST['user_role'];
    $name = $_POST['name'];
    $no_meja = $_POST['no_meja'];
    $type_order = $_POST['type_order'];

    // Get the highest id_order from deleted_orders
    $query_deleted = $conn->prepare("SELECT MAX(id_order) AS highest_deleted_id_order FROM deleted_orders");
    $query_deleted->execute();
    $result_deleted = $query_deleted->get_result();
    $row_deleted = $result_deleted->fetch_assoc();

    $highest_deleted_id_order = $row_deleted['highest_deleted_id_order'] ?? null;

    // Get the highest id_order for the given date from order_details
    $query_details = $conn->prepare("SELECT MAX(id_order) AS highest_id_order FROM order_details WHERE DATE(tanggal) = DATE(?)");
    $query_details->bind_param('s', $tanggal);
    $query_details->execute();
    $result_details = $query_details->get_result();
    $row_details = $result_details->fetch_assoc();

    $highest_order_details_id = $row_details['highest_id_order'] ?? null;

    // Determine the starting id_order
    if ($highest_deleted_id_order || $highest_order_details_id) {
        // Compare the highest ids and use the larger one
        $max_id_order = max($highest_deleted_id_order, $highest_order_details_id);

        $last_sequence = substr($max_id_order, -3); // Extract last 3 digits
        $new_sequence = (int)$last_sequence + 1;    // Increment sequence
        $new_id_order = date("dmy", strtotime($tanggal)) . str_pad($new_sequence, 3, '0', STR_PAD_LEFT);
    } else {
        // Start from 001 if no orders exist in both tables
        $new_id_order = date("dmy", strtotime($tanggal)) . '001';
    }

    // Proceed with inserting data into the database or processing orders
    $conn->begin_transaction(); // Start a transaction

    try {
        $query_order_items = "SELECT * FROM orders";
        $result_order_items = $conn->query($query_order_items);

        if ($result_order_items->num_rows > 0) {
            while ($row = $result_order_items->fetch_assoc()) {
                $id_masakan = $row['id_masakan'];
                $nama_masakan = $row['nama_masakan'];
                $price = $row['price'];
                $quantity = $row['quantity'];

                // Check stock availability
                $query_stock = $conn->prepare("SELECT stock_menu FROM masakan WHERE id_masakan = ?");
                $query_stock->bind_param('i', $id_masakan);
                $query_stock->execute();
                $result_stock = $query_stock->get_result();

                if ($result_stock->num_rows > 0) {
                    $stock_data = $result_stock->fetch_assoc();
                    $stock_menu = $stock_data['stock_menu'];

                    if ($stock_menu >= $quantity) {
                        // Reduce stock
                        $update_stock = $conn->prepare("UPDATE masakan SET stock_menu = stock_menu - ? WHERE id_masakan = ?");
                        $update_stock->bind_param('ii', $quantity, $id_masakan);
                        $update_stock->execute();

                        // Insert into order_details
                        $insert_detail = $conn->prepare("
                            INSERT INTO order_details 
                            (id_order, id_masakan, tanggal, nama_masakan, quantity, price, user_role, name, no_meja, type_order)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");

                        // Bind parameters using references
                        $insert_detail->bind_param(
                            'sissidssss',
                            $new_id_order,
                            $id_masakan,
                            $tanggal, // Sekarang tanggal mencakup jam, menit, dan detik
                            $nama_masakan,
                            $quantity,
                            $price,
                            $user_role,
                            $name,
                            $no_meja,
                            $type_order
                        );
                        $insert_detail->execute();
                    } else {
                        throw new Exception("Stock tidak mencukupi untuk $nama_masakan.");
                    }
                } else {
                    throw new Exception("Item tidak ditemukan: $nama_masakan.");
                }
            }

            // Delete data from 'orders' table
            $delete_orders = $conn->query("DELETE FROM orders");

            $conn->commit(); // Commit transaction
            header("Location: detail_order.php?status=success");
            exit();
        } else {
            throw new Exception("Tidak ada pesanan untuk diproses.");
        }
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction
        header("Location: detail_order.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
