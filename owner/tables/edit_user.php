<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['user_id'];
    $username = $_POST['username'];
    $tipe_user = $_POST['tipe_user'];
    $nama_lengkap = $_POST['nama_lengkap'];

    $sql = "UPDATE user SET username = ?, tipe_user = ?, nama_lengkap = ? WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $username, $tipe_user, $nama_lengkap, $id_user);

    if ($stmt->execute()) {
        header('Location: user_table.php?status=success&message=User updated successfully');
    } else {
        header('Location: user_table.php?status=error&message=Failed to update user');
    }

    $stmt->close();
    $conn->close();
}
?>
