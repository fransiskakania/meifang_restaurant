<?php
session_start();
include 'koneksi.php'; // Include your database connection file

// Query untuk mengambil nama_lengkap berdasarkan id_user
$id_user = $_SESSION['id_user'] ?? null; // Pastikan id_user berasal dari sesi atau sumber valid
if ($id_user) {
    $sql = "SELECT nama_lengkap,username FROM user WHERE id_user = '$id_user'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_lengkap = $row['nama_lengkap'];
        $username = $row['username'];

    } else {
        $nama_lengkap = "Guest";
        $username = "Not avalaible";

    }
} else {
    $nama_lengkap = "Guest";
    $username = "Not avalaible";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Meifang Restaurant - Meifang Restaurant authentic Chinese dishes</title>
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
    <link rel="stylesheet" href="./css/footer.css">

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
                <li class="nav-item "><a href="#home" class="nav-link">Home</a></li>
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

                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn btn-light position-relative" id="cartButton">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger" id="cartCount" style="color:antiquewhite">0</span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;" id="cartDropdown">
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

	<!-- END nav -->
	
	<section class="hero-wrap" id="home">
    <div class="home-slider owl-carousel js-fullheight" data-owl-carousel>
        <div class="slider-item js-fullheight" style="background-image: url('images/banner_meifang/1.png');">
            <div class="container">
                <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center">
                    <div class="col-md-12 ftco-animate">
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-item js-fullheight" style="background-image: url('images/banner_meifang/2.png');">
            <div class="container">
                <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center">
                    <div class="col-md-12 ftco-animate">
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-item js-fullheight" style="background-image: url('images/banner_meifang/3.png');">
            <div class="container">
                <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center">
                    <div class="col-md-12 ftco-animate">
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-item js-fullheight" style="background-image: url('images/banner_meifang/4.png');">
            <div class="container">
                <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center">
                    <div class="col-md-12 ftco-animate">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

	<section class="ftco-section"id="menu">
		<div class="container">
			<div class="row justify-content-center mb-10 pb-2">
				<div class="col-md-10 text-center heading-section ftco-animate">
					<span class="subheading">Special</span>
					<h2 class="mb-4">Our Menu</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-lg-4">
				<div class="menu-wrap">
				<div class="heading-menu text-center ftco-animate">
					<h3>Main Course</h3>
				</div>

                <?php 
                // Create connection to the database
                $connection = new mysqli("localhost", "root", "", "apk_kasir");

                // Check the connection
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // Query to select the menu data for 'main_course' category
                $query = "SELECT id_masakan, image, nama_masakan, stock_menu, harga FROM masakan WHERE category = 'main_course'";
                $result = $connection->query($query);

                // If there are results, display them
                if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="menus d-flex ftco-animate">
                            <!-- Menu Image -->
                            <div class="menu-img img" style="background-image: url('image/maincourse/<?= $row['image'] ?>');"></div>
                            
                            <!-- Menu Text Section -->
                            <div class="text">
                                <!-- Menu Header (Name and Bookmark Icon) -->
                                <div class="menu-header d-flex justify-content-between align-items-center mb-2">
                                    <h3 id="nama_masakan_<?= $row['id_masakan'] ?>" class="m-0"><?= htmlspecialchars($row['nama_masakan']) ?></h3>
                                    <i class="fas fa-bookmark" onclick="bookmarkMenu(<?= $row['id_masakan'] ?>, '<?= htmlspecialchars($row['nama_masakan']) ?>', <?= $row['harga'] ?>)" style="cursor: pointer;"></i>
                                    </div>

                                <!-- Price Display -->
                                <p class="harga">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>

                                <!-- Stock Information (Hidden by default) -->
                                <p class="stock_menu" style="display: none;">
                                    Stok: <span id="stock_menu<?= $row['id_masakan'] ?>"><?= htmlspecialchars($row['stock_menu']) ?></span>
                                </p>

                                <!-- Quantity Control (Increase/Decrease) -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="quantity-control">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity(<?= $row['id_masakan'] ?>, -1)">-</button>
                                        <span id="qty_<?= $row['id_masakan'] ?>" class="quantity-display">0</span>
                                        <button 
                                            class="btn btn-sm btn-outline-secondary" 
                                            onclick="changeQuantity(<?= $row['id_masakan'] ?>, 1, '<?= $row['nama_masakan'] ?>', <?= $row['harga'] ?>)">
                                            +
                                        </button>
                                    </div>
                                    <!-- Link to the details page -->
                                    <a href="details_menu.php?id=<?= $row['id_masakan'] ?>" class="details-text" style="margin-left: auto;">Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Tidak ada menu tersedia.</p>
                <?php endif; ?>

                <?php 
                // Close the database connection
                $connection->close(); 
                ?>



				<span class="flat flaticon-bread" style="left: 0;"></span>
				<span class="flat flaticon-breakfast" style="right: 0;"></span>


				</div>
				</div>
				<!-- snack -->
				<div class="col-md-6 col-lg-4">
				<div class="menu-wrap">
						<div class="heading-menu text-center ftco-animate">
							<h3>Snack</h3>
						</div>

				
						<?php 
				$connection = new mysqli("localhost", "root", "", "apk_kasir");

				// Periksa koneksi
				if ($connection->connect_error) {
					die("Koneksi gagal: " . $connection->connect_error);
				}

				$query = "SELECT id_masakan, image, nama_masakan, stock_menu, harga FROM masakan WHERE category = 'snack'";
				$result = $connection->query($query);
				if ($result->num_rows > 0): ?>
					<?php while ($row = $result->fetch_assoc()): ?>
						<div class="menus d-flex ftco-animate">
							<div class="menu-img img" style="background-image: url('image/snack/<?= $row['image'] ?>');"></div>
							<div class="text">
								<h3><?= htmlspecialchars($row['nama_masakan']) ?></h3>
								<p class="harga">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
								<p class="stock_menu" style="display: none;">
									Stok: <span id="stock_menu<?= $row['id_masakan'] ?>"><?= htmlspecialchars($row['stock_menu']) ?></span>
								</p>
								<!-- Section Increase & Decrease -->
								<div class="quantity-control">
									<button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity(<?= $row['id_masakan'] ?>, -1)">-</button>
									<span id="qty_<?= $row['id_masakan'] ?>" class="quantity-display">0</span>
									<button 
										class="btn btn-sm btn-outline-secondary" 
										onclick="changeQuantity(<?= $row['id_masakan'] ?>, 1, '<?= $row['nama_masakan'] ?>', <?= $row['harga'] ?>)">
										+
									</button>
									<a href="details_menu_snack.php?id=<?= $row['id_masakan'] ?>" class="details-text ml-3">Details</a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php else: ?>
					<p>Tidak ada menu tersedia.</p>
				<?php endif; ?>
				<?php $connection->close(); ?>
					
						<span class="flat flaticon-bread" style="left: 0;"></span>
						<span class="flat flaticon-breakfast" style="right: 0;"></span>
					</div>
				</div>
				<!-- dessert -->
				<div class="col-md-6 col-lg-4">
				<div class="menu-wrap">
						<div class="heading-menu text-center ftco-animate">
							<h3>Dessert</h3>
						</div>

						<?php 
				$connection = new mysqli("localhost", "root", "", "apk_kasir");

				// Periksa koneksi
				if ($connection->connect_error) {
					die("Koneksi gagal: " . $connection->connect_error);
				}

				$query = "SELECT id_masakan, image, nama_masakan, stock_menu, harga FROM masakan WHERE category = 'dessert'";
				$result = $connection->query($query);
				if ($result->num_rows > 0): ?>
					<?php while ($row = $result->fetch_assoc()): ?>
						<div class="menus d-flex ftco-animate">
							<div class="menu-img img" style="background-image: url('image/dessert/<?= $row['image'] ?>');"></div>
							<div class="text">
								<h3><?= htmlspecialchars($row['nama_masakan']) ?></h3>
								<p class="harga">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
								<p class="stock_menu" style="display: none;">
									Stok: <span id="stock_menu<?= $row['id_masakan'] ?>"><?= htmlspecialchars($row['stock_menu']) ?></span>
								</p>
								<!-- Section Increase & Decrease -->
								<div class="quantity-control">
									<button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity(<?= $row['id_masakan'] ?>, -1)">-</button>
									<span id="qty_<?= $row['id_masakan'] ?>" class="quantity-display">0</span>
									<button 
										class="btn btn-sm btn-outline-secondary" 
										onclick="changeQuantity(<?= $row['id_masakan'] ?>, 1, '<?= $row['nama_masakan'] ?>', <?= $row['harga'] ?>)">
										+
									</button>
									<a href="details_menu_dessert.php?id=<?= $row['id_masakan'] ?>" class="details-text ml-3">Details</a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php else: ?>
					<p>Tidak ada menu tersedia.</p>
				<?php endif; ?>
				<?php $connection->close(); ?>
					
						<span class="flat flaticon-bread" style="left: 0;"></span>
						<span class="flat flaticon-breakfast" style="right: 0;"></span>
					</div>
				</div>
				<!-- non coffe -->
				<div class="col-md-6 col-lg-4">
				<div class="menu-wrap">
						<div class="heading-menu text-center ftco-animate">
							<h3>Non Coffe</h3>
						</div>

						<?php 
				$connection = new mysqli("localhost", "root", "", "apk_kasir");

				// Periksa koneksi
				if ($connection->connect_error) {
					die("Koneksi gagal: " . $connection->connect_error);
				}

				$query = "SELECT id_masakan, image, nama_masakan, stock_menu, harga FROM masakan WHERE category = 'drink'";
				$result = $connection->query($query);
				if ($result->num_rows > 0): ?>
					<?php while ($row = $result->fetch_assoc()): ?>
						<div class="menus d-flex ftco-animate">
							<div class="menu-img img" style="background-image: url('image/drinks/<?= $row['image'] ?>');"></div>
							<div class="text">
								<h3><?= htmlspecialchars($row['nama_masakan']) ?></h3>
								<p class="harga">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
								<p class="stock_menu" style="display: none;">
									Stok: <span id="stock_menu<?= $row['id_masakan'] ?>"><?= htmlspecialchars($row['stock_menu']) ?></span>
								</p>
								<!-- Section Increase & Decrease -->
								<div class="quantity-control">
									<button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity(<?= $row['id_masakan'] ?>, -1)">-</button>
									<span id="qty_<?= $row['id_masakan'] ?>" class="quantity-display">0</span>
									<button 
										class="btn btn-sm btn-outline-secondary" 
										onclick="changeQuantity(<?= $row['id_masakan'] ?>, 1, '<?= $row['nama_masakan'] ?>', <?= $row['harga'] ?>)">
										+
									</button>
									<a href="details_menu_drink.php?id=<?= $row['id_masakan'] ?>" class="details-text ml-3">Details</a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php else: ?>
					<p>Tidak ada menu tersedia.</p>
				<?php endif; ?>
				<?php $connection->close(); ?>
					
						<span class="flat flaticon-bread" style="left: 0;"></span>
						<span class="flat flaticon-breakfast" style="right: 0;"></span>
					</div>
				</div>
				<!-- coffe n tea -->
				<div class="col-md-6 col-lg-4">
				<div class="menu-wrap">
						<div class="heading-menu text-center ftco-animate">
							<h3>Coffe & Tea</h3>
						</div>

						<?php 
				$connection = new mysqli("localhost", "root", "", "apk_kasir");

				// Periksa koneksi
				if ($connection->connect_error) {
					die("Koneksi gagal: " . $connection->connect_error);
				}

				$query = "SELECT id_masakan, image, nama_masakan, stock_menu, harga FROM masakan WHERE category = 'coffentea'";
				$result = $connection->query($query);
				if ($result->num_rows > 0): ?>
					<?php while ($row = $result->fetch_assoc()): ?>
						<div class="menus d-flex ftco-animate">
							<div class="menu-img img" style="background-image: url('image/coffentea/<?= $row['image'] ?>');"></div>
							<div class="text">
								<h3><?= htmlspecialchars($row['nama_masakan']) ?></h3>
								<p class="harga">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
								<p class="stock_menu" style="display: none;">
									Stok: <span id="stock_menu<?= $row['id_masakan'] ?>"><?= htmlspecialchars($row['stock_menu']) ?></span>
								</p>
								<!-- Section Increase & Decrease -->
								<div class="quantity-control">
									<button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity(<?= $row['id_masakan'] ?>, -1)">-</button>
									<span id="qty_<?= $row['id_masakan'] ?>" class="quantity-display">0</span>
									<button 
										class="btn btn-sm btn-outline-secondary" 
										onclick="changeQuantity(<?= $row['id_masakan'] ?>, 1, '<?= $row['nama_masakan'] ?>', <?= $row['harga'] ?>)">
										+
									</button>
									<a href="details_menu_coffe.php?id=<?= $row['id_masakan'] ?>" class="details-text ml-3">Details</a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php else: ?>
					<p>Tidak ada menu tersedia.</p>
				<?php endif; ?>
				<?php $connection->close(); ?>
					
						<span class="flat flaticon-bread" style="left: 0;"></span>
						<span class="flat flaticon-breakfast" style="right: 0;"></span>
					</div>
				</div>
				<!-- milk -->
				<div class="col-md-6 col-lg-4">
				<div class="menu-wrap">
						<div class="heading-menu text-center ftco-animate">
							<h3>Milks & Smothies </h3>
						</div>

						<?php 
				$connection = new mysqli("localhost", "root", "", "apk_kasir");

				// Periksa koneksi
				if ($connection->connect_error) {
					die("Koneksi gagal: " . $connection->connect_error);
				}

				$query = "SELECT id_masakan, image, nama_masakan, stock_menu, harga FROM masakan WHERE category = 'milks'";
				$result = $connection->query($query);
				if ($result->num_rows > 0): ?>
					<?php while ($row = $result->fetch_assoc()): ?>
						<div class="menus d-flex ftco-animate">
							<div class="menu-img img" style="background-image: url('image/milks/<?= $row['image'] ?>');"></div>
							<div class="text">
								<h3><?= htmlspecialchars($row['nama_masakan']) ?></h3>
								<p class="harga">Rp <?= number_format($row['harga'], 3, ',', '.') ?></p>
								<p class="stock_menu" style="display: none;">
									Stok: <span id="stock_menu<?= $row['id_masakan'] ?>"><?= htmlspecialchars($row['stock_menu']) ?></span>
								</p>
								<!-- Section Increase & Decrease -->
								<div class="quantity-control">
									<button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity(<?= $row['id_masakan'] ?>, -1)">-</button>
									<span id="qty_<?= $row['id_masakan'] ?>" class="quantity-display">0</span>
									<button 
										class="btn btn-sm btn-outline-secondary" 
										onclick="changeQuantity(<?= $row['id_masakan'] ?>, 1, '<?= $row['nama_masakan'] ?>', <?= $row['harga'] ?>)">
										+
									</button>
									<a href="details_menu_milks.php?id=<?= $row['id_masakan'] ?>" class="details-text ml-3">Details</a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php else: ?>
					<p>Tidak ada menu tersedia.</p>
				<?php endif; ?>
				<?php $connection->close(); ?>
					
					
						<span class="flat flaticon-bread" style="left: 0;"></span>
						<span class="flat flaticon-breakfast" style="right: 0;"></span>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="ftco-section ftco-intro" style="background-image: url(images/chinese/chinese.png);">
		<div class=""></div>
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<span>Authentic Flavors, Every Bite</span>
					<h2>Private Dinners &amp; Happy Hours</h2>
				</div>
			</div>
		</div>
	</section>
	<section class="ftco-section ftco-wrap-about ftco-no-pb ftco-no-pt" id="about">
    <div class="container">
        <div class="row no-gutters">
          
            <!-- Kolom untuk bagian About -->
            <div class="col-lg-12 wrap-about py-5 ftco-animate img" style="background-image: url(images/about.jpg);">
                <div class="row pb-10 pb-md-0">
                    <div class="col-md-12 col-lg-7">
                        <div class="heading-section mt-5 mb-4">
                            <div class="pl-lg-3 ml-md-5">
                                <span class="subheading">About Us</span>
                                <h2 class="mb-4">Meifang Resto</h2>
                            </div>
                        </div>
                        <div class="pl-lg-6 ml-md-5">
                            <p> At Meifang Resto, we bring the rich flavors and traditions of Chinese cuisine to your table, using the freshest ingredients. From classic favorites like Wonton and Dim Sum to new delights like Bebek Peking and Hot Pot, our diverse menu caters to every craving. We pride ourselves on quality, service, and creating a warm, inviting atmosphere for you to enjoy with family, friends, or colleagues. Each dish is expertly prepared for a memorable dining experience that keeps you coming back.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


	<footer class="ftco-footer ftco-no-pb ftco-section">
	<footer class="pt-10 pb-7 fade-in">
    <div class="container text-center text-md-left">
        <div class="row text-center text-md-left">
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Meifang Restaurant</h5>
                <p>At Meifang Resto, we bring you the true flavors of China with every dish.Come join us and explore the best of Chinese cuisine today!</p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Quick Links</h5>
                <p><a href="#">Home</a></p>
                <p><a href="#menu">Menu</a></p>
                <p><a href="#about">About Us</a></p>
            </div>

            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3 social-icons">
                <h5 class="text-uppercase mb-4 font-weight-bold">Follow Us</h5>
                <a href="#" class="me-3"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
                <a href="#" class="me-3"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#"><i class="fab fa-youtube fa-lg"></i></a>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Contact Us</h5>
                <p><i class="fas fa-map-marker-alt me-2"></i> 123 Main Street, City, Country</p>
                <p><i class="fas fa-envelope me-2"></i> info@meifangresto.com</p>
                <p><i class="fas fa-phone me-2"></i> +123 456 789</p>
            </div>
        </div>

        <hr class="my-3">

        <div class="row d-flex justify-content-center">
            <div class="col-md-7 col-lg-8">
                <p class="text-center">&copy; 2024 Meifang Restaurant. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Add FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- Add FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


	
		

		<!-- loader -->
		<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


		<script src="js/jquery.min.js"></script>
		<script src="js/jquery-migrate-3.0.1.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.easing.1.3.js"></script>
		<script src="js/jquery.waypoints.min.js"></script>
		<script src="js/jquery.stellar.min.js"></script>
		<script src="js/owl.carousel.min.js"></script>
		<script src="js/jquery.magnific-popup.min.js"></script>
		<script src="js/jquery.animateNumber.min.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="js/jquery.timepicker.min.js"></script>
		<script src="js/scrollax.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
		<script src="js/google-map.js"></script>
		<script src="js/main.js"></script>
		<script>
// Mengatur navbar untuk menyembunyikan saat scroll
let prevScrollPos = window.pageYOffset;
window.onscroll = function () {
    let currentScrollPos = window.pageYOffset;
    if (prevScrollPos > currentScrollPos) {
        document.getElementById("ftco-navbar").classList.remove("hide");
    } else {
        document.getElementById("ftco-navbar").classList.add("hide");
    }
    prevScrollPos = currentScrollPos;
};


</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
	updateCartDropdown();
    syncCartWithUI();
    const navLinks = document.querySelectorAll("#ftco-navbar .nav-item .nav-link");

    navLinks.forEach(link => {
        link.addEventListener("click", function () {
            // Hapus kelas 'active' dari semua nav-item
            navLinks.forEach(nav => nav.parentElement.classList.remove("active"));

            // Tambahkan kelas 'active' ke link yang diklik
            this.parentElement.classList.add("active");
        });
    });
});
function syncCartWithUI() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    cart.forEach(item => {
        let qtyElement = document.getElementById("qty_" + item.id);
        if (qtyElement) {
            qtyElement.innerText = item.qty;
        }
    });
}

</script>
<script>
 function changeQuantity(id, amount, namaMasakan = "", harga = 0) {
    let qtyElement = document.getElementById("qty_" + id);
    let stockElement = document.getElementById("stock_menu" + id);

    if (!qtyElement || !stockElement) {
        console.error("Element tidak ditemukan: qty_" + id + " atau stock_menu" + id);
        return;
    }

    let currentQty = parseInt(qtyElement.innerText) || 0;
    let stock = parseInt(stockElement.innerText) || 0;
    let newQty = currentQty + amount;

    if (newQty < 0) {
        Swal.fire({
            title: "Error!",
            text: "Jumlah item tidak boleh kurang dari 0!",
            icon: "error",
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    if (newQty > stock) {
        Swal.fire({
            title: "Stok Habis!",
            text: `Stok untuk ${namaMasakan} hanya tersedia ${stock} item.`,
            icon: "warning",
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    qtyElement.innerText = newQty;

    // Perbarui localStorage
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let itemIndex = cart.findIndex(item => item.id === id);

    if (itemIndex !== -1) {
        cart[itemIndex].qty = newQty;
        if (newQty === 0) cart.splice(itemIndex, 1);
    } else if (newQty > 0) {
        cart.push({ id, namaMasakan, harga, qty: newQty, stock });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartDropdown();
}

function updateCartDropdown() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartItemsContainer = document.getElementById("cartItems");
    let cartCount = document.getElementById("cartCount");

    cartItemsContainer.innerHTML = "";
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `<p class="text-muted text-center">Cart is empty</p>`;
        cartCount.textContent = "0";
        return;
    }

    cartCount.textContent = cart.length; // Update jumlah item di badge

    cart.forEach((item, index) => {
        let cartItem = document.createElement("div");
        cartItem.classList.add("d-flex", "justify-content-between", "align-items-center", "mb-2");
        cartItem.innerHTML = `
            <div>
                <p class="mb-0"><strong>${index + 1}. ${item.namaMasakan}</strong></p>  <!-- Tambahkan nomor urut -->
                    <small>${item.qty} x Rp ${(item.harga).toFixed(3).toLocaleString("id-ID")}</small> <!-- Harga dengan 3 angka desimal -->
            </div>
            <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id}, true)">X</button>
        `;
        cartItemsContainer.appendChild(cartItem);
    });
}

// Pastikan fungsi ini dipanggil setiap kali halaman dimuat
document.addEventListener("DOMContentLoaded", updateCartDropdown);

// Modifikasi fungsi removeFromCart agar juga memperbarui dropdown
function removeFromCart(id, fromDropdown = false) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartDropdown();
    
    if (!fromDropdown) {
        loadCart(); // Hanya panggil ini jika di cart.php
    }
}

function addToCart(id, namaMasakan, harga, newQty, stock) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let itemIndex = cart.findIndex(item => item.id === id);

    if (itemIndex !== -1) {
        // Perbarui quantity jika item sudah ada di cart
        if (newQty > stock) {
            return; // Tidak menambahkan lebih dari stok tersedia
        }
        if (newQty === 0) {
            cart.splice(itemIndex, 1); // Hapus item jika quantity 0
        } else {
            cart[itemIndex].qty = newQty;
        }
    } else {
        // Tambahkan item baru ke cart jika belum ada
        if (newQty > 0) {
            cart.push({ id, namaMasakan, harga, qty: newQty, stock });
        }
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartDropdown();
}

</script>

<script>
function bookmarkMenu(id, nama, harga) {
    let formData = new FormData();
    formData.append("id_masakan", id);
    formData.append("nama_masakan", nama);
    formData.append("harga", harga);  // Tambahkan harga

    fetch("bookmark.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result === "added") {
            alert(nama + " (Rp " + harga + ") ditambahkan ke bookmark.");
        } else if (result === "removed") {
            alert(nama + " (Rp " + harga + ") dihapus dari bookmark.");
        }
    })
    .catch(error => console.error("Error:", error));
}

</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('a[href^="./index.php#menu"]').forEach(anchor => {
        anchor.addEventListener("click", function(event) {
            event.preventDefault(); // Mencegah redirect langsung

            const target = document.querySelector("#menu");
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth"
                });
            }
        });
    });
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(window).on("scroll", function () {
        var scrollPos = $(document).scrollTop();
        $(".navbar-nav .nav-link").each(function () {
            var section = $(this).attr("href");
            if ($(section).length) {
                var sectionTop = $(section).offset().top - 80; // Sesuaikan offset jika perlu
                var sectionHeight = $(section).outerHeight();
                
                if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                    $(".navbar-nav .nav-item").removeClass("active");
                    $(this).parent().addClass("active");
                }
            }
        });
    });
});
</script>


	</body>
	</html>