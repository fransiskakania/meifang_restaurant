<?php
$connection = new mysqli("localhost", "root", "", "apk_kasir");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_masakan = $_POST['id_masakan'];
    $nama_masakan = $_POST['nama_masakan'];
    $harga = $_POST['harga'];  // Ambil harga
    $category = $_POST['category'];  // Ambil category

    $user_id = 1; // Gantilah dengan user yang sedang login

    // Periksa apakah sudah ada bookmark
    $check = $connection->query("SELECT * FROM bookmark WHERE id_masakan = $id_masakan AND user_id = $user_id");
    if ($check->num_rows > 0) {
        // Hapus jika sudah ada
        $connection->query("DELETE FROM bookmark WHERE id_masakan = $id_masakan AND user_id = $user_id");
        echo "removed";
    } else {
        // Tambah ke bookmark
        $connection->query("INSERT INTO bookmark (user_id, id_masakan, nama_masakan, harga, category) 
                            VALUES ($user_id, $id_masakan, '$nama_masakan', $harga, '$category')");
        echo "added";
    }
}
?>
