<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tangkap data form
    $username = $_POST['username'];
    $tipe_user = $_POST['tipe_user'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $password = $_POST['password'];

    // Pemetaan tipe_user ke id_level
    $role_mapping = [
        'Administrator' => 1,
        'Customer' => 2,
        'Waiter' => 3,
        'Owner' => 4,
        'Kasir' => 5,
    ];

    // Validasi tipe_user
    if (!array_key_exists($tipe_user, $role_mapping)) {
        echo "<script>alert('Tipe user tidak valid!'); window.location.href='user_table.php';</script>";
        exit;
    }

    $id_level = $role_mapping[$tipe_user];

    // Validasi email
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!'); window.location.href='user_table.php';</script>";
        exit;
    }

    // Validasi password
    if (strlen($password) < 8) {
        echo "<script>alert('Password harus minimal 8 karakter!'); window.location.href='user_table.php';</script>";
        exit;
    }

    // Cek apakah username sudah terdaftar
    $check_query = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $check_query->bind_param("s", $username);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar!'); window.location.href='user_table.php';</script>";
        exit;
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Tambahkan data ke database
    $stmt = $conn->prepare("INSERT INTO user (username, password, nama_lengkap, tipe_user, id_level) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $username, $hashed_password, $nama_lengkap, $tipe_user, $id_level);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='user_table.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='user_table.php';</script>";
    }

    $stmt->close();
    $check_query->close();
}
?>
