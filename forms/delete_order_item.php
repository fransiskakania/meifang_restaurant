<?php
// Include the database connection file
include('koneksi.php');

// Check if the order ID is set and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $orderId = $_GET['id'];

    // SQL query to delete the specific order from the 'orders' table
    $query = "DELETE FROM orders WHERE id_order = $orderId";
    
    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Redirect back to the order page with a success message
        echo "<script>
                alert('Order deleted successfully!');
                window.location.href = 'detail_order.php'; // Adjust this to the correct page
              </script>";
        exit();
    } else {
        echo "Error deleting order: " . mysqli_error($conn);
    }
} else {
    echo "Invalid order ID.";
}
?>
