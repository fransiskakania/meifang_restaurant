<?php
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Optional: Close the connection at the end of your scripts
// mysqli_close($conn);
?>
