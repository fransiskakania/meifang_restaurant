<?php
session_start();

// Koneksi ke database
include 'koneksi.php';

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

// Query untuk mengambil total pembayaran per bulan
$query = "
    SELECT 
        DATE_FORMAT(date, '%b') AS bulan, 
        SUM(total_payment) AS total_pembayaran 
    FROM transaksi 
    GROUP BY MONTH(date), YEAR(date)
    ORDER BY MONTH(date)
";

$result = mysqli_query($conn, $query);

$months = [];
$payments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['bulan'];
    $payments[] = $row['total_pembayaran'];
}

// Encode data ke JSON untuk digunakan di JavaScript
$monthsJSON = json_encode($months);
$paymentsJSON = json_encode($payments);
// Ambil total_payment berdasarkan tanggal hari ini
$dateToday = date('Y-m-d'); // Format tanggal hari ini

// Gunakan prepared statement untuk keamanan
$query = "SELECT SUM(total_payment) AS today_income FROM transaksi WHERE DATE(date) = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $dateToday);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $todayIncome = $row['today_income'] ?? 0; // Default ke 0 jika tidak ada transaksi
    } else {
        $todayIncome = 0;
    }

    mysqli_stmt_close($stmt);
} else {
    $todayIncome = 0;
}


// Ambil total nama_masakan dari tabel transaksi
$query = "SELECT COUNT(nama_masakan) AS total_items FROM transaksi";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalItems = $row['total_items'] ?? 0; // Default ke 0 jika tidak ada data
} else {
    $totalItems = 0;
}
// Query untuk Total Revenue saat ini
$queryRevenue = "SELECT SUM(total_payment) AS income FROM transaksi WHERE MONTH(date) = MONTH(CURRENT_DATE())";
$resultRevenue = mysqli_query($conn, $queryRevenue);
$rowRevenue = mysqli_fetch_assoc($resultRevenue);
$income = $rowRevenue['income'] ?? 0;

// Query untuk Total Revenue bulan sebelumnya
$queryPreviousRevenue = "SELECT SUM(total_payment) AS income FROM transaksi WHERE MONTH(date) = MONTH(CURRENT_DATE()) - 1";
$resultPreviousRevenue = mysqli_query($conn, $queryPreviousRevenue);
$rowPreviousRevenue = mysqli_fetch_assoc($resultPreviousRevenue);
$previousIncome = $rowPreviousRevenue['income'] ?? 0;

// Hitung perubahan revenue
$revenueChange = $previousIncome > 0 ? (($income - $previousIncome) / $previousIncome) * 100 : 100;

// Query to count total orders
$orderQuery = "SELECT COUNT(DISTINCT id_order) AS total_orders FROM transaksi";
$orderResult = mysqli_query($conn, $orderQuery);
$totalOrders = 0;
if ($orderResult && $row = mysqli_fetch_assoc($orderResult)) {
    $totalOrders = $row['total_orders'];
}


// Query untuk Total Orders bulan sebelumnya
$queryPreviousOrders = "SELECT COUNT(*) AS total_orders FROM transaksi WHERE MONTH(date) = MONTH(CURRENT_DATE()) - 1";
$resultPreviousOrders = mysqli_query($conn, $queryPreviousOrders);
$rowPreviousOrders = mysqli_fetch_assoc($resultPreviousOrders);
$previousOrders = $rowPreviousOrders['total_orders'] ?? 0;

// Hitung perubahan orders
$orderChange = $previousOrders > 0 ? (($totalOrders - $previousOrders) / $previousOrders) * 100 : 100;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Meifang Resto - Sales Report </title>
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

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../assets/css/demo.css" />
  </head>
  <body>
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
                <!-- <li class="nav-item">
                <a href="../tables/user_manager.php">
                  <i class="fas fa-users"></i>
                  <p>User Manager</p>
                </a>
              </li> -->
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
              <li class="nav-item active">
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
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div>
                  <input
                    type="text"
                    placeholder="Search ..."
                    class="form-control"
                  />
                </div>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
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
                          <p class="text-muted"><?php echo htmlspecialchars(string: $username); ?></p>
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

        <div class="container">
          <div class="page-inner">
            <h3 class="fw-bold mb-3">Sales Report </h3>
            <div class="page-category">
            The sales report provides an overview of our company's monthly performance
            </div>
            <div class="row">
           
            <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Bar Chart</div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-sm-6 col-md-5 col-xl-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h4><b>Todays Revenue</b></h4>
                    <p class="text-muted">All Today Revenue</p>
                </div>
                <h3 class="text-info fw-bold">Rp<?php echo number_format($todayIncome, 2); ?></h3>
            </div>
            <div class="progress progress-sm">
                <?php
                $targetIncome = 800;
                $progress = ($todayIncome / $targetIncome) * 100;
                $progress = $progress > 100 ? 100 : $progress;
                ?>
                <div
                    class="progress-bar bg-info"
                    role="progressbar"
                    style="width: <?php echo $progress; ?>%"
                    aria-valuenow="<?php echo $progress; ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <p class="text-muted mb-0">Progress</p>
                <p class="text-muted mb-0"><?php echo number_format($progress, 2); ?>%</p>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-6 col-xl-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h5><b>Month <br>Revenue</b></h5>
                    <p class="text-muted">Month Revenue</p>
                </div>
                <h3 class="text-danger fw-bold">Rp<?php echo number_format($income); ?></h3>            </div>
            <?php
            $targetItems = 1000;
            $progress = ($totalItems / $targetItems) * 10000;
            $progress = $progress > 1000 ? 1000 : $progress;
            ?>
            <div class="progress progress-sm">
                <div
                    class="progress-bar bg-danger"
                    role="progressbar"
                    style="width: <?php echo $progress; ?>%;"
                    aria-valuenow="<?php echo $progress; ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <p class="text-muted mb-0">Progress</p>
                <p class="text-muted mb-0"><?php echo number_format($revenueChange); ?>%</p>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-6 col-xl-3 align-self-stretch">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h5><b>Item<br> Sales</b></h5>
                    <p class="text-muted">All Item Sales</p>
                </div>
                <h3 class="text-success fw-bold"><?php echo $totalItems; ?></h3>
            </div>
            <div class="progress progress-sm">
                <?php
                $targetItems = 500;
                $progress = ($totalItems / $targetItems) * 100;
                $progress = $progress > 100 ? 100 : $progress;
                ?>
                <div
                    class="progress-bar bg-success"
                    role="progressbar"
                    style="width: <?php echo $progress; ?>%"
                    aria-valuenow="<?php echo $progress; ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <p class="text-muted mb-0">Progress</p>
                <p class="text-muted mb-0"><?php echo number_format($progress, 3); ?>%</p>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-6 col-xl-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h5><b>New <br> Orders</b></h5>
                    <p class="text-muted">All New Orders</p>
                </div>
                <h3 class="text-secondary fw-bold"><?php echo $totalOrders; ?></h3>
            </div>
            <?php
            $targetItems = 1000;
            $progress = ($totalItems / $targetItems) * 1000;
            $progress = $progress > 100 ? 100 : $progress;
            ?>
            <div class="progress progress-sm">
                <div
                    class="progress-bar bg-secondary"
                    role="progressbar"
                    style="width: <?php echo $progress; ?>%;"
                    aria-valuenow="<?php echo $progress; ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <p class="text-muted mb-0">Progress</p>
                <p class="text-muted mb-0"><?php echo number_format($orderChange, 2); ?>%</p>
            </div>
        </div>
    </div>
</div>
            </div>
              <!-- <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <div class="card-title">Chart with HTML Legends</div>
                  </div>
                  <div class="card-body">
                    <div class="card-sub">
                      Sometimes you need a very complex legend. In these cases,
                      it makes sense to generate an HTML legend. Charts provide
                      a generateLegend() method on their prototype that returns
                      an HTML string for the legend.
                    </div>
                    <div class="chart-container">
                      <canvas id="htmlLegendsChart"></canvas>
                    </div>
                    <div id="myChartLegend"></div>
                  </div>
                </div>
              </div> -->
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
        <div class="custom-toggle">
          <i class="icon-settings"></i>
        </div>
      </div>
      <!-- End Custom template -->
    </div>
    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo2.js"></script>
    <script>
  // Data dari PHP
  var months = <?php echo $monthsJSON; ?>;
  var payments = <?php echo $paymentsJSON; ?>;

  var barChart = document.getElementById("barChart").getContext("2d");

  var myBarChart = new Chart(barChart, {
    type: "bar",
    data: {
      labels: months, // Menggunakan nama bulan dari database
      datasets: [
        {
          label: "Total Sales",
          backgroundColor: "rgb(23, 125, 255)",
          borderColor: "rgb(23, 125, 255)",
          data: payments, // Menggunakan total pembayaran dari database
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [
          {
            ticks: {
              beginAtZero: true,
            },
          },
        ],
      },
    },
  });
</script>

  </body>
</html>
