<?php

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $identity = $_POST['identity'];
    $nama_lengkap = $_POST['nama_lengkap']; // Tangkap nilai nama_lengkap

    // Proses upload gambar
    $avatar = $_FILES['avatar']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($avatar);

    // Validasi folder upload
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
        // Insert data ke database
        $query = "INSERT INTO user_manager (username, identity, nama_lengkap, avatar) 
                  VALUES ('$username', '$identity', '$nama_lengkap', '$avatar')";
        if (mysqli_query($conn, $query)) {
            echo "Data berhasil ditambahkan!";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Gagal mengunggah gambar.";
    }
}
?>
