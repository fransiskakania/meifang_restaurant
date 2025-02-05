<?php
// Include database connection
include 'koneksi.php';

// Check if id_user is set in the URL
if (isset($_GET['id_user'])) {
    $id_user = intval($_GET['id_user']); // Sanitize input

    // Delete query
    $sql = "DELETE FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: user_table.php?status=success");
    } else {
        // Redirect with error message
        header("Location: user_table.php?status=error");
    }

    $stmt->close();
}
$conn->close();
?>
