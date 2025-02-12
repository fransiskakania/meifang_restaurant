<?php
session_start();
include 'koneksi.php'; // Include your database connection file

// Pastikan session id_user ada sebelum mengaksesnya
if (!isset($_SESSION['id_user'])) {
  echo "<script>alert('Anda belum login!'); window.location.href='../login.php';</script>";
  exit(); // Hentikan eksekusi script
}

// Ambil id_user dari session
$id_user = $_SESSION['id_user'];

// Query untuk mengambil data pengguna berdasarkan id_user
$sql = "SELECT id_user, username, nama_lengkap, id_level FROM user WHERE id_user = '$id_user'";
$result = $conn->query($sql);

// Jika query gagal atau tidak ada hasil, tampilkan error dan redirect
if (!$result || $result->num_rows == 0) {
  echo "<script>alert('Error: Id User tidak ditemukan!'); window.location.href='../login.php';</script>";
  exit();
}

// Ambil data pengguna
$row = $result->fetch_assoc();
$nama_lengkap = $row['nama_lengkap'];
$username = $row['username'];
$id_level = $row['id_level'];



?>


<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <title>Meifang Resto - Transaction Order </title>
      <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport"
      />
      <link
        rel="icon"
        href="../assets/img/meifang_resto_logo/2.svg"
        type="image/x-icon"
      />

      <!-- Fonts and icons -->
      <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

      <script>
        WebFont.load({
          google: { families: ["Public Sans:300,400,500,600,700"] },
          custom: {
            families: [
              "Font Awesome 5 Solid",
              "Font Awesome 5 Regular",
              "Font Awesome 5 Brands",
              "simple-line-icons",
            ],
            urls: ["../assets/css/fonts.min.css"],
          },
          active: function () {
            sessionStorage.fonts = true;
          },
        });
      </script>

      <!-- CSS Files -->
      <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
      <link rel="stylesheet" href="../assets/css/plugins.min.css" />
      <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
      <link rel="stylesheet" href="../assets/css/meifang.css"/>

      <!-- CSS Just for demo purpose, don't include it in your project -->
      <link rel="stylesheet" href="../assets/css/demo.css" />
      
    </head>
    <style>
    .order-transaction-wrapper {
        max-width: 800px; /* Batasi lebar maksimal */
        margin: 0 auto; /* Posisikan ke tengah */
        padding: 15px; /* Tambahkan padding untuk ruang di sekitar */
    }

    .order-transaction {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Tambahkan bayangan untuk efek */
    }

    .order-transaction .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center; /* Rata tengah secara vertikal */
        margin-bottom: 10px;
        flex-wrap: wrap; /* Membungkus elemen jika diperlukan */
    }

    .order-transaction .order-item-number {
        font-weight: bold;
        margin-right: 10px;
        flex: 1; /* Flex untuk menyesuaikan ukuran */
        min-width: 150px; /* Setel lebar minimum untuk menghindari terlalu sempit */
    }

    .order-transaction .order-item-price {
        font-weight: bold;
        margin-left: 10px; /* Tambahkan margin kiri */
        flex: 0 0 auto; /* Tidak fleksibel, menggunakan ukuran aslinya */
    }

    .order-transaction .total {
        font-weight: bold;
        font-size: 18px;
        margin-top: 10px;
        text-align: right; /* Menyelaraskan total ke kanan */
        flex-basis: 100%; /* Memaksa elemen ini berada di baris baru */
    }

    .btn-confirm {
    background-color: #0d6efd;
    color: white;
    padding: 10px 20px; /* Tambahkan padding untuk ukuran tombol */
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: block;
    width: 100%; /* Membuat tombol penuh lebar */
    margin-top: 15px; /* Tambahkan jarak atas */
    transition: background-color 0.3s ease; /* Tambahkan efek transisi */
}

.btn-confirm:hover {
    background-color: #0056b3; /* Warna biru lebih terang saat hover */
    color: white; /* Pastikan teks tetap putih */
}



    /* Media query untuk perangkat dengan lebar maksimum 600px */
    @media (max-width: 600px) {
        .order-transaction-wrapper {
            padding: 10px; /* Kurangi padding pada layar kecil */
        }

        .order-transaction .order-item {
            flex-direction: column; /* Ubah arah elemen menjadi kolom */
            align-items: flex-start; /* Rata kiri untuk item */
        }

        .order-transaction .order-item-number {
            margin-right: 0;
            margin-bottom: 5px; /* Tambahkan jarak bawah */
            min-width: 0; /* Hapus lebar minimum */
        }

        .order-transaction .order-item-price {
            margin-left: 0; /* Hapus margin kiri pada layar kecil */
            margin-top: 5px; /* Tambahkan jarak atas */
        }

        .order-transaction .total {
            font-size: 16px; /* Ubah ukuran font menjadi lebih kecil */
            text-align: left; /* Ubah penyelarasan ke kiri */
        }

        .btn-confirm {
            padding: 8px 15px; /* Kurangi padding pada layar kecil */
        }
    }
    .payment-container {
    display: flex;
    flex-direction: column; /* Stack items vertically */
    gap: 15px; /* Add spacing between payment categories */
    width: 100%; /* Ensure the container spans full width */
    padding: 10px; /* Add padding for inner spacing */
}

.payment-container h5 {
    margin-bottom: 10px; /* Space below the title */
    font-size: 18px; /* Slightly larger font for section title */
    font-weight: bold;
    color: #333;
}

.payment-category {
    border: 2px solid #dee2e6; /* Light border around each section */
    border-radius: 8px; /* Rounded corners */
    padding: 15px; /* Inner padding for spacing */
    background-color: #f8f9fa; /* Light background for the section */
}

.payment-category b {
    margin-top: 0; /* No margin on the top */
    font-size: 16px; /* Distinctive size for category labels */
    color: #444;
    display: block; /* Ensures that the category label stays on its own line */
}

.payment-option {
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 10px;
    display: flex; /* Enable horizontal alignment */
    flex-direction: row; /* Arrange icon and text horizontally */
    align-items: center; /* Center items vertically */
    justify-content: flex-start; /* Align content to the left */
    height: 50px; /* Adjust height for button size */
    padding: 10px 15px; /* Add padding for spacing */
    background-color: #f8f9fa; /* Light background for button */
    width: 100%; /* Full width of container */
    max-width: 100%; /* Ensure it stretches to fill available space */
    transition: background-color 0.3s, border-color 0.3s; /* Smooth transitions */
}

.payment-option img {
    max-width: 30px; /* Smaller icon size for button */
    height: auto; /* Maintain aspect ratio */
    margin-right: 10px; /* Add spacing between icon and text */
}

.payment-option span {
    font-size: 14px; /* Set font size for button text */
    font-weight: 500;
    color: #333;
}

.payment-option:hover {
    border-color: #0d6efd; /* Blue border on hover */
    background-color: #e9ecef; /* Slightly darker background on hover */
}

.payment-option.border-success {
    border-color: #0d6efd; /* Blue border for selected state */
    background-color: #dbeafe; /* Light blue background for selected state */
}

/* Ensure the payment option buttons fill the full width */
.payment-category .payment-option {
    width: 100%; /* Make sure each button fills the container width */
    margin-bottom: 5px; /* Add spacing between each option */
}

.notification-card {
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    width: 350px;
    margin: 0 auto;
    padding: 15px;
    background-color: #fff;
}

.notification-header {
    text-align: center;
    margin-bottom: 10px;
}

.bank-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.check-status-btn {
    margin-top: 15px;
    background-color: #00bcd4;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

.check-status-btn:hover {
    background-color: #0097a7;
}


</style>



    <div>
      <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
          <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="../index.php" class="logo">
                <img
                  src="../assets/img/meifang_resto_gambar/1.svg"
                  alt="navbar brand"
        class="navbar-brand"
        height="150"  
        width="150"   
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
              <ul class="nav nav-secondary">
              <li class="nav-item ">
                  <a href="../index.php">
                    <i class="fas fa-home"></i>
                    <p>Dashboard</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="../tables/user_table.php">
                  <i class="fas fa-user-alt "></i>
                    <p>User Login</p>
                  </a>
                </li>
                <li class="nav-item  ">
                <a href="../tables/user_manager.php">
                  <i class="fas fa-users"></i>
                  <p>User Manager</p>
                </a>
              </li>
                <li class="nav-section">
                  <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                  </span>
                  <h4 class="text-section">Components</h4>
                </li>
                <li class="nav-item ">
                <a href="../tables/daftar_menu_1.php">
                  <i class="fas fa-book"></i>
                  <p>Daftar Menu</p>
                </a>
                <li class="nav-item">
                <a href="../forms/order_menu.php">
                  <i class="
                    fas fa-utensils
                    "></i>
                  <p>Order Menu</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../forms/detail_order.php">
                  <i class="fas fa-shopping-cart"></i>
                  <p>Details Order</p>
                </a>
              </li>
              <li class="nav-item active">
                <a href="../forms/transaction_order.php">
                  <i class="fas fa-money-check-alt"></i>
                  <p>Transaction Order</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../forms/transaction_history.php">
                  <i class="fas fa-history"></i>
                  <p> History Transaction </p>
                </a>
              </li>
            <li class="nav-item">
            <a href="../forms/digital_payment.php">
              <i class="fas fa-money-check"></i>
              <p>Payment Method</p>
            </a>
          </li>
          <li class="nav-item ">
            <a href="../forms/charts.php">
              <i class="far fa-chart-bar"></i>
              <p>Sales Report</p>
            </a>
          </li>
                <!-- <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#base">
                    <i class="fas fa-layer-group"></i>
                    <p>Base</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="base">
                    <ul class="nav nav-collapse">
                      <li>
                        <a href="../components/avatars.php">
                          <span class="sub-item">Avatars</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/buttons.php">
                          <span class="sub-item">Buttons</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/gridsystem.php">
                          <span class="sub-item">Grid System</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/panels.php">
                          <span class="sub-item">Panels</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/notifications.php">
                          <span class="sub-item">Notifications</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/sweetalert.php">
                          <span class="sub-item">Sweet Alert</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/font-awesome-icons.php">
                          <span class="sub-item">Font Awesome Icons</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/simple-line-icons.php">
                          <span class="sub-item">Simple Line Icons</span>
                        </a>
                      </li>
                      <li>
                        <a href="../components/typography.php">
                          <span class="sub-item">Typography</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarLayouts">
                    <i class="fas fa-th-list"></i>
                    <p>Sidebar Layouts</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="sidebarLayouts">
                    <ul class="nav nav-collapse">
                      <li>
                        <a href="../sidebar-style-2.php">
                          <span class="sub-item">Sidebar Style 2</span>
                        </a>
                      </li>
                      <li>
                        <a href="../icon-menu.php">
                          <span class="sub-item">Icon Menu</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
               
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#tables">
                    <i class="fas fa-table"></i>
                    <p>Tables</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse " id="tables">
                    <ul class="nav nav-collapse">
                      <li class="">
                        <a href="../tables/tables.php">
                          <span class="sub-item">Basic Table</span>
                        </a>
                      </li>
                      <li>
                        <a href="../tables/datatables.php">
                          <span class="sub-item">Datatables</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#maps">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Maps</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="maps">
                    <ul class="nav nav-collapse">
                      <li>
                        <a href="../maps/googlemaps.php">
                          <span class="sub-item">Google Maps</span>
                        </a>
                      </li>
                      <li>
                        <a href="../maps/jsvectormap.php">
                          <span class="sub-item">Jsvectormap</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#charts">
                    <i class="far fa-chart-bar"></i>
                    <p>Charts</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="charts">
                    <ul class="nav nav-collapse">
                      <li>
                        <a href="../charts/charts.php">
                          <span class="sub-item">Chart Js</span>
                        </a>
                      </li>
                      <li>
                        <a href="../charts/sparkline.php">
                          <span class="sub-item">Sparkline</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                  <a href="../widgets.php">
                    <i class="fas fa-desktop"></i>
                    <p>Widgets</p>
                    <span class="badge badge-success">4</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="../../../documentation/index.php">
                    <i class="fas fa-file"></i>
                    <p>Documentation</p>
                    <span class="badge badge-secondary">1</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#submenu">
                    <i class="fas fa-bars"></i>
                    <p>Menu Levels</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="submenu">
                    <ul class="nav nav-collapse">
                      <li>
                        <a data-bs-toggle="collapse" href="#subnav1">
                          <span class="sub-item">Level 1</span>
                          <span class="caret"></span>
                        </a>
                        <div class="collapse" id="subnav1">
                          <ul class="nav nav-collapse subnav">
                            <li>
                              <a href="#">
                                <span class="sub-item">Level 2</span>
                              </a>
                            </li>
                            <li>
                              <a href="#">
                                <span class="sub-item">Level 2</span>
                              </a>
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li>
                        <a data-bs-toggle="collapse" href="#subnav2">
                          <span class="sub-item">Level 1</span>
                          <span class="caret"></span>
                        </a>
                        <div class="collapse" id="subnav2">
                          <ul class="nav nav-collapse subnav">
                            <li>
                              <a href="#">
                                <span class="sub-item">Level 2</span>
                              </a>
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li>
                        <a href="#">
                          <span class="sub-item">Level 1</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li> -->
              </ul>
            </div>
          </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
          <div class="main-header">
            <div class="main-header-logo">
              <!-- Logo Header -->
              <div class="logo-header" data-background-color="dark">
                <a href="../index.php" class="logo">
                  <img
                    src="../assets/img/kaiadmin/logo_light.svg"
                    alt="navbar brand"
                    class="navbar-brand"
                    height="20"
                  />
                </a>
                <div class="nav-toggle">
                  <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                  </button>
                  <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                  </button>
                </div>
                <button class="topbar-toggler more">
                  <i class="gg-more-vertical-alt"></i>
                </button>
              </div>
              <!-- End Logo Header -->
            </div>
            <!-- Navbar Header -->
            <nav
              class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
              <div class="container-fluid">
                <nav
                  class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <button type="submit" class="btn btn-search pe-1">
                        <i class="fa fa-search search-icon"></i>
                      </button>
                    </div>
                    <input type="text"placeholder="Search ..."class="form-control"/>
                  </div>
                </nav>

                            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-search"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-search animated fadeIn">
                        <form class="navbar-left navbar-form nav-search">
                            <div class="input-group">
                                <input
                                    type="text"
                                    placeholder="Search ..."
                                    class="form-control"
                                    id="navbarSearchInput" 
                                />
                            </div>
                        </form>
                    </ul>
                </li>
              
                  <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="messageDropdown"
                      role="button"
                      data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                    >
                      <i class="fa fa-envelope"></i>
                    </a>
                    <ul
                      class="dropdown-menu messages-notif-box animated fadeIn"
                      aria-labelledby="messageDropdown"
                    >
                      <li>
                        <div
                          class="dropdown-title d-flex justify-content-between align-items-center"
                        >
                          Messages
                          <a href="#" class="small">Mark all as read</a>
                        </div>
                      </li>
                      <li>
                        <div class="message-notif-scroll scrollbar-outer">
                          <div class="notif-center">
                            <a href="#">
                              <div class="notif-img">
                                <img
                                  src="../assets/img/jm_denis.jpg"
                                  alt="Img Profile"
                                />
                              </div>
                              <div class="notif-content">
                                <span class="subject">Jimmy Denis</span>
                                <span class="block"> How are you ? </span>
                                <span class="time">5 minutes ago</span>
                              </div>
                            </a>
                            <a href="#">
                              <div class="notif-img">
                                <img
                                  src="../assets/img/chadengle.jpg"
                                  alt="Img Profile"
                                />
                              </div>
                              <div class="notif-content">
                                <span class="subject">Chad</span>
                                <span class="block"> Ok, Thanks ! </span>
                                <span class="time">12 minutes ago</span>
                              </div>
                            </a>
                            <a href="#">
                              <div class="notif-img">
                                <img
                                  src="../assets/img/mlane.jpg"
                                  alt="Img Profile"
                                />
                              </div>
                              <div class="notif-content">
                                <span class="subject">Jhon Doe</span>
                                <span class="block">
                                  Ready for the meeting today...
                                </span>
                                <span class="time">12 minutes ago</span>
                              </div>
                            </a>
                            <a href="#">
                              <div class="notif-img">
                                <img
                                  src="../assets/img/talha.jpg"
                                  alt="Img Profile"
                                />
                              </div>
                              <div class="notif-content">
                                <span class="subject">Talha</span>
                                <span class="block"> Hi, Apa Kabar ? </span>
                                <span class="time">17 minutes ago</span>
                              </div>
                            </a>
                          </div>
                        </div>
                      </li>
                      <li>
                        <a class="see-all" href="javascript:void(0);"
                          >See all messages<i class="fa fa-angle-right"></i>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="notifDropdown"
                      role="button"
                      data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                    >
                      <i class="fa fa-bell"></i>
                      <span class="notification">4</span>
                    </a>
                    <ul
                      class="dropdown-menu notif-box animated fadeIn"
                      aria-labelledby="notifDropdown"
                    >
                      <li>
                        <div class="dropdown-title">
                          You have 4 new notification
                        </div>
                      </li>
                      <li>
                        <div class="notif-scroll scrollbar-outer">
                          <div class="notif-center">
                            <a href="#">
                              <div class="notif-icon notif-primary">
                                <i class="fa fa-user-plus"></i>
                              </div>
                              <div class="notif-content">
                                <span class="block"> New user registered </span>
                                <span class="time">5 minutes ago</span>
                              </div>
                            </a>
                            <a href="#">
                              <div class="notif-icon notif-success">
                                <i class="fa fa-comment"></i>
                              </div>
                              <div class="notif-content">
                                <span class="block">
                                  Rahmad commented on Admin
                                </span>
                                <span class="time">12 minutes ago</span>
                              </div>
                            </a>
                            <a href="#">
                              <div class="notif-img">
                                <img
                                  src="../assets/img/profile2.jpg"
                                  alt="Img Profile"
                                />
                              </div>
                              <div class="notif-content">
                                <span class="block">
                                  Reza send messages to you
                                </span>
                                <span class="time">12 minutes ago</span>
                              </div>
                            </a>
                            <a href="#">
                              <div class="notif-icon notif-danger">
                                <i class="fa fa-heart"></i>
                              </div>
                              <div class="notif-content">
                                <span class="block"> Farrah liked Admin </span>
                                <span class="time">17 minutes ago</span>
                              </div>
                            </a>
                          </div>
                        </div>
                      </li>
                      <li>
                        <a class="see-all" href="javascript:void(0);"
                          >See all notifications<i class="fa fa-angle-right"></i>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a
                      class="nav-link"
                      data-bs-toggle="dropdown"
                      href="#"
                      aria-expanded="false"
                    >
                      <i class="fas fa-layer-group"></i>
                    </a>
                    <div class="dropdown-menu quick-actions animated fadeIn">
                      <div class="quick-actions-header">
                        <span class="title mb-1">Quick Actions</span>
                        <span class="subtitle op-7">Shortcuts</span>
                      </div>
                      <div class="quick-actions-scroll scrollbar-outer">
                        <div class="quick-actions-items">
                          <div class="row m-0">
                            <a class="col-6 col-md-4 p-0" href="#">
                              <div class="quick-actions-item">
                                <div class="avatar-item bg-danger rounded-circle">
                                  <i class="far fa-calendar-alt"></i>
                                </div>
                                <span class="text">Calendar</span>
                              </div>
                            </a>
                            <a class="col-6 col-md-4 p-0" href="#">
                              <div class="quick-actions-item">
                                <div
                                  class="avatar-item bg-warning rounded-circle"
                                >
                                  <i class="fas fa-map"></i>
                                </div>
                                <span class="text">Maps</span>
                              </div>
                            </a>
                            <a class="col-6 col-md-4 p-0" href="#">
                              <div class="quick-actions-item">
                                <div class="avatar-item bg-info rounded-circle">
                                  <i class="fas fa-file-excel"></i>
                                </div>
                                <span class="text">Reports</span>
                              </div>
                            </a>
                            <a class="col-6 col-md-4 p-0" href="#">
                              <div class="quick-actions-item">
                                <div
                                  class="avatar-item bg-success rounded-circle"
                                >
                                  <i class="fas fa-envelope"></i>
                                </div>
                                <span class="text">Emails</span>
                              </div>
                            </a>
                            <a class="col-6 col-md-4 p-0" href="#">
                              <div class="quick-actions-item">
                                <div
                                  class="avatar-item bg-primary rounded-circle"
                                >
                                  <i class="fas fa-file-invoice-dollar"></i>
                            
                                </div>
                                <span class="text">Invoice</span>
                              </div>
                            </a>
                            <a class="col-6 col-md-4 p-0" href="#">
                              <div class="quick-actions-item">
                                <div
                                  class="avatar-item bg-secondary rounded-circle"
                                >
                                  <i class="fas fa-credit-card"></i>
                                </div>
                                <span class="text">Payments</span>
                              </div>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>

                  <li class="nav-item topbar-user dropdown hidden-caret">
                    <a
                      class="dropdown-toggle profile-pic"
                      data-bs-toggle="dropdown"
                      href="#"
                      aria-expanded="false"
                    >
                      <div class="avatar-sm">
                        <img
                         src="../assets/img/profile/jane.png"
                          alt="..."
                          class="avatar-img rounded-circle"
                        />
                      </div>
                      <span class="profile-username">
                        <span class="op-7">Hi,</span>
                        <span class="fw-bold"><?php echo htmlspecialchars(string: $nama_lengkap); ?></span>
                      </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                      <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                          <div class="user-box">
                            <div class="avatar-lg">
                              <img
                                   src="../assets/img/profile/jane.png"
                                alt="image profile"
                                class="avatar-img rounded"
                              />
                            </div>
                            <div class="u-text">
                              <h4><?php echo htmlspecialchars($nama_lengkap); ?></h4>
                              <p class="text-muted"><?php echo htmlspecialchars($username); ?></p>
                              <a
               href="../profile.php"
                                class="btn btn-xs btn-secondary btn-sm"
                                >View Profile</a
                              >
                            </div>
                          </div>
                        </li>
                        <li>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#">My Profile</a>
                          <a class="dropdown-item" href="#">My Balance</a>
                          <a class="dropdown-item" href="#">Inbox</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#">Account Setting</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="logout.php">Logout</a>
                        </li>
                      </div>
                    </ul>
                  </li>
                </ul>
              </div>
            </nav>
            <!-- End Navbar -->
          </div>
          <div class="container mt-15"><div class="card">
          <?php
                include 'koneksi.php'; // Include your database connection file

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // SQL query to fetch the most recent order details
                $sql = "SELECT user_role, tanggal, id_order, name, no_meja 
                        FROM order_details 
                        ORDER BY tanggal DESC 
                        LIMIT 1"; // Limit to the latest order
                $result = $conn->query($sql);

                $user_role = "Unknown"; // Default if no data is found
                $order_date = "Unknown";
                $no_meja = "Unknown";
                $name = "Unknown";
                $id_order = "Unknown"; // Default if no order found

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $user_role = $row['user_role'];
                    $order_date = $row['tanggal'];
                    $no_meja = $row['no_meja'];
                    $name = $row['name'];
                    $id_order = $row['id_order'];
                }
                ?>  
    <div class="container mt-5">
        <div class="order-transaction-wrapper">
            <div class="order-transaction">
                <!-- Subheadline -->
                <div class="text-center">
                  <h3>Meifang Resto Admin</h3>
                  <p>Jl. Raya Purwosari No. 158, Pasuruan</p>
                  <p>Contact: 0822-4707-9268 | Email: meifangresto@gmail.com</p>
                  <hr>
                  <div class="d-flex justify-content-between">
                      <!-- Left-aligned section -->
                      <div>
                          <p>
                              Date: <?php echo date("d-m-Y", strtotime($order_date)); ?> | 
                              Order ID: <?php echo htmlspecialchars($id_order); ?> | 
                              Cashier: <?php echo ucfirst($user_role); ?> | 
                          </p>
                      </div>
                      <!-- Right-aligned section -->
                      <div>
                          <p>
                              Table Number: <?php echo htmlspecialchars($no_meja); ?> |
                              Customer Name: <?php echo htmlspecialchars($name); ?>
                          </p>
                      </div>
                  </div>
               </div>
                <hr>

                <?php
include 'koneksi.php';

// Fetch the most recent order ID from the order_details table
$sql = "SELECT id_order, price FROM order_details ORDER BY tanggal DESC LIMIT 1";
$result = $conn->query($sql);

// Check if we got a result
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_order = $row['id_order'];
} else {
    $id_order = 0; // Default if no orders are found
}

// Fetch order details for the current id_order
$sql = "SELECT * FROM order_details WHERE id_order = '$id_order'";
$result = $conn->query($sql);

$subtotal = 0; // Initialize subtotal
if ($result->num_rows > 0): 
?>
    <?php $itemNumber = 1; ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php 
        $itemTotal = $row['quantity'] * $row['price']; 
        $subtotal += $itemTotal;
        ?>
        <div class="order-item d-flex justify-content-between">
            <div>
                <span class="order-item-number"><?php echo $itemNumber; ?>.</span>
                <div class="d-inline-block">
                    <h6 class="mb-0"><?php echo htmlspecialchars($row['nama_masakan']); ?></h6>
                    <small>Quantity: <?php echo htmlspecialchars($row['quantity']); ?> | Type Order: <?php echo htmlspecialchars($row['type_order']); ?></small>
                </div>
            </div>
            <div class="text-end">
                <span>Rp<?php echo number_format($itemTotal, 3, ',', '.'); ?></span>
                <form method="POST" action="delete_item.php" class="d-inline-block" onsubmit="return confirmDelete()">
                    <input type="hidden" name="id_detail" value="<?php echo $row['id_detail']; ?>">
                    <input type="hidden" name="id_order" value="<?php echo $id_order; ?>">
                    <button type="submit" class="btn btn-sm btn-light">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php $itemNumber++; ?>
    <?php endwhile; ?>
<?php else: ?>
    <p>No items found in this order.</p>
<?php endif; ?>


                <hr>
                <!-- Total Payment Section -->
                <div class="d-flex justify-content-between total">
                    <span>Total Payment</span>
                    <span id="total">Rp<?php echo number_format($subtotal, 3, ',', '.'); ?></span>
                </div>
                <hr>
                <!-- Payment Buttons -->
                <div class="d-flex flex-wrap justify-content-between mt-2 gap-2">
    <!-- Cancel Payment Button -->
    <button class="btn btn-outline-danger flex-grow-1" onclick="cancelPayment()">Cancel Payment</button>
    
    <!-- Confirm Payment Button -->
    <button class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#paymentModal">Confirm Payment</button>
</div>


              <!-- Modal form -->
           
            <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <form method="POST" action="save_transaction.php" id="paymentForm">
                          <div class="container">
                          <input type="hidden" name="payment_with" id="paymentMethod">
                           
                          <div class="payment-container">
                            <!-- Section Title -->
                            <h5>Payment</h5>

                            <!-- Cash Section -->
                            <div class="payment-category">
                                <b>Cash</b>
                                <label for="paymentMethodsCash" class="payment-option border-primary d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('Cash')">
                                    <div>
                                      <img src="../assets/img/payment/money.png" alt="Cash">
                                    <span>Cash</span></div>
                                    <input type="radio" class="" id="paymentMethodsCash" name="paymentMethods" value="Cash" checked>
                                </label>
                            </div>

                         <!-- Transfer Virtual Account Section -->
                          <div class="payment-category">
                              <b>Transfer Virtual Account</b>
                              <label for="paymentMethodsBCA" class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('BCA')">
                                 <div> <img src="../assets/img/payment/bca.png" alt="BCA">
                                 <span>BCA</span> </div> 
                                  <input type="radio" id="paymentMethodsBCA" name="paymentMethods" value="BCA">
                              </label>

                              <label for="paymentMethodsBRI" class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('BRI')">
                                 <div>  <img src="../assets/img/payment/bri.png" alt="BRI">
                                  <span>BRI</span></div>
                                  <input type="radio" id="paymentMethodsBRI" name="paymentMethods" value="BRI">
                              </label>

                              <label for="paymentMethodsMandiri" class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('Mandiri')">
                              <div> 
                              <img src="../assets/img/payment/mandiri.png" alt="Mandiri">
                                  <span>Mandiri</span>
                              </div>
                                  <input type="radio" id="paymentMethodsMandiri" name="paymentMethods" value="Mandiri">
                              </label>
                          </div>

                          <!-- E-Wallet Section -->
                          <div class="payment-category">
                              <b>E-Wallet</b>
                              <label for="paymentMethodsDana" class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('Dana')">
                                  <div> <img src="../assets/img/payment/dana.png" alt="Dana">
                                  <span>Dana</span></div>
                                  <input type="radio" id="paymentMethodsDana" name="paymentMethods" value="Dana">
                              </label>

                              <label for="paymentMethodsGoPay" class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('GoPay')">
                              <div> <img src="../assets/img/payment/gopay.png" alt="GoPay">
                                 <span>GoPay</span> </div>
                                  <input type="radio" id="paymentMethodsGoPay" name="paymentMethods" value="GoPay">
                              </label>

                              <label for="paymentMethodsSeaBank" class="payment-option d-flex justify-content-between align-items-center" onclick="selectPaymentMethod('SeaBank')">
                              <div> <img src="../assets/img/payment/seabank.png" alt="SeaBank">
                                  <span>SeaBank</span></div>
                                  <input type="radio" id="paymentMethodsSeaBank" name="paymentMethods" value="SeaBank">
                              </label>
                          </div>  
                
                    <!-- Total Payment Input -->
                    <div class="mb-4">
                        <label for="modalTotalPayment" class="form-label">Total Payment (Rp)</label>
                        <input type="text" class="form-control" id="TotalPayment" name="total_payment" value="Rp<?php echo number_format($subtotal, 3, ',', '.'); ?>" readonly>
                    </div>
                                            <!-- Cash Amount Input -->
                          <div id="cashFields" style="display: none;">
                              <label for="cashAmount" class="form-label">Cash Amount (Rp)</label>
                              <div class="input-group mb-4">
                                  <div class="input-group-prepend">
                                      <span class="input-group-text">Rp.</span>
                                  </div>
                                  <input type="text" id="cashAmount" name="cash_amount" class="form-control" placeholder="Enter amount" oninput="calculateChange(this)" onblur="checkCashAmount()">
                              </div>
                          </div>

                          <!-- Change Section -->
                          <div id="changeSection" style="display: none;">
                              <label for="changeAmount" class="form-label">Change (Rp)</label>
                              <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                      <span class="input-group-text">Rp.</span>
                                  </div>
                                  <input type="text" class="form-control" id="changeAmount" name="change_amount" value="0" readonly>
                              </div>
                          </div>
         
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Submit Payment</button>
                            
                          </div>
                      </form>
                  </div>
              </div>
          </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        const idTransaksi = document.querySelector('[name="id_transaksi"]').value;
        if (!idTransaksi) {
            alert("Please enter a valid transaction ID.");
            event.preventDefault();
        }
    });
</script>




            <?php
            // Close connection
            $conn->close();
            ?>
        </div>
</div>

</div>



    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo2.js"></script>
    <script>
      $("#displayNotif").on("click", function () {
        var placementFrom = $("#notify_placement_from option:selected").val();
        var placementAlign = $("#notify_placement_align option:selected").val();
        var state = $("#notify_state option:selected").val();
        var style = $("#notify_style option:selected").val();
        var content = {};

        content.message =
          'Turning standard Bootstrap alerts into "notify" like notifications';
        content.title = "Bootstrap notify";
        if (style == "withicon") {
          content.icon = "fa fa-bell";
        } else {
          content.icon = "none";
        }
        content.url = "index.php";
        content.target = "_blank";

        $.notify(content, {
          type: state,
          placement: {
            from: placementFrom,
            align: placementAlign,
          },
          time: 1000,
        });
      });
    </script>

<script> 
  function deleteOrderItem(orderId) {
    if (confirm("Are you sure you want to delete this order?")) {
        // Redirect to delete_order.php with order ID as a GET parameter
        window.location.href = "delete_order_item.php?id=" + orderId;
    }
}

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const paymentRadios = document.querySelectorAll('input[name="paymentMethods"]');
    const cashFields = document.getElementById('cashFields'); 
    const changeSection = document.getElementById('changeSection'); 
    function toggleCashFields() {
        const selectedPayment = document.querySelector('input[name="paymentMethods"]:checked').value;

        if (selectedPayment === "Cash") {
            cashFields.style.display = "block";
            changeSection.style.display = "block";
        } else {
            cashFields.style.display = "none";
            changeSection.style.display = "none";
            document.getElementById("cashAmount").value = ""; 
            document.getElementById("changeAmount").value = "0";
        }
    }

    paymentRadios.forEach(radio => {
        radio.addEventListener("change", toggleCashFields);
    });

    toggleCashFields();
});
</script>

<script>
     document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function () {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('border-primary'));
            this.classList.add('border-primary');
        });
    });
// Function to handle payment method selection
function selectPaymentMethod(paymentMethod) {
    // Hide the change section by default
    document.getElementById('changeSection').style.display = 'none';

    // Mark the selected payment method
    document.getElementById('paymentMethod').value = paymentMethod;

    // If Cash is selected, show the change section
    if (paymentMethod === 'Cash') {
        document.getElementById('changeSection').style.display = 'block';
    } else {
        document.getElementById('changeAmount').value = "0"; // Reset for non-cash
    }
}

// Function to calculate the change when cash is entered
function calculateChange(input) {
    // Clean the TotalPayment input value
    const rawTotalPayment = document.getElementById('TotalPayment').value;
    const totalPayment = parseFloat(
        rawTotalPayment.replace('Rp', '').replace(/\./g, '').replace(',', '')
    ) || 0;

    // Clean the cashAmount input value
    const rawCashAmount = document.getElementById('cashAmount').value;
    const cashAmount = parseFloat(
        rawCashAmount.replace(/,/g, '') // Remove commas for calculations
    ) || 0;

    console.log("Cash Amount: ", cashAmount);
    console.log("Total Payment: ", totalPayment);

    // Automatically select cash payment method when cash amount is entered
    if (cashAmount > 0) {
        selectPaymentMethod('Cash');
    }

    // Check if the payment method is 'Cash' before calculating change
    const paymentMethod = document.getElementById('paymentMethod').value;
    if (paymentMethod === 'Cash') {
        if (!isNaN(totalPayment) && !isNaN(cashAmount)) {
            const changeAmount = cashAmount - totalPayment;
            console.log('change', changeAmount)

            // Ensure the change is non-negative
            if (changeAmount >= 0) {
              document.getElementById('changeAmount').value = changeAmount.toLocaleString('id-ID', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2
      });

            } else {
                console.log('Change is less than 0');
                document.getElementById('changeAmount').value = "0";
            }
        }
    } else {
        console.log('Non-Cash Payment');
        document.getElementById('changeAmount').value = "0";
    }

    // Format the cash input field dynamically (add thousand separators)
    let value = input.value.replace(/[^0-9.]/g, ""); // Remove all non-numeric characters except '.'
    let parts = value.split(".");
    let integerPart = parts[0];
    let decimalPart = parts.length > 1 ? "." + parts[1] : "";
    let formatted = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    input.value = formatted + decimalPart;
}

// New function to trigger alert after finishing input
function checkCashAmount() {
    const rawTotalPayment = document.getElementById('TotalPayment').value;
    const totalPayment = parseFloat(
        rawTotalPayment.replace('Rp', '').replace(/\./g, '').replace(',', '.')
    ) || 0;

    const cashAmount = parseFloat(document.getElementById('cashAmount').value) || 0;

    // Check if the payment method is 'Cash' and validate cash amount
    const paymentMethod = document.getElementById('paymentMethod').value;
    if (paymentMethod === 'Cash' && cashAmount < totalPayment) {
        // alert('Insufficient cash amount!');
    }
}



// Optional: Form validation before submission (ensure fields are correctly filled)
document.getElementById('paymentForm').addEventListener('submit', function (event) {
    const paymentMethod = document.querySelector('.payment-option.selected');
    const accountNumber = document.getElementById('accountNumber').value;
    const password = document.getElementById('password').value;
    const cashAmount = document.getElementById('cashAmount').value;

    // Ensure payment method is selected
    if (!paymentMethod) {
        alert('Please select a payment method.');
        event.preventDefault(); // Prevent form submission
        return;
    }


    // Ensure cash amount is entered when paying by cash
    if (paymentMethod.getAttribute('data-value') === 'cash' && !cashAmount) {
        alert('Please enter the cash amount.');
        event.preventDefault(); // Prevent form submission
        return;
    }

    // The form can be submitted if all checks pass
});
</script>

<script> 
function selectPaymentMethod(method) {
    document.getElementById('paymentMethod').value = method;
    console.log('Selected Payment Method:', method); // Debugging log
}

</script>
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this item?");
    }

</script>
<script>
    function cancelPayment() {
        if (confirm("Are you sure you want to cancel this payment? This action cannot be undone.")) {
            // Make an AJAX request to cancel the order in the backend
            const idOrder = <?php echo json_encode($id_order); ?>; // Pass the current order ID
            
            fetch('cancel_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id_order: idOrder })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Payment has been canceled successfully.");
                    // Optionally reset the UI or redirect the user
                    window.location.reload(); // Reload the page
                } else {
                    alert("Failed to cancel payment: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while canceling the payment.");
            });
        }
    }
</script>

  </body>
</html>
