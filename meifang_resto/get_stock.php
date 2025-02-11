<?php
require 'koneksi.php'; // Pastikan file koneksi database sudah benar

if (isset($_GET['id_masakan'])) {
    $id_masakan = intval($_GET['id_masakan']);

    $query = "SELECT stock_menu FROM masakan WHERE id_masakan = $id_masakan";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo $row['stock']; // Menampilkan stok langsung dengan echo
    } else {
        echo "Data tidak ditemukan";
    }
} else {
    echo "ID tidak diberikan";
}
?>
