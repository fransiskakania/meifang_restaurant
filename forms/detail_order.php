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

// Step 1: Fetch the total payment
$query_total_payment = "SELECT SUM(price * quantity) AS total_payment FROM orders";
$result_total_payment = mysqli_query($conn, $query_total_payment);

if ($result_total_payment) {
    $row_total_payment = mysqli_fetch_assoc($result_total_payment);
    // Format the total payment to have 2 decimal places
    $total_payment = number_format($row_total_payment['total_payment'], 2, '.', '');
} else {
    $total_payment = '0.00'; // Fallback in case of an error, ensure it's a string with 2 decimals
}

// Step 2: Fetch the order details
$query_order_details = "SELECT id_order, id_masakan, quantity, price FROM orders";
$result_order_details = mysqli_query($conn, $query_order_details);
if (isset($_GET['status'])) {
  $status = $_GET['status'];
  $message = isset($_GET['message']) ? $_GET['message'] : '';

  echo "
      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              ";
  if ($status === 'success') {
      echo "
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Order confirmed successfully!',
                  confirmButtonText: 'OK'
              }).then(function() {
                  window.location.href = 'transaction_order.php';
              });
              ";
  } elseif ($status === 'error') {
      echo "
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: '$message',
                  confirmButtonText: 'OK'
              }).then(function() {
                  window.location.href = 'order_menu.php';
              });
              ";
  }
  echo "
          });
      </script>
  ";
}




?>


<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <title>Meifang Resto - Detail Order</title>
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
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
              <li class="nav-item active">
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
          <div class="container mt-15">
    <div class="card">
        <div class="card-body">
            <div id="cItemsContainer" class="d-flex justify-content-center align-items-center flex-wrap">
                <div class="col-12 col-md-10 col-lg-10">
                    <!-- Display order details in HTML table -->
                    <div class="order-summary border rounded p-3 bg-light">
                        <h4 class="text-center mb-4">Order Summary</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id Menu</th>
                                    <th>Menu Name</th>
                                    <th>Stock Menu</th> <!-- Tambahkan kolom Stock Menu -->
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="orderTable">
                                <?php
                                $query = "SELECT * FROM orders";
                                $result = mysqli_query($conn, $query);
                                $no = 1;
                                $total_payment = 0;

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $total_harga = $row['quantity'] * $row['price'];
                                    $total_payment += $total_harga;
                                ?>
                                    <tr data-id="<?= $row['id_masakan'] ?>" data-price="<?= $row['price'] ?>">
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['id_masakan'] ?></td>
                                        <td><?= $row['nama_masakan'] ?></td>
                                        <td><?= $row['stock_menu'] ?></td> <!-- Tambahkan data Stock Menu -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-sm btn-outline-secondary px-2 py-0 quantity-btn" onclick="updateQuantity(this, -1)">-</button>
                                                <input type="text" class="form-control text-center mx-1 quantity-input" value="<?= $row['quantity'] ?>" readonly style="width: 50px;">
                                                <button class="btn btn-sm btn-outline-secondary px-2 py-0 quantity-btn" onclick="updateQuantity(this, 1)">+</button>
                                            </div>
                                        </td>
                                        <td class="item-total">Rp <?= number_format($total_harga, 3, ',', '.') ?></td>
                                        <td>
    <button class="btn btn-danger btn-sm" onclick="deleteOrderItem(<?= $row['id_order'] ?>)">
        <i class="fas fa-trash"></i>
    </button>
</td>

                                    </tr>
                                <?php } ?>
                            </tbody>

                            </table>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Total Payment:</strong></span>
                            <span id="total">Rp <?= number_format($total_payment, 3, ',', '.') ?></span>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between">
                            <button class="btn btn-danger mb-2 mb-md-0 w-100 me-md-2" onclick="deleteOrder()">Delete Order</button>
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#confirmOrderModal">Confirm Order</button>
                            </div>
                      <!-- Modal Form untuk Confirm Order -->
                    <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmOrderLabel">Confirm Order</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="confirm_order.php">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="tanggal" class="form-label">Tanggal</label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_role" class="form-label">User Role</label>
                                            <input type="text" class="form-control" id="user_role" name="user_role" value="<?php echo htmlspecialchars($nama_lengkap); ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_meja" class="form-label">No Meja</label>
                                            <input type="number" class="form-control" id="no_meja" name="no_meja" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type_order" class="form-label">Type Order</label>
                                            <select class="form-select" id="type_order" name="type_order">
                                                <option value="Dine In">Dine In</option>
                                                <option value="Dine Out">Dine Out</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
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
        document.addEventListener('DOMContentLoaded', () => {
            const cartItemsContainer = document.getElementById('cartItemsContainer');
            const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

            if (cartItems.length > 0) {
                cartItems.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.innerHTML = `
                        <p>Nama: ${item.name}</p>
                        <p>Harga: ${item.price}</p>
                        <p>Jumlah: ${item.quantity}</p>
                        <hr>
                    `;
                    cartItemsContainer.appendChild(itemElement);
                });
            } else {
                cartItemsContainer.textContent = 'Keranjang Anda kosong.';
            }
        });
    </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteOrder() {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_orders.php";
        }
    });
}
</script>
<script>
// When the Edit button is clicked, populate the modal with the current values
$('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var id_order = button.data('id_order');
    var id_masakan = button.data('id_masakan');
    var quantity = button.data('quantity');
    var price = button.data('price');
    
    // Populate the modal fields with the current values
    var modal = $(this);
    modal.find('#id_order').val(id_order);
    modal.find('#id_masakan').val(id_masakan);
    modal.find('#quantity').val(quantity);
    modal.find('#price').val(price);
});

</script>

<script>
// Function to update the total payment
function updateTotalPayment() {
    let totalPayment = 0;
    const rows = document.querySelectorAll('#orderTable tr');

    // Loop through each row to calculate the total payment
    rows.forEach(row => {
        const price = parseFloat(row.dataset.price); // Retrieve the price from data attributes
        const quantity = parseInt(row.querySelector('.quantity-input').value); // Retrieve the quantity from the input field
        totalPayment += price * quantity; // Add the cost for this item
    });

    // Update the displayed total payment value
    document.getElementById('total').textContent = `Rp ${totalPayment.toLocaleString('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 })}`;
}

// Function to update quantity when the button is clicked
function updateQuantity(button, change) {
    const row = button.closest('tr');
    const quantityInput = row.querySelector('.quantity-input');
    const price = parseFloat(row.dataset.price);
    let quantity = parseInt(quantityInput.value);
    const idMasakan = row.dataset.id; // Get id_masakan from data-id

    quantity += change;
    if (quantity < 1) quantity = 1; // Minimum quantity is 1

    quantityInput.value = quantity;
    const itemTotal = row.querySelector('.item-total');
    const total = quantity * price;
    itemTotal.textContent = `Rp ${total.toLocaleString('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 })}`;

    // Send request to update stock on server
    updateStock(idMasakan, quantity);

    // Update total payment
    updateTotalPayment();
}

// Function to update stock in the database
function updateStock(idMasakan, quantity) {
    const row = document.querySelector(`tr[data-id="${idMasakan}"]`);
    const currentStock = parseInt(row.dataset.stock);

    const newStock = currentStock - quantity;

    // Ensure newStock is valid
    if (isNaN(newStock) || newStock < 0) {
        console.error('Invalid stock value');
        return;
    }

    console.log('Sending data to server:', { id_masakan: idMasakan, new_stock: newStock });

    fetch('update_stock.php', {
        method: 'POST',
        body: JSON.stringify({ id_masakan: idMasakan, quantity: quantity, new_stock: newStock }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Stock successfully updated!');
        } else {
            alert(data.errors.join('\n'));
        }
    })
    .catch(error => {
        console.error('Error updating stock:', error);
    });
}

</script>
<script> 
function deleteOrderItem(orderId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Pesanan ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to delete_order_item.php with order ID as a GET parameter
            window.location.href = "delete_order_item.php?id=" + orderId;
        }
    });
}


</script>
<script> 

function confirmPayment() {
    // Populate the modal with necessary data
    document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
    document.getElementById('userRole').value = 'User Role Here'; // Replace with actual value based on session or context

    // Show the modal
    var myModal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
    myModal.show();
}

function submitOrder() {
    const formData = new FormData(document.getElementById('orderConfirmationForm'));
    
    // Collect form data
    const data = {
        tanggal: formData.get('tanggal'),
        userRole: formData.get('userRole'),
        name: formData.get('name'),
        noMeja: formData.get('noMeja'),
        typeOrder: formData.get('typeOrder')
    };

    // Send data to server for processing (e.g., using fetch or XMLHttpRequest)
    fetch('confirm_order.php', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert('Order Confirmed');
        // window.location.reload(); // Reload to refresh the state if needed
    })
    .catch(error => console.error('Error:', error));
}

</script>
<script> 
// Add event listener to capture selected payment method
document.querySelectorAll('.payment-option').forEach(function(option) {
    option.addEventListener('click', function() {
        const paymentMethod = this.getAttribute('data-value');
        document.getElementById('payment_with').value = paymentMethod;  // Setting the hidden field value
    });
});


</script>
  </body>
</html>
