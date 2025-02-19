<?php
session_start();

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "apk_kasir");

// Pengecekan koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ensure session id_user exists before accessing it
if (!isset($_SESSION['id_user'])) {
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
                  icon: 'warning',
                  title: 'Access Denied!',
                  text: 'You are not logged in. Please log in first.',
              }).then(function() {
                  window.location.href = '../login.php';
              });
          });
      </script>";
  exit(); // Stop script execution
}
function showErrorAndExit($message, $redirectUrl) {
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
                  icon: 'error',
                  title: 'Oops...',
                  text: '$message',
              }).then(function() {
                  window.location.href = '/meifang_resto_admin/login.php';
              });
          });
      </script>";
  exit();
}


// Periksa apakah sesi id_user tersedia
if (!isset($_SESSION['id_user'])) {
  header("Location: ./login.php");
  exit();
}
// Ambil id_user dari session
$id_user = $_SESSION['id_user'];

// Query untuk mengambil nama_lengkap
$sql = "SELECT nama_lengkap,username,tipe_user FROM user WHERE id_user = '$id_user'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nama_lengkap = $row['nama_lengkap'];
    $username = $row['username'];
    $tipe_user = $row['tipe_user'];

} else {
    $nama_lengkap = "Guest";
    $username = "Not avalaible";
    $tipe_user = "tipe_user";

}
if ($tipe_user !== "Administrator") {
  showErrorAndExit("Anda tidak memiliki akses sebagai admin!", "./login.php");
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

// Query untuk mengambil data transaksi
$query = "SELECT DISTINCT id_order, date, user_role, payment_with, total_payment, status_order 
          FROM transaksi 
          ORDER BY id_transaksi DESC";
$transactionResult = mysqli_query($conn, $query);

if (!$transactionResult) {
    die("Query gagal: " . mysqli_error($conn));
}

// Ambil dua riwayat pembayaran terakhir dengan ID order yang berbeda
$query = "
    SELECT DISTINCT id_order, total_payment, status_order, date 
    FROM payment_history 
    ORDER BY date DESC 
    LIMIT 2
";
$result = mysqli_query($conn, $query);

// Cek apakah ada hasil
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Ambil data pembayaran
$query = "SELECT * FROM transaksi GROUP BY id_order ORDER BY date DESC LIMIT 2"; // Ambil 2 transaksi terakhir dengan id_order yang berbeda
$result = mysqli_query($conn, $query);


// Cek apakah ada hasil
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Query untuk menghitung total pembayaran digital (bukan cash)
$query = "SELECT SUM(total_payment) AS total_digital_payment FROM transaksi WHERE payment_with != 'cash'";
$totalDigitalPaymentResult = mysqli_query($conn, $query);

// Cek apakah query total pembayaran digital berhasil
if (!$totalDigitalPaymentResult) {
    die("Query gagal: " . mysqli_error($conn));
}

// Query to calculate total income
$incomeQuery = "SELECT SUM(total_payment) AS total_income FROM transaksi";
$incomeResult = mysqli_query($conn, $incomeQuery);
$income = 0;
if ($incomeResult && $row = mysqli_fetch_assoc($incomeResult)) {
    $income = $row['total_income'];
}

// Jangan tutup koneksi sebelum mengambil semua data
?>




 <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <title>Meifang Resto - History Transaksi</title>
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
     <style>
   .status.success {
    background-color:rgb(35, 204, 74);
    color: #fff;
    border: 2px; /* Green border for success */
    padding: 5px 10px; /* Optional: to add padding inside the span */
    border-radius: 3px; /* Optional: to make the border rounded */
    font-weight: bold;

}

.status.pending {
    background-color: #ffc107;
    color: #fff;
    border: 2px ; /* Yellow border for pending */
    padding: 5px 10px; /* Optional: to add padding inside the span */
    border-radius: 3px; /* Optional: to make the border rounded */
    font-weight: bold;

}
.filter-btn-active {
    color: white;
    border-color: #0d6efd;
}



    </style>
      <!-- CSS Files -->
      <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
      <link rel="stylesheet" href="../assets/css/plugins.min.css" />
      <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
<!-- SweetAlert2 -->
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

      <!-- CSS Just for demo purpose, don't include it in your project -->
      <link rel="stylesheet" href="../assets/css/demo.css" />
      
    </head>
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
                <li class="nav-item ">
                  <a href="../tables/user_table.php">
                  <i class="fas fa-user-alt "></i>
                    <p>User Login</p>
                  </a>
                </li>
                <li class="nav-item">
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
                  <p>Daftar Menu </p>
                </a>
                </li>
                
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
              <li class="nav-item">
                <a href="../forms/transaction_order.php">
                  <i class="fas fa-money-check-alt"></i>
                  <p>Transaction Order</p>
                </a>
              </li>
              <li class="nav-item  ">
                <a href="../forms/transaction_history.php">
                  <i class="fas fa-history"></i>
                  <p> History Transaction </p>
                </a>
              </li>
              <li class="nav-item active">
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
                </li> -->
                <!-- <li class="nav-item">
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
                        <span class="fw-bold"><?php echo htmlspecialchars($nama_lengkap); ?></span>
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
          <div class="container mt-12">
          <div class="card">
  <!-- Tabs Navigation -->
  <div class="col" style="margin-left: 20px;">
    <div class="col-md-8 col-12">
      <h4 class="card-title mb-4">Payment Method</h4>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
  <button class="nav-link active" id="saldo-tab" data-bs-toggle="tab" data-bs-target="#saldo-tab-pane" type="button" role="tab" aria-controls="saldo-tab-pane" aria-selected="true"> Revenue</button>
</li>
<li class="nav-item" role="presentation">
  <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status-tab-pane" type="button" role="tab" aria-controls="status-tab-pane" aria-selected="false"> Payment Status</button>
</li>

    </ul>
  </div>


  <div class="tab-content">

<!-- Saldo Digital Payment -->
<div class="tab-pane fade show active p-3" id="saldo-tab-pane" role="tabpanel" aria-labelledby="saldo-tab">
  <div class="row d-flex justify-content-between align-items-center">
  <div class="d-flex justify-content-between align-items-center col-lg-8 col-12 col-md-8">
    <div>
        <b class="fs-9">Total Payment</b>
        <h4 class="text-danger"><b>Rp <?php echo number_format($income, 3, ',', '.'); ?></b></h4>
    </div>
</div>




    <div class="col-lg-4 col-12 text-end">
      <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#topupModal">
        <b>Payment</b>
      </button>
      <button class="btn btn-warning" onclick="window.location.href='check_deadline.php'">Cek Status</button>

    </div>
  <!-- Payment History -->
  <?php

// Set the number of rows per page
$rowsPerPage = 5;

// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row index for the query
$start = ($page - 1) * $rowsPerPage;

// Query to fetch the total number of rows
$totalRowsQuery = "SELECT COUNT(*) AS total FROM transaksi";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];

// Calculate the total number of pages
$totalPages = ceil($totalRows / $rowsPerPage);

// Query to fetch the data for the current page
$sql = "SELECT 
            id_order, 
            MAX(deadline) AS date, 
            total_payment, 
            payment_with, 
            status_order,
            deadline AS dateline -- Menambahkan kolom dateline
        FROM transaksi 
        WHERE status_order = 'pending' 
        GROUP BY id_order 
        ORDER BY MAX(deadline) DESC 
        LIMIT $start, $rowsPerPage";

$result = $conn->query($sql);
?>

<!-- Table -->
<div class="table-responsive" style="margin-left: 5px;">
  <table class="table table-bordered table-striped">
    <thead class="thead-light">
      <tr>
        <th scope="col">#</th> <!-- Number column -->
        <th scope="col">Date</th>
        <th scope="col">ID Order</th>
        <th scope="col">Total Payment</th>
        <th scope="col">Payment Method</th>
        <th scope="col">Status</th>
      </tr>
    </thead>
    <tbody>
    <?php
// Nomor urut dimulai dari 1 di setiap halaman
$currentNumber = 1;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $currentNumber++ . "</td> <!-- Increment the counter -->
<td>" . date('d F Y, H:i:s', strtotime($row['date'])) . "</td>
                <td>" . htmlspecialchars($row['id_order']) . "</td>
                <td>Rp " . number_format($row['total_payment'], 3, ',', '.') . "</td>
                <td>" . htmlspecialchars($row['payment_with']) . "</td>
                <td><span class='status " . strtolower($row['status_order']) . "'>" . ucfirst(htmlspecialchars($row['status_order'])) . "</span></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No data available</td></tr>";
}
?>

    </tbody>
  </table>
</div>


<!-- Pagination -->
<nav aria-label="Page navigation">
  <ul class="pagination justify-content-center">
    <?php
    // Tentukan jumlah halaman yang ditampilkan di sekitar halaman saat ini
    $range = 2; // Jumlah halaman sebelum/sesudah halaman saat ini
    $startPage = max(1, $page - $range);
    $endPage = min($totalPages, $page + $range);

    // Tombol "Previous"
    if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
    <?php endif; ?>

    <?php
    // Tampilkan ellipsis jika ada halaman tersembunyi di awal
    if ($startPage > 1): ?>
      <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
      <?php if ($startPage > 2): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
      <?php endif; ?>
    <?php endif; ?>

    <?php
    // Tampilkan halaman di sekitar halaman saat ini
    for ($i = $startPage; $i <= $endPage; $i++): ?>
      <li class="page-item <?php echo ($i == $page) ? 'active text-white' : ''; ?>">
        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
      </li>
    <?php endfor; ?>

    <?php
    // Tampilkan ellipsis jika ada halaman tersembunyi di akhir
    if ($endPage < $totalPages): ?>
      <?php if ($endPage < $totalPages - 1): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
      <?php endif; ?>
      <li class="page-item"><a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a></li>
    <?php endif; ?>

    <?php
    // Tombol "Next"
    if ($page < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    <?php endif; ?>
  </ul>
</nav>


<!-- Modal untuk Top-up Saldo -->
<div class="modal fade" id="topupModal" tabindex="-1" aria-labelledby="topupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="topupModalLabel">Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="process_topup.php" method="POST">
        <div class="modal-body">
          <!-- ID Order -->
          <div class="mb-3">
            <label for="id_order" class="form-label">No Id</label>
            <input type="text" class="form-control" id="id_order" name="id_order" placeholder="Masukkan Nomor Order" required>
          </div>
         
          <!-- Price (diambil otomatis berdasarkan id_order) -->
          <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" id="price" name="price" readonly>
          </div>

          <!-- Cash Amount (Tampil jika payment_with = 'cash') -->
          <div class="mb-3" id="cashAmountContainer" style="display: none;">
            <label for="cash_amount" class="form-label">Cash Amount (Rp)</label>
            <input type="number" class="form-control" id="cash_amount" name="cash_amount" step="any">
            </div>

          <!-- Change Amount (Tampil jika payment_with = 'cash') -->
          <div class="mb-3" id="changeAmountContainer" style="display: none;">
            <label for="change_amount" class="form-label">Change Amount (Rp)</label>
            <input type="text" class="form-control" id="change_amount" name="change_amount" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>


</div>

</div>

  </div>
  <div class="tab-content">
  <!-- Status Pembayaran Tab -->
  <div class="tab-pane fade p-3" id="status-tab-pane" role="tabpanel" aria-labelledby="status-tab">
    <div class="d-flex mb-3">
      <button class="btn btn-outline-primary me-2 filter-btn" data-status="all">All</button>
      <button class="btn btn-outline-primary me-2 filter-btn" data-status="success">Success</button>
      <button class="btn btn-outline-primary me-2 filter-btn" data-status="pending">Pending</button>
      <button class="btn btn-outline-primary  me-2 filter-btn" data-status="canceled">Canceled</button>
    </div>

    <!-- Status Pembayaran Cards -->
    <div id="status-payment-container">
    <?php
       // Query to get unique id_order from transaksi table
       $statusQuery = "SELECT 
       id_order, 
       MAX(date) AS date, 
       payment_with, 
       SUM(total_payment) AS total_payment, 
       status_order, 
       deadline
   FROM transaksi 
   GROUP BY id_order, status_order 
   ORDER BY MAX(date) DESC";


    $statusResult = $conn->query($statusQuery);

    if ($statusResult && $statusResult->num_rows > 0) {
      while ($row = $statusResult->fetch_assoc()) {
          $statusClass = strtolower($row['status_order']); // Convert status to lowercase for CSS class
          $id_order = strtolower($row['id_order']); // Convert id_order to lowercase for CSS class
          $statusLabel = ucfirst($row['status_order']); // Capitalize the status
          $formattedDate = date('d M Y, H:i', strtotime($row['date']));
          $formattedDeadline = date('d M Y, H:i', strtotime($row['deadline']));

          // Calculate due date only if status is not pending
          $dueDate = '';
          if (strtolower($row['status_order']) !== 'success') {
              $dueDate = date('d M, H:i', strtotime('+1 day', strtotime($row['date']))); 
          }

          $totalPayment = number_format($row['total_payment'], 3, ',', '.');
  ?>
            <div class="card p-3 mb-3 status-card <?php echo $statusClass; ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <b>Saldo Pembayaran</b>
                        <span class="text-secondary ps-2"><?php echo $formattedDate; ?></span>
                    </div>
                    <div class="d-flex flex-column text-end">
                        <div>
                            <?php if ($dueDate): ?>
                                <span class="text">Bayar Sebelum:</span>
                                <span class="text-warning">
                                    <b><i class="bi-clock mx-1"></i><?php echo $formattedDeadline; ?></b>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2">
                            <span class="text fs-6">Id Order:</span>
                            <span class="text-secondary ps-2"><?php echo $id_order; ?></span>
                        </div>
                        <div class="mt-2">
                            <span class="text fs-6">Status Order:</span>
                            <span class="text-secondary ps-2"><?php echo $statusLabel; ?></span>
                        </div>
                     
                    </div>
                </div>

                <div class="main-status my-3 d-flex justify-content-between">
                    <div class="d-flex">
                        <div class="logo-standar d-flex justify-content-center align-items-center rounded-circle me-3">
                            <?php
                            // Determine which payment logo to show based on payment_with
                            switch (strtolower($row['payment_with'])) {
                                case 'bca':
                                    echo '<img src="../assets/img/payment/bca.png" alt="BCA" class="img-fluid" style="width: 70px; height: 40px;">';
                                    break;
                                case 'bri':
                                    echo '<img src="../assets/img/payment/bri.png" alt="BRI" class="img-fluid" style="width: 100px; height: 25px;">';
                                    break;
                                case 'mandiri':
                                    echo '<img src="../assets/img/payment/mandiri.png" alt="Mandiri" class="img-fluid" style="width: 80px; height: 30px;">';
                                    break;
                                case 'dana':
                                    echo '<img src="../assets/img/payment/dana.png" alt="Dana" class="img-fluid" style="width: 70px; height: 25px;">';
                                    break;
                                case 'gopay':
                                    echo '<img src="../assets/img/payment/gopay.png" alt="GoPay" class="img-fluid" style="width: 70px; height: 40px;">';
                                    break;
                                case 'seabank':
                                    echo '<img src="../assets/img/payment/seabank.png" alt="SeaBank" class="img-fluid" style="width: 100px; height: 30px;">';
                                    break;
                                default:
                                    echo '<img src="../assets/img/payment/money.png" alt="Default Payment" class="img-fluid" style="width: 60px; height: 40px;">';
                                    break;
                            }
                            ?>
                        </div>
                        <div>
                            <div class="text-secondary fs-6">Metode Pembayaran</div>
                            <div><b><?php echo htmlspecialchars($row['payment_with']); ?></b></div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div><b>Rp <?php echo $totalPayment; ?></b></div>
                    </div>
                </div>
                <div class="col-lg-20 col-12 text-end">
                 
                  
              </div>
            </div>
    <?php
        }
    } else {
        echo "<p class='text-center text-muted'>Tidak ada data pembayaran.</p>";
    }
    ?>
</div>


  </div>
</div>

</div>




</div>
  </div>
</div>

 


        <!-- <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <nav class="pull-left">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="http://www.themekita.com">
                    ThemeKita
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#"> Help </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#"> Licenses </a>
                </li>
              </ul>
            </nav>
            <div class="copyright">
              2024, made with <i class="fa fa-heart heart text-danger"></i> by
              <a href="http://www.themekita.com">ThemeKita</a>
            </div>
            <div>
              Distributed by
              <a target="_blank" href="https://themewagon.com/">ThemeWagon</a>.
            </div>
          </div>
        </footer> -->
      </div>

      <!-- Custom template | don't include it in your project! -->
      <div class="custom-template">
        <div class="title">Settings</div>
        <div class="custom-content">
          <div class="switcher">
            <div class="switch-block">
              <h4>Logo Header</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="selected changeLogoHeaderColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="selected changeLogoHeaderColor"
                  data-color="blue"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="purple"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="light-blue"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="green"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="orange"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="red"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="white"
                ></button>
                <br />
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="dark2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="blue2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="purple2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="light-blue2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="green2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="orange2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="red2"
                ></button>
              </div>
            </div>
            <div class="switch-block">
              <h4>Navbar Header</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="blue"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="purple"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="light-blue"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="green"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="orange"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="red"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="white"
                ></button>
                <br />
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="dark2"
                ></button>
                <button
                  type="button"
                  class="selected changeTopBarColor"
                  data-color="blue2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="purple2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="light-blue2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="green2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="orange2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="red2"
                ></button>
              </div>
            </div>
            <div class="switch-block">
              <h4>Sidebar</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="selected changeSideBarColor"
                  data-color="white"
                ></button>
                <button
                  type="button"
                  class="changeSideBarColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeSideBarColor"
                  data-color="dark2"
                ></button>
              </div>
            </div>
          </div>
        </div>
        <!-- <div class="custom-toggle">
          <i class="icon-settings"></i>
        </div> -->
      </div>
      <!-- End Custom template -->
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

<!-- JavaScript untuk menambahkan koma sebagai pemisah ribuan -->
<script>
  const priceInput = document.getElementById('price');

  priceInput.addEventListener('input', function (e) {
    // Hapus semua koma dari input sebelumnya
    let value = this.value.replace(/,/g, '');

    // Pastikan hanya angka yang diizinkan
    if (!/^\d*$/.test(value)) {
      this.value = this.value.slice(0, -1); // Jika bukan angka, hapus karakter terakhir
      return;
    }

    // Tambahkan koma setiap 3 angka
    this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi untuk menangani respon dari PHP
    function handlePaymentResponse(response) {
        if (response.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Payment Successful!',
                text: response.message,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed!',
                text: response.message,
                showConfirmButton: true,
                confirmButtonText: 'Try Again'
            });
        }
    }

    // Submit form dengan AJAX
    document.getElementById("paymentForm").addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah form reload
        const formData = new FormData(this);

        fetch("process_topup.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => handlePaymentResponse(data))
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan pada server.',
            });
        });
    });
</script>
<script>
  // Event listener untuk tombol filter
  document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function() {
      const status = this.getAttribute('data-status');
      
      // Menghapus class 'filter-btn-active' dari semua tombol filter
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('filter-btn-active');
      });

      // Menambahkan class 'filter-btn-active' pada tombol yang aktif
      this.classList.add('filter-btn-active');
      
      // Menampilkan atau menyembunyikan kartu berdasarkan status yang dipilih
      const cards = document.querySelectorAll('.status-card');
      
      cards.forEach(card => {
        if (status === 'all') {
          card.style.display = 'block'; // Menampilkan semua kartu
        } else {
          if (card.classList.contains(status)) {
            card.style.display = 'block'; // Menampilkan kartu dengan status yang sesuai
          } else {
            card.style.display = 'none'; // Menyembunyikan kartu yang tidak sesuai status
          }
        }
      });
    });
  });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const filterButtons = document.querySelectorAll(".filter-btn");
        const paymentCards = document.querySelectorAll(".status-card");

        // Make the "all" filter button active by default
        const defaultButton = document.querySelector(".filter-btn[data-status='all']");
        defaultButton.classList.add("btn-primary", "filter-btn-active");
        defaultButton.style.color = '#ffffff'; // Set solid text color (white for active button)

        // Filter cards based on "all" status by default
        paymentCards.forEach(card => {
            card.style.display = "block"; // Show all cards
        });

        filterButtons.forEach(button => {
            button.addEventListener("click", () => {
                // Remove active class from all buttons and reset to default
                filterButtons.forEach(btn => {
                    btn.classList.remove("filter-btn-active");
                    btn.classList.remove("btn-primary");
                    btn.classList.add("btn-outline-primary"); // Reset to outline button

                    // Reset color and transparency for text
                    btn.style.color = ''; // Reset to default color
                });

                // Add primary class to the clicked button
                button.classList.add("btn-primary");
                button.classList.add("filter-btn-active");

                // Set solid color for text when active
                button.style.color = '#ffffff'; // Set solid text color (white for active button)

                // Get the selected status
                const selectedStatus = button.getAttribute("data-status");

                // Filter cards based on status
                paymentCards.forEach(card => {
                    if (selectedStatus === "all") {
                        card.style.display = "block"; // Show all cards
                    } else if (card.classList.contains(selectedStatus)) {
                        card.style.display = "block"; // Show matching cards
                    } else {
                        card.style.display = "none"; // Hide non-matching cards
                    }
                });
            });
        });
    });
</script>
<!-- AJAX untuk Mengambil Data -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    $('#id_order').on('change', function () {
      var id_order = $(this).val();

      // Request ke server untuk mendapatkan price dan payment_with
      $.ajax({
        url: 'get_order_details.php',
        type: 'POST',
        data: { id_order: id_order },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            $('#price').val(response.price);

            // Cek metode pembayaran
            if (response.payment_with === 'Cash') {
              $('#cashAmountContainer').show();
              $('#changeAmountContainer').show();
            } else {
              $('#cashAmountContainer').hide();
              $('#changeAmountContainer').hide();
            }
          } else {
            alert('ID Order tidak ditemukan!');
          }
        }
      });
    });

    // Hitung Change Amount otomatis
    $('#cash_amount').on('input', function () {
      var cashAmount = parseFloat($(this).val()) || 0;
      var price = parseFloat($('#price').val()) || 0;
      var change = cashAmount - price;

      $('#change_amount').val(change >= 0 ? change.toFixed(3) : '0.000');
    });
  });
  document.addEventListener("DOMContentLoaded", function () {
    const cashInput = document.getElementById("cash_amount");
    const changeInput = document.getElementById("change_amount");
    
    // Fungsi untuk memformat angka dengan pemisah ribuan dan desimal
    function formatCurrency(value) {
        let number = parseFloat(value.replace(/\./g, "").replace(",", ".")) || 0;
        return number.toLocaleString("id-ID").replace(",", ".");
    }

    // Event listener untuk input cash amount
    cashInput.addEventListener("input", function (e) {
        let rawValue = this.value.replace(/\D/g, ""); // Hapus karakter selain angka
        if (rawValue !== "") {
            this.value = formatCurrency(rawValue);
        }
        calculateChange();
    });

    // Fungsi untuk menghitung kembalian
    function calculateChange() {
        let totalAmount = 50000; // Ganti dengan nilai total belanja dari sistem
        let cashAmount = parseFloat(cashInput.value.replace(/\./g, "").replace(",", ".")) || 0;

        if (cashAmount >= totalAmount) {
            let change = cashAmount - totalAmount;
            changeInput.value = formatCurrency(change.toString());
        } else {
            changeInput.value = "0";
        }
    }
});
</script>

  </body>
</html>
