<?php
include 'koneksi.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the id_detail and id_order from POST request
    $id_detail = $_POST['id_detail'];
    $id_order = $_POST['id_order']; // Assume id_order is passed in POST request

    // Check if there are more than one item for the same id_order
    $check_sql = "SELECT COUNT(*) as count FROM order_details WHERE id_order = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id_order);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 1) {
        // If there are more than one item, proceed with the delete
        $sql = "DELETE FROM order_details WHERE id_detail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_detail);

        if ($stmt->execute()) {
            // Redirect back to the transaction page
            header("Location: transaction_order.php?id_order=$id_order");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        // If there's only one item, do not delete and show alert
        echo "
        <script>
            alert('Order must have at least one item.');
            window.location.href = 'transaction_order.php?id_order=$id_order&error=Order+minimum+1+item';
        </script>
        ";
    }

    $check_stmt->close();
}

$conn->close();
?>
 