<?php
// Koneksi ke database
$connection = new mysqli("localhost", "root", "", "apk_kasir");

// Periksa koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

// Pastikan ID masakan dikirim melalui GET dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_masakan = $_GET['id'];

    // Gunakan prepared statement untuk keamanan
    $stmt = $connection->prepare("SELECT id_masakan, image, nama_masakan, note, harga, stock_menu FROM masakan WHERE id_masakan = ?");
    $stmt->bind_param("i", $id_masakan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("<p class='text-center text-danger mt-5'>Masakan tidak ditemukan.</p>");
    }

    $stmt->close();
} else {
    die("<p class='text-center text-danger mt-5'>ID tidak valid.</p>");
}

// Tutup koneksi database
$connection->close();
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Menu - <?= htmlspecialchars($row['nama_masakan']) ?></title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" href="css/animate.css">
	
	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	<link rel="stylesheet" href="css/magnific-popup.css">

	<link rel="stylesheet" href="css/bootstrap-datepicker.css">
	<link rel="stylesheet" href="css/jquery.timepicker.css">

	
	<link rel="stylesheet" href="css/flaticon.css">
	<link rel="stylesheet" href="css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">


		<!-- Script untuk Owl Carousel -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://owlcarousel2.github.io/OwlCarousel2/assets/owlcarousel/owl.carousel.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Tambahkan SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



  <link rel="stylesheet" href="./css/details.css">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="./logo_meifang/meifang.svg" alt="Meifang Resto">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="#menu" class="nav-link">Menu</a></li>
                    <li class="nav-item"><a href="#about" class="nav-link">About</a></li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</a>
                        <div class="dropdown-menu" aria-labelledby="moreDropdown">
                            <a href="#" class="dropdown-item">Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="dropdown">
                            <button class="btn btn-light position-relative" id="cartButton">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge bg-danger" id="cartCount">0</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3" id="cartDropdown" style="width: 270px; min-width: 250px;">
                            <p class="mb-2"><strong>Cart Items</strong></p>
                                <div id="cartItems">
                                    <p class="text-muted text-center">Cart is empty</p>
                                </div>
                                <hr>
                                <a href="cart.php" class="btn btn-primary btn-sm w-100">See all cart</a>
                            </div>
                        </div>
                    </li>
                     <li class="nav-item">
                    <a href="show_bookmarks.php" class="nav-link position-relative">
                        <i class="fas fa-bookmark"></i>
                    </a>
                </li>
                       <li class="nav-item">
                    <a href="./index.php#menu" class="btn btn-danger text-white order-btn">Order Now</a>
                </li>


                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="menu-container">
                <div class="row">
                    <!-- Image Section -->
                    <div class="col-12 col-md-6">
                        <img src="image/drinks/<?= htmlspecialchars($row['image']) ?>" 
                             alt="<?= htmlspecialchars($row['nama_masakan']) ?>" 
                             class="menu-image img-fluid">
                    </div>
                    
                    <!-- Details Section -->
                    <div class="col-12 col-md-6">
                        <h2 class="menu-title"><?= htmlspecialchars($row['nama_masakan']) ?></h2>
                        <p class="menu-price">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
                        <p><strong></strong> <?= ucfirst(htmlspecialchars($row['note'])) ?></p>

                                        <!-- Quantity Control -->
                    <div class="col-12 col-md-4">
                        <div class="quantity-control my-3 d-flex justify-content-start">
                            <button class="btn btn-outline-secondary btn-quantity rounded-circle" onclick="changeQuantity(-1)" style="width: 40px; height: 40px;">-</button>
                            <span id="quantity" class="quantity-display mx-2" style="font-size: 1rem;">0</span>
                            <button class="btn btn-outline-secondary btn-quantity rounded-circle" onclick="changeQuantity(1)" style="width: 40px; height: 40px;">+</button>
                        </div>
                    </div>

                        
                        <!-- Hidden stock value -->
                        <span id="stock" data-stock="<?= htmlspecialchars($row['stock_menu']) ?>" hidden></span>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-md-row justify-content-center">
                            <button class="btn btn-secondary mb-2 mb-md-0 me-md-2 w-100" onclick="window.location.href='menu.php'">Back</button>
                            <button class="btn btn-primary w-100" onclick="addToCart()">Add To Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function changeQuantity(amount) {
    var quantityElement = document.getElementById('quantity');
    var currentQuantity = parseInt(quantityElement.innerText);
    
    // Update the quantity based on the amount (prevent negative values if needed)
    var newQuantity = currentQuantity + amount;
    if (newQuantity >= 0) {
        quantityElement.innerText = newQuantity;
    }
}
document.getElementById("cartButton").addEventListener("click", function (e) {
    const dropdownMenu = document.getElementById("cartDropdown");
    dropdownMenu.classList.toggle("show");
});


      
</script>
<script>
 function addToCart() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let quantity = parseInt(document.getElementById("quantity").textContent);
    let namaMasakan = "<?= htmlspecialchars($row['nama_masakan']) ?>";
    let harga = <?= $row['harga'] ?>;
    let id = <?= $row['id_masakan'] ?>;
    let stock = <?= $row['stock_menu'] ?>;

    if (quantity < 1) {
        alert("Masukkan jumlah yang valid!");
        return;
    }

    let existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        if (existingItem.qty + quantity > stock) {
            alert("Stok tidak mencukupi!");
            return;
        }
        existingItem.qty += quantity;
    } else {
        if (quantity > stock) {
            alert("Stok tidak mencukupi!");
            return;
        }
        cart.push({ id, namaMasakan, harga, qty: quantity, stock });
    }

    localStorage.setItem("cart", JSON.stringify(cart));

    // Refresh halaman untuk update cart di dropdown
    location.reload();
}


function changeQuantity(amount) {
    let quantityElem = document.getElementById("quantity");
    let stock = parseInt(document.getElementById("stock").getAttribute("data-stock"));
    let currentQty = parseInt(quantityElem.textContent);
    let newQty = currentQty + amount;

    if (newQty < 0) newQty = 0;
    if (newQty > stock) {
        alert("Stok tidak mencukupi!");
        return;
    }

    quantityElem.textContent = newQty;
}

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

</script>

</body>
</html>
