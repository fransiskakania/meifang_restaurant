<?php
include 'koneksi.php'; // Pastikan koneksi database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['menuId'];
    $name = $_POST['menuName'];
    $price = $_POST['menuPrice'];
    $status = $_POST['menuStatus'];
    $category = $_POST['menuCategory'];
    $stock = $_POST['menuStock'];
    $note = isset($_POST['menuNote']) ? $_POST['menuNote'] : ''; // Jika tidak ada catatan, set sebagai string kosong

    $imageQuery = '';
    if (!empty($_FILES['menuImage']['name'])) {
        $image = $_FILES['menuImage']['name'];
        $target = "uploads/" . basename($image);
    
        // Pindahkan file ke folder tujuan
        if (move_uploaded_file($_FILES['menuImage']['tmp_name'], $target)) {
            $imageQuery = "image='$image', ";
        } else {
            echo "<script>
                alert('Gagal mengunggah gambar.');
                window.location.href = 'daftar_menu.php';
            </script>";
            exit;
        }
    } else {
        // Gunakan gambar lama jika tidak ada file baru
        $currentImageQuery = "SELECT image FROM masakan WHERE id_masakan='$id'";
        $currentImageResult = mysqli_query($conn, $currentImageQuery);
        $currentImage = mysqli_fetch_assoc($currentImageResult)['image'];
        $imageQuery = "image='$currentImage', ";
    }

    // Validasi input
    if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
        echo "<script>
            alert('Harga tidak valid.');
            window.location.href = 'daftar_menu.php';
        </script>";
        exit;
    }

    if (!filter_var($stock, FILTER_VALIDATE_INT) || $stock < 0) {
        echo "<script>
            alert('Stok tidak valid.');
            window.location.href = 'daftar_menu.php';
        </script>";
        exit;
    }

    // Query update
    $query = "UPDATE masakan 
              SET $imageQuery 
                  nama_masakan='$name', 
                  harga='$price', 
                  status_masakan='$status', 
                  category='$category', 
                  stock_menu='$stock', 
                  note='$note' 
              WHERE id_masakan='$id'";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data menu berhasil diperbarui.'
    ]);
} else {
    $error_message = mysqli_real_escape_string($conn, mysqli_error($conn));
    echo json_encode([
        'status' => 'error',
        'message' => "Data menu gagal diperbarui: $error_message"
    ]);
}

    // Tutup koneksi
    mysqli_close($conn);
} else {
    // Alert untuk metode pengiriman tidak valid
    echo "<script>
        alert('Metode pengiriman tidak valid.');
        window.location.href = 'daftar_menu.php';
    </script>";
}
?>
