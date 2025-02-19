<?php
include 'koneksi.php'; // Include database connection

function showErrorAndExit($message, $redirectUrl) {
    echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
        <style>
            .swal2-popup {
                font-family: 'Poppins', sans-serif;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '$message',
                }).then(function() {
                    window.location.href = '$redirectUrl';
                });
            });
        </script>";
    exit();
}

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
            showErrorAndExit("Error: " . $conn->error, "transaction_order.php?id_order=$id_order");
        }

    } else {
        // If there's only one item, do not delete and show SweetAlert error
        showErrorAndExit("Order must have at least one item.", "transaction_order.php?id_order=$id_order&error=Order+minimum+1+item");
    }

}

$conn->close();
?>
