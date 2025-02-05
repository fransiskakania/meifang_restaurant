<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // Cek apakah username ada di database
        $query = "SELECT * FROM user WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Generate token unik
            $token = bin2hex(random_bytes(32));

            // Simpan token di tabel password_resets
            $query = "INSERT INTO password_resets (username, token) VALUES ('$username', '$token')";
            mysqli_query($conn, $query);

            // Dapatkan email terkait username
            $row = mysqli_fetch_assoc($result);
            $email = $row['username']; // asumsikan username adalah email

            // Kirim email reset password
            // $resetLink = "http://192.168.1.100/meifang_resto_admin/reset_password.php?token=$token";
            // mail($email, "Reset Password", "Klik tautan ini untuk reset password Anda: $resetLink");

            // Menampilkan alert sukses dan redirect
            echo "<script>
                    alert('Tautan reset password telah dikirim ke email yang terdaftar.');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            // Menampilkan alert error dan redirect
            echo "<script>
                    alert('Username tidak ditemukan.');
                    window.location.href = 'login.php';
                  </script>";
        }
    } else {
        // Menampilkan alert error dan redirect
        echo "<script>
                alert('Username tidak dikirimkan.');
                window.location.href = 'login.php';
              </script>";
    }
}
?>
