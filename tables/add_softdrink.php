<?php
include 'koneksi2.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_masakan = $_POST['menuName'];
    $harga = $_POST['menuPrice'];
    $status_masakan = $_POST['menuStatus'];
    $stock_menu = $_POST['menuStock'];
    $note = $_POST['menuNote'];
    $category = "soft_drink"; // Set category to Soft Drink

    // Handle image upload
    $image = $_FILES['menuImage']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES["menuImage"]["tmp_name"], $target_file);

    $query = "INSERT INTO masakan (nama_masakan, harga, status_masakan, stock_menu, note, category, image) 
              VALUES ('$nama_masakan', '$harga', '$status_masakan', '$stock_menu', '$note', '$category', '$image')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: daftar_menu.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
