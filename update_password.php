<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Cek token di database
    $query = "SELECT email FROM password_resets WHERE token = '$token' AND created_at > NOW() - INTERVAL 1 HOUR";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        // Update password
        $query = "UPDATE user SET password = '$password' WHERE email = '$email'";
        mysqli_query($conn, $query);

        // Hapus token dari database
        $query = "DELETE FROM password_resets WHERE email = '$email'";
        mysqli_query($conn, $query);

        echo "Password berhasil direset. Silakan login.";
    } else {
        echo "Token tidak valid atau sudah kadaluarsa.";
    }
}
?>
