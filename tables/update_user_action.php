<?php
// Koneksi ke database dengan pengecekan error
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $identity = mysqli_real_escape_string($conn, $_POST['identity']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);

    // Mengambil avatar (jika ada file gambar yang diupload)
    $avatar = null;
    if (!empty($_FILES['avatar']['name'])) {
        // Menangani upload gambar
        $avatar = $_FILES['avatar']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($avatar);

        // Memastikan gambar berhasil diupload
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            // Hapus gambar lama jika ada
            $query_old = "SELECT avatar FROM user_manager WHERE id_user = '$id_user'";
            $result = mysqli_query($conn, $query_old);
            $row = mysqli_fetch_assoc($result);

            if ($row['avatar'] && file_exists($target_dir . $row['avatar'])) {
                unlink($target_dir . $row['avatar']); // Menghapus gambar lama
            }
        } else {
            echo "Gagal mengupload gambar.";
            exit; // Jika upload gagal, hentikan proses
        }
    }

    // Membuat query update
    if ($avatar) {
        // Jika ada gambar baru, update termasuk avatar
        $query = "UPDATE user_manager 
                  SET username = '$username', identity = '$identity', nama_lengkap = '$nama_lengkap', avatar = '$avatar' 
                  WHERE id_user = '$id_user'";
    } else {
        // Jika tidak ada gambar baru, update tanpa avatar
        $query = "UPDATE user_manager 
                  SET username = '$username', identity = '$identity', nama_lengkap = '$nama_lengkap' 
                  WHERE id_user = '$id_user'";
    }

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Jika pembaruan berhasil
        echo json_encode(['status' => 'success', 'message' => 'User information has been updated successfully.']);
    } else {
        // Jika pembaruan gagal
        echo json_encode(['status' => 'failed', 'message' => 'There was an issue updating the user information.']);
    }

    // Menutup koneksi database
    mysqli_close($conn);
}
?>
