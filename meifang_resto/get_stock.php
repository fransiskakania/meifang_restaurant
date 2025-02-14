<?php
include 'koneksi2.php';

if (isset($_GET['id_masakan'])) {
    $id = intval($_GET['id_masakan']);
    $query = mysqli_query($conn, "SELECT stock_menu FROM masakan WHERE id_masakan = $id");

    if ($row = mysqli_fetch_assoc($query)) {
        echo json_encode(["stock" => intval($row['stock_menu'])]);
    } else {
        echo json_encode(["error" => "Menu tidak ditemukan"]);
    }
} else {
    echo json_encode(["error" => "ID tidak diberikan"]);
}
?>
