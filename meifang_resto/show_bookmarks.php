<?php
session_start();
$connection = new mysqli("localhost", "root", "", "apk_kasir");

// Periksa koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

// Ambil ID pengguna dari sesi jika sudah login (ubah sesuai kebutuhan)
$user_id = $_SESSION['user_id'] ?? 1;

// Gunakan prepared statement untuk menghindari SQL Injection
$query = $connection->prepare("SELECT * FROM bookmark WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$bookmarks = [];
while ($row = $result->fetch_assoc()) {
    $bookmarks[] = $row;
}

$query->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meifang Resto - Bookmark</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<!-- Bootstrap JS (versi terbaru, sesuaikan dengan proyek Anda) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./css/footer.css">


    <style>
        .bookmark-item {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            justify-content: space-between;
        }
        .btn-danger {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-info {
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .remove-btn:hover { background-color: #e60000; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top" id="ftco-navbar">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="./logo_meifang/meifang.svg" alt="Meifang Resto">
        </a>

        <!-- Toggler Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="ftco-nav">
            <ul class="navbar-nav ml-auto d-flex align-items-center">
                <li class="nav-item "><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="#menu" class="nav-link">Menu</a></li>
                <li class="nav-item"><a href="#about" class="nav-link">About</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</a>
                    <div class="dropdown-menu" aria-labelledby="moreDropdown">
                        <a href="#" class="dropdown-item">Profile</a>
                        <a href="#" class="dropdown-item">Settings</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">Logout</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
    <button class="btn btn-light position-relative dropdown-toggle" id="cartButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge bg-danger" id="cartCount" style="color:antiquewhite">0</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;" id="cartDropdown">
        <p class="mb-2"><strong>Cart Items</strong></p>
        <div id="cartItems">
            <p class="text-muted text-center">Cart is empty</p>
        </div>
        <hr>
        <a href="cart.php" class="btn btn-danger btn-sm w-100">See all cart</a>
    </ul>
</li>


                <li class="nav-item">
                    <a href="show_bookmarks.php" class="nav-link position-relative">
                        <i class="fas fa-bookmark"></i>
                    </a>
                </li>

                 <li class="nav-item">
                    <a href="./index.php#menu" class="btn btn-danger text-white order-btn">Order Now</a>
                </li>


                <!-- Profile Image -->
				<!-- <li class="nav-item">
    <a href="#" class="nav-link d-flex align-items-center">
        <img src="./images/insta-6.jpg" alt="Profile" class="rounded-circle" width="40" height="40">
        <div class="ml-2">
            <span class="d-block"><?php echo htmlspecialchars($nama_lengkap); ?></span>
            <span class="text-muted d-block"><?php echo htmlspecialchars($username); ?></span>
        </div>
    </a>
</li> -->


            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center"> Bookmark List</h2>
    <div class="bookmark-list">
        <?php if (!empty($bookmarks)): ?>
            <?php foreach ($bookmarks as $row): ?>
                <div class="bookmark-item d-flex align-items-center justify-content-between" id="bookmark_<?= $row['id_masakan'] ?>">
                    <div>
                        <h4><?= htmlspecialchars($row['nama_masakan']) ?></h4>
                        <p>Price: Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-info btn-sm me-2" onclick="viewDetails(<?= $row['id_masakan'] ?>)">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeBookmark(<?= $row['id_masakan'] ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">Belum ada menu yang dibookmark.</p>
        <?php endif; ?>
    </div>
</div>


    <script>
       function removeBookmark(id) {
    if (confirm("Apakah Anda yakin ingin menghapus bookmark ini?")) {
        fetch("delete_bookmark.php", { // Mengubah endpoint ke delete_bookmark.php
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id_masakan=" + id
        })
        .then(response => response.text())
        .then(result => {
            if (result === "removed") {
                document.getElementById("bookmark_" + id).remove();
            } else {
                alert("Gagal menghapus bookmark.");
            }
        })
        .catch(error => console.error("Error:", error));
    }
}

    </script>
   <script>
    function loadCartDropdown() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartItemsContainer = document.getElementById("cartItems");
    let cartCount = document.getElementById("cartCount");

    cartItemsContainer.innerHTML = ""; // Kosongkan dulu
    cartCount.textContent = cart.length; // Update jumlah item di badge

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `<p class="text-muted text-center">Cart is empty</p>`;
        return;
    }

    cart.forEach((item, index) => {
        cartItemsContainer.innerHTML += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <p class="m-0"><strong>${index + 1}. ${item.namaMasakan}</strong></p> <!-- Menambahkan nomor urut -->
                    <small>${item.qty} x Rp ${(item.harga).toFixed(3).toLocaleString("id-ID")}</small> <!-- Harga dengan 3 angka desimal -->
                </div>
                <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">X</button>
            </div>
        `;
    });
}



document.addEventListener("DOMContentLoaded", loadCartDropdown);

// Pastikan juga fungsi `removeFromCart()` diperbarui untuk refresh dropdown setelah hapus item:
function removeFromCart(id) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCartDropdown(); // Refresh dropdown cart
}
    function viewDetails(id) {
        window.location.href = 'details_menu.php?id=' + id;
    }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</body>
</html>