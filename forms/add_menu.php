<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "apk_kasir";

$conn = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk menampilkan alert dengan SweetAlert2
function showAlert($type, $title, $message, $redirectUrl = null) {
    echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
        <style>
            .swal2-popup {
                font-family: 'Poppins', sans-serif;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$type',
                    title: '$title',
                    text: '$message',
                }).then(function() {
                    " . ($redirectUrl ? "window.location.href = '$redirectUrl';" : "") . "
                });
            });
        </script>";
    exit();
}

// Periksa apakah form dikirim dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $menuImage = $_FILES['menuImage']['name'];
    $menuName = mysqli_real_escape_string($conn, $_POST['menuName']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock_menu = mysqli_real_escape_string($conn, $_POST['stock_menu']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // Pastikan ada file yang diunggah sebelum diproses
    if (!empty($menuImage)) {
        // Upload gambar
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($menuImage);

        if (move_uploaded_file($_FILES['menuImage']['tmp_name'], $targetFile)) {
            // Masukkan data ke tabel masakan
            $sql = "INSERT INTO masakan (image, nama_masakan, harga, status_masakan, category, stock_menu, note) 
                    VALUES ('$menuImage', '$menuName', '$price', '$status', '$category', '$stock_menu', '$note')";

            if ($conn->query($sql) === TRUE) {
                showAlert('success', 'Success!', 'The item has been added successfully.', 'order_menu.php');
            } else {
                showAlert('error', 'Oops...', 'There was an error saving the data.');
            }
        } else {
            showAlert('error', 'Oops...', 'There was an error uploading the image.');
        }
    } else {
        showAlert('error', 'Oops...', 'Please upload an image.');
    }
}

// Tutup koneksi database


// Fungsi untuk render menu berdasarkan kategori
function renderMenuItems($category, $folder, $conn) {
    $query = "SELECT * FROM masakan WHERE category = '$category'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '
            <div class="col-12 col-sm-6 col-md-3 col-lg-2 mb-2 portfolio-item filter-' . $category . '">
                <div class="card h-90 text-center border-light position-relative" 
                    data-id-masakan="' . $row['id_masakan'] . '" data-stock-menu="' . $row['stock_menu'] . '">
                    <img src="uploads/' . $row['image'] . '" alt="menuimage" class="card-img-top menu-image">
                    <div class="c-body position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white portfolio-info">
                        <div>
                            <h4 style="font-size: 16px; margin-top: -15px; margin-bottom: 2px;">' . htmlspecialchars($row['nama_masakan']) . '</h4>
                            <p style="font-size: 14px; margin: -2px 0 2px 0;">Rp ' . number_format($row['harga'], 3, ',', '.') . '</p>
                        </div>
                    </div>
                    <!-- Quantity control icons -->
                    <div class="quantity-control position-absolute d-flex align-items-center justify-content-center" 
                        style="bottom: 10px; left: 50%; transform: translateX(-50%);">
                        <button onclick="decreaseQuantity(this)">-</button>
                        <span id="quantity" style="color: black;">0</span>
                        <button onclick="increaseQuantity(this)">+</button>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<p>Tidak ada menu tersedia untuk kategori ini.</p>';
    }
}

// Render items untuk setiap kategori
renderMenuItems('main_course', 'maincourse', $conn);
renderMenuItems('snack', 'snack', $conn);
renderMenuItems('dessert', 'dessert', $conn);
renderMenuItems('drink', 'drinks', $conn);
renderMenuItems('coffentea', 'coffentea', $conn);
renderMenuItems('milks', 'milks', $conn);

// Tutup koneksi
$conn->close();
?>
