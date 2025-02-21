 <?php
session_start();
include 'koneksi.php'; // Include your database connection file

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


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Meifang Resto - Order Menu</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link rel="icon" href="../assets/img/meifang_resto_logo/2.svg" type="image/x-icon"/>
<!-- Bootstrap JS, jQuery, and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>


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
    <link rel="stylesheet" href="../assets/css/meifang.css"/>

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../assets/css/demo.css" />
  </head>
  <style>
   
</style>

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
              <!-- <li class="nav-item ">
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
                  <p>Daftar Menu</p>
                </a>
              </li>
              <li class="nav-item active">
                <a href="../forms/order_menu.php">
                  <i class="fas fa-utensils"></i>
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
                <a href="../forms/table.php">
                  <i class="fas fa-calendar-check"></i>
                  <p>Number Table</p>
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
                <div class="collapse" id="tables">
                  <ul class="nav nav-collapse">
                    <li>
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

        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Order Menu</h3>
              <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                  <a href="#">
                    <i class="icon-home"></i>
                  </a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Order Menu</a>
                </li>
              </ul>
            </div>
            <div class="search-section mb-3">
    <div class="row">
        <div class="col-md-8">
            <input type="text" id="searchInput" class="form-control" placeholder="Search menu by name...">
        </div>
        <div class="col-md-4">
          <select id="priceFilter" class="form-control">
              <option value="all">All Prices</option>
              <option value="best-seller">Best Seller</option>   
              <option value="under-30">Under Rp 30.000</option>
              <option value="30-50">Rp 30.000 - Rp 50.000</option>
              <option value="above-50">Above Rp 50.000</option>
          </select>
      </div>
    </div>
</div>

    <div class="row">
      <div class="col-md-12">
        <!-- <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 col-lg-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">
                  Pesan Makanan
                </button>
                <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Form Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form method="post">
                          <div class="mb-3">
                            <label for="datetime" class="form-label">Tanggal & Waktu</label>
                            <input type="text" class="form-control" id="datetime" name="datetime" readonly value="<?php echo date('Y-m-d H:i:s'); ?>">
                          </div>
                          <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="id_user" placeholder="Masukkan nama" required>
                          </div>
                          <div class="mb-3">
                            <label for="noMeja" class="form-label">No Meja / No Pesanan</label>
                            <input type="number" class="form-control" id="noMeja" name="no_meja" placeholder="Masukkan nomor meja/pesanan" required>
                          </div>
                          <div class="mb-3">
                            <label for="tipeMasakan" class="form-label">Tipe Masakan</label>
                            <select class="form-select" id="tipeMasakan" name="status_order" required>
                              <option value="dine-in">Dine In</option>
                              <option value="dine-out">Dine Out</option>
                            </select>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Pesan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    <!-- Modal -->
    <div class="container mt-2">
    <!-- Modal for Adding Menu Item -->
    <div class="modal fade" id="addMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="addMenuItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
       
            <form action="add_menu.php" method="post" enctype="multipart/form-data">
              <div class="modal-body">
                  <div class="form-group">
                      <label for="menuImage">Upload Image <span style="color: red;">*</span></label>
                      <input type="file" class="form-control" id="menuImage" name="menuImage" required>
                  </div>
                  <div class="form-group">
                      <label for="menuName">Menu Name <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="menuName" name="menuName" required>
                  </div>
                  <div class="form-group">
                      <label for="price">Price <span style="color: red;">*</span></label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text">Rp</span>
                          </div>
                          <input type="text" class="form-control" id="price" name="price" placeholder="Add Harga" step="0.01" required oninput="formatPrice(this)">
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" id="status" name="status">
                          <option value="ready">Ready</option>
                          <option value="empty">Empty</option>
                      </select>
                  </div>
                                  <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-control" id="category" name="category" onchange="toggleCategoryInput()">
                        <option value="main_course">Main Course</option>
                        <option value="drink">Non Coffee</option>
                        <option value="coffentea">Coffee & Tea</option>
                        <option value="milks">Milks & Smoothies</option>
                        <option value="snack">Snack</option>
                        <option value="dessert">Dessert</option>
                        <option value="new">+ Add Category</option>
                    </select>
                </div>

                <div class="form-group" id="newCategoryDiv" style="display: none;">
                    <label for="newCategory">New Category</label>
                    <input type="text" class="form-control" id="newCategory" name="newCategory">
                    <button type="button" class="btn btn-primary mt-2" onclick="saveCategory()">Save</button>
                </div>


               
                  <div class="form-group">
                      <label for="stock_menu">Stock</label>
                      <input type="number" class="form-control" id="stock_menu" name="stock_menu" min="0" title="Please enter a valid number">
                  </div>
                  <div class="form-group">
                      <label for="note">Note</label>
                      <textarea class="form-control" id="note" name="note"></textarea>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Add Item</button>
              </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Menu Selection Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="menu-selection">
            <ul class="nav nav-pills nav-primary">
                <li class="nav-item">
                    <a class="nav-link active" href="#" onclick="filterSelection('all')">ALL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterSelection('filter-main_course')">MAIN COURSE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterSelection('filter-snack')">SNACK</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterSelection('filter-dessert')">DESSERT</a>
                    <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterSelection('filter-drink')">DRINK</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterSelection('filter-coffentea')">COFFE & TEA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterSelection('filter-milks')">MILKS & SMOTHIES</a>
                </li>
                </li>
            </ul>
        </div>
        <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 20px;">
    <button id="orderButton" class="btn btn-success" style="display: none;" onclick="placeOrder()">Tambah Order</button>
    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#addMenuItemModal">
        <span aria-hidden="true">
            <i class="fas fa-plus"></i> 
        </span>
        Add Menu 
          </button>
          
</div>
    </div>
 

<!-- Menu Items Display Section -->
<div class="row no-gutters">
    <?php
        // Function to render menu items by category
        function renderMenuItems($category, $folder) {
            // Koneksi ke database
            $conn = mysqli_connect('localhost', 'root', '', 'apk_kasir');

            // Cek koneksi
            if (!$conn) {
                die("Koneksi gagal: " . mysqli_connect_error());
            }

            // Query untuk mendapatkan data berdasarkan kategori
            $query = "SELECT * FROM masakan WHERE category = '$category'";
            $result = mysqli_query($conn, $query);

            // Periksa apakah ada data yang ditemukan
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Tampilkan setiap menu dalam bentuk card
                    echo '
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mb-2 portfolio-item filter-' . $category . '">
                        <div class="card h-90 text-center border-light position-relative" 
                            data-id-masakan="' . $row['id_masakan'] . '" data-stock-menu="' . $row['stock_menu'] . '">
                            <!-- Delete button -->
                           
                            <img src="../assets/img/' . $folder . '/' . $row['image'] . '" alt="menuimage" class="card-img-top menu-image">
                            <div class="c-body position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white portfolio-info">
                                <div>
                                    <h4 style="font-size: 16px; margin-top: -15px; margin-bottom: 2px;">' . htmlspecialchars($row['nama_masakan']) . '</h4>
                                    <p style="font-size: 14px; margin: -2px 0 2px 0;">Rp ' . number_format($row['harga'], 3, ',', '.') . '</p>
                                </div>
                                <div class="position-absolute d-flex gap-1" style="top: 5px; right: 6px; z-index: 2;">
                                  <!-- Delete Button -->
                                  <button class="btn btn-danger btn-sm p-0" style="font-size: 12px; width: 24px; height: 24px;" onclick="deleteMenu(' . $row['id_masakan'] . ')">
                                  <i class="fas fa-times"></i>
                              </button>
                           
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

            // Tutup koneksi
            mysqli_close($conn);
        }

        // Render items for each category
        renderMenuItems('main_course', 'maincourse');
        renderMenuItems('snack', 'snack');
        renderMenuItems('dessert', 'dessert');
        renderMenuItems('drink', 'drinks');
        renderMenuItems('coffentea', 'coffentea');
        renderMenuItems('milks', 'milks');
    ?>
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
        </footer>
      </div> -->

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

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Google Maps Plugin -->
    <script src="../assets/js/plugin/gmaps/gmaps.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo2.js"></script>
<script>
    // Menampilkan tanggal & waktu otomatis
    document.addEventListener('DOMContentLoaded', (event) => {
        const now = new Date();
        document.getElementById('datetime').value = now.toLocaleString();
    });
</script>

<script>
function updateOrderButtonVisibility() {
    const quantities = document.querySelectorAll('#quantity');
    const orderButton = document.getElementById('orderButton');

    if (!orderButton) {
        console.error('Element orderButton tidak ditemukan.');
        return;
    }

    let totalQuantity = 0;

    quantities.forEach(quantityElement => {
        const quantity = parseInt(quantityElement.textContent) || 0;
        totalQuantity += quantity;
    });

    // Tampilkan tombol jika ada setidaknya satu item dengan quantity > 0
    orderButton.style.display = totalQuantity > 0 ? 'block' : 'none';
}

function increaseQuantity(button) {
    const card = button.closest('.card');
    if (!card) {
        console.error('Card not found.');
        return;
    }

    const quantityElement = card.querySelector('#quantity');
    if (!quantityElement) {
        console.error('Quantity element not found.');
        return;
    }

    const stock = parseInt(card.getAttribute('data-stock-menu')) || 0;
    let quantity = parseInt(quantityElement.textContent) || 0;

    if (quantity < stock) {
        quantity += 1;
        quantityElement.textContent = quantity;

        // Add green-border if quantity > 0
        card.classList.add('green-border');
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Insufficient Stock!',
            text: 'The selected quantity exceeds available stock.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }

    updateOrderButtonVisibility();
}


function decreaseQuantity(button) {
    const card = button.closest('.card');
    if (!card) {
        console.error('Card tidak ditemukan.');
        return;
    }

    const quantityElement = card.querySelector('#quantity');
    if (!quantityElement) {
        console.error('Element quantity tidak ditemukan.');
        return;
    }

    let quantity = parseInt(quantityElement.textContent) || 0;

    if (quantity > 0) {
        quantity -= 1;
        quantityElement.textContent = quantity;

        // Hapus green-border jika quantity kembali ke 0
        if (quantity === 0) {
            card.classList.remove('green-border');
        }
    }

    updateOrderButtonVisibility();
}
function placeOrder() {
    const selectedItems = [];
    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        const quantityEl = card.querySelector('#quantity');
        if (!quantityEl) return; // Skip jika elemen tidak ditemukan

        const quantity = parseInt(quantityEl.textContent) || 0;
        const stock = parseInt(card.getAttribute('data-stock-menu')) || 0;
        const masakanName = card.getAttribute('data-id-masakan');
        if (!masakanName) return; // Pastikan masakan memiliki ID

        const priceEl = card.querySelector('p:nth-of-type(2)');
        const priceText = priceEl ? priceEl.textContent.replace(/Rp\s?|[.,]/g, '') : '0';
        const price = parseInt(priceText) || 0;

        if (quantity > 0 && quantity <= stock) {
            selectedItems.push({
                id_masakan: masakanName,
                quantity: quantity,
                price: price,
                stock_menu: stock
            });
        }
    });

    if (selectedItems.length > 0) {
    console.log('Data yang dikirim ke server:', JSON.stringify(selectedItems)); // Debug log
    fetch('process_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(selectedItems)
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug log
        return response.json();
    })
    .then(data => {
        console.log('Response dari server:', data); // Debug log
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Order berhasil disimpan!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'detail_order.php'; // Arahkan ke halaman detail_order.php
            });
        } else {
            console.error('Server Response Errors:', data.errors || 'Terjadi kesalahan saat menyimpan order.');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat menyimpan order.',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error); // Debug log
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Terjadi kesalahan saat memproses order.',
            confirmButtonText: 'OK'
        });
    });
} else {
    Swal.fire({
        icon: 'warning',
        title: 'Perhatian!',
        text: 'Tidak ada item yang dipilih atau stok tidak mencukupi.',
        confirmButtonText: 'OK'
    });
}

}


</script>




<scrip>
  <!-- Script untuk filtering -->
<script>
function filterSelection(category) {
    // Hapus kelas 'active' dari semua tab nav-link
    var links = document.getElementsByClassName('nav-link');
    for (var i = 0; i < links.length; i++) {
        links[i].classList.remove('active');
    }
    
    // Tambahkan kelas 'active' ke elemen yang diklik
    event.currentTarget.classList.add('active');
    
    // Dapatkan semua elemen dengan class 'portfolio-item'
    var items = document.getElementsByClassName('portfolio-item');
    
    // Jika 'all' dipilih, tampilkan semua elemen
    if (category === 'all') {
        for (var i = 0; i < items.length; i++) {
            items[i].style.display = 'block';
        }
    } else {
        // Sembunyikan semua elemen
        for (var i = 0; i < items.length; i++) {
            items[i].style.display = 'none';
        }
        
        // Tampilkan elemen yang memiliki class yang dipilih
        var selectedItems = document.getElementsByClassName(category);
        for (var j = 0; j < selectedItems.length; j++) {
            selectedItems[j].style.display = 'block';
        }
    }
}
</script>
<script>
function addMenuItem() {
    // Meminta input dari pengguna untuk ID, stock, nama menu, dan harga
    const idMenu = prompt("Masukkan ID menu:");
    const stockMenu = prompt("Masukkan jumlah stock menu:");
    const namaMenu = prompt("Masukkan nama menu:");
    const hargaMenu = prompt("Masukkan harga menu (misalnya: Rp 21.200):");

    // Mendapatkan file gambar dari input file
    const imageInput = document.getElementById('imageInput');
    if (!imageInput.files.length) {
        alert("Harap pilih gambar terlebih dahulu!");
        return;
    }
    
    const file = imageInput.files[0];

    // Membaca file gambar menggunakan FileReader
    const reader = new FileReader();
    reader.onload = function(e) {
        const imageUrl = e.target.result; // URL data gambar

        // Membuat elemen baru
        const newItem = document.createElement('div');
        newItem.className = 'col-12 col-sm-6 col-md-3 col-lg-2 mb-2 portfolio-item filter-dessert';
        newItem.innerHTML = `
            <div class="card h-90 text-center border-light position-relative" data-id-masakan="${idMenu}" data-stock-menu="${stockMenu}">
                <img src="${imageUrl}" alt="menuimage" class="card-img-top menu-image">
                <div class="c-body position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white portfolio-info">
                    <div>
                        <h4 style="font-size: 16px; margin-bottom: 1px;">${namaMenu}</h4>
                        <p style="font-size: 14px; margin-top: 0;">${hargaMenu}</p>
                    </div>
                </div>
                <div class="quantity-control position-absolute d-flex align-items-center justify-content-center" style="bottom: 10px; left: 50%; transform: translateX(-50%);">
                    <button onclick="decreaseQuantity(this)">-</button>
                    <span id="quantity" style="color: black;">0</span>
                    <button onclick="increaseQuantity(this)">+</button>
                </div>
            </div>
        `;

        // Menambahkan elemen baru ke dalam container di atas elemen lainnya
        document.getElementById('menuContainer').prepend(newItem);
    };

    // Membaca file sebagai URL data
    reader.readAsDataURL(file);
}



</script>
<script>
    const searchInput = document.getElementById('searchInput');
    const priceFilter = document.getElementById('priceFilter');
    const menuItems = document.querySelectorAll('.portfolio-item');

    function filterMenu() {
        const searchValue = searchInput.value.toLowerCase();
        const priceValue = priceFilter.value;

        menuItems.forEach(item => {
            const name = item.querySelector('h4').textContent.toLowerCase();
            const price = parseInt(item.querySelector('p').textContent.replace(/[^\d]/g, ''));
            const isBestSeller = item.getAttribute('data-bestseller') === 'true';

            let isVisible = true;

            // Filter by name
            if (searchValue && !name.includes(searchValue)) {
                isVisible = false;
            }

            // Filter by price
            if (priceValue === 'under-30' && price >= 30000) {
                isVisible = false;
            } else if (priceValue === '30-50' && (price < 30000 || price > 50000)) {
                isVisible = false;
            } else if (priceValue === 'above-50' && price <= 50000) {
                isVisible = false;
            } else if (priceValue === 'best-seller' && !isBestSeller) {
                isVisible = false;
            }

            // Show or hide item based on filters
            item.style.display = isVisible ? 'block' : 'none';
        });
    }

    searchInput.addEventListener('input', filterMenu);
    priceFilter.addEventListener('change', filterMenu);
</script>

<script>
    function formatPrice(input) {
        let value = input.value.replace(/[^\d]/g, ''); // Remove non-digit characters
        input.value = new Intl.NumberFormat('id-ID', { style: 'decimal' }).format(value);
    }
</script>
<script>
function deleteMenu(idMasakan) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX request to delete menu
            fetch(`delete_menu.php?id_masakan=${idMasakan}`, { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "The menu has been deleted.",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload(); // Reload page after deletion
                        });
                    } else {
                        Swal.fire({
                            title: "Failed!",
                            text: "Failed to delete the menu.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        title: "Error!",
                        text: "There was a problem processing your request.",
                        icon: "error"
                    });
                });
        }
    });
}


</script>
<script>
function toggleCategoryInput() {
    var category = document.getElementById("category").value;
    var newCategoryDiv = document.getElementById("newCategoryDiv");

    if (category === "new") {
        newCategoryDiv.style.display = "block";
    } else {
        newCategoryDiv.style.display = "none";
    }
}

function saveCategory() {
    var newCategory = document.getElementById("newCategory").value.trim();

    if (newCategory === "") {
        alert("Please enter a category name.");
        return;
    }

    // Kirim data ke server untuk disimpan di database
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "save_category.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Category saved successfully!");

            // Tambahkan kategori baru ke dalam dropdown
            var categorySelect = document.getElementById("category");
            var newOption = document.createElement("option");
            newOption.value = newCategory.toLowerCase().replace(/\s+/g, "_");
            newOption.textContent = newCategory;
            categorySelect.insertBefore(newOption, categorySelect.lastElementChild);

            // Reset input field dan sembunyikan form tambah category
            document.getElementById("newCategory").value = "";
            document.getElementById("newCategoryDiv").style.display = "none";
            categorySelect.value = newOption.value;
        }
    };
    xhr.send("category=" + encodeURIComponent(newCategory));
}
</script>

  </body>
</html>
