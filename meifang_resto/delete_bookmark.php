<?php
$connection = new mysqli("localhost", "root", "", "apk_kasir");

// Periksa koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

// Periksa apakah request berasal dari POST dan memiliki id_masakan
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_masakan'])) {
    $id_masakan = intval($_POST['id_masakan']);
    $user_id = 1; // Gantilah dengan ID user yang sedang login

    $delete_query = "DELETE FROM bookmark WHERE id_masakan = ? AND user_id = ?";
    $stmt = $connection->prepare($delete_query);
    $stmt->bind_param("ii", $id_masakan, $user_id);

    if ($stmt->execute()) {
        echo "removed";
    } else {
        echo "error";
    }

    $stmt->close();
    $connection->close();
    exit;
}
?>
