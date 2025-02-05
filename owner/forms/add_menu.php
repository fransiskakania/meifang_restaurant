<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "apk_kasir";

$conn = new mysqli($servername, $username, $password, $database);

// Query untuk mengambil data dari tabel masakan
$sql = "SELECT * FROM masakan";
$result = $conn->query($sql);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Periksa apakah form dikirim dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menuImage = $_FILES['menuImage']['name'];
    $menuName = $_POST['menuName'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $category = $_POST['category'];
    $stock_menu = $_POST['stock_menu'];
    $note = $_POST['note'];

    // Upload gambar
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($menuImage);

    if (move_uploaded_file($_FILES['menuImage']['tmp_name'], $targetFile)) {
        // Insert data ke tabel masakan
        $sql = "INSERT INTO masakan (image, nama_masakan, harga, status_masakan, category, stock_menu, note)
                VALUES ('$menuImage', '$menuName', '$price', '$status', '$category', '$stock_menu', '$note')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('Item berhasil ditambahkan!');
                window.location.href = 'order_menu.php';
            </script>";
        } else {
            echo "<script>
                alert('Terjadi kesalahan saat menyimpan data!');
            </script>";
        }
    } else {
        echo "<script>
            alert('Gagal mengupload gambar!');
        </script>";
    }
}

// Tutup koneksi
$conn->close();
?>
