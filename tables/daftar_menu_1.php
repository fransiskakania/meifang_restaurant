<?php
session_start();
include 'koneksi2.php'; // Include your database connection file

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
// Handle form submission to add a new menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menuName = mysqli_real_escape_string($conn, $_POST['menuName']);
    $menuPrice = mysqli_real_escape_string($conn, $_POST['menuPrice']);
    $menuStatus = mysqli_real_escape_string($conn, $_POST['menuStatus']);
    $menuStock = mysqli_real_escape_string($conn, $_POST['menuStock']);
    $menuNote = mysqli_real_escape_string($conn, $_POST['menuNote']);
    $menuCategory = mysqli_real_escape_string($conn, $_POST['menuCategory']); // New category field

    // Handle image upload
    $image = $_FILES['menuImage']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    
    if (move_uploaded_file($_FILES['menuImage']['tmp_name'], $target_file)) {
        // File uploaded successfully
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
    // Insert data into `masakan` table
    $sql = "INSERT INTO masakan (image, nama_masakan, harga, status_masakan, stock_menu, category, note) 
    VALUES ('$image', '$menuName', '$menuPrice', '$menuStatus', '$menuStock', '$menuCategory', '$menuNote')";

    if (mysqli_query($conn, $sql)) {
        header("Location: daftar_menu_1.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch data for each category
$maincourses = mysqli_query($conn, "SELECT * FROM masakan WHERE category = 'main_course'");
$snacks = mysqli_query($conn, "SELECT * FROM masakan WHERE category = 'snack'");
$dessert = mysqli_query($conn, "SELECT * FROM masakan WHERE category = 'dessert'");
// Other categories (if needed)
$softdrinks = mysqli_query($conn, "SELECT * FROM masakan WHERE category = 'drink'");
$coffentea = mysqli_query($conn, "SELECT * FROM masakan WHERE category = 'coffentea'");
$milks = mysqli_query($conn, "SELECT * FROM masakan WHERE category = 'milks'");



// Retrieve existing menu items from the `masakan` table
$menus = mysqli_query($conn, "SELECT * FROM masakan");

// // Now log $maincourses in JavaScript
// echo "<script>";
// echo "console.log('Main Courses:');";

// // Loop through the result and log each row as an object
// while ($row = mysqli_fetch_assoc($maincourses)) {
//     echo "console.log(" . json_encode($row) . ");";
// }

// echo "</script>";
?>
 <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Data berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
        ";
    }

    if (isset($_GET['error'])) {
        $errorMessage = htmlspecialchars($_GET['error']); // Hindari XSS
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: '$errorMessage',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
        ";
    }
    ?>


 <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <title>Tables - Kaiadmin Bootstrap 5 Admin Dashboard</title>
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
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                  <a href="user_table.php">
                  <i class="fas fa-user-alt "></i>
                    <p>User Login</p>
                  </a>
                </li>
                <li class="nav-item ">
                <a href="./user_manager.php">
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
                <li class="nav-item active">
               
                <a href="./daftar_menu_1.php">
                  <i class="fas fa-book"></i>
                  <p>Daftar Menu </p>
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
              <!-- </li>
                <li class="nav-item">
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
                         src="../assets/img/profile/1.png"
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
                                   src="../assets/img/profile/1.png"
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
          
      <div class="container mt-10">
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
                <a href="#">Daftar Menu</a>
              </li>
            </ul>
          </div>
          <div class="row justify-content-center">
          <div class="col-md-12">
          <div class="card-body" style="padding-top: 5px;">
            <div class="card mb-5">
                  <div class="card-header d-flex justify-content-between align-items-center">
                      <h4 class="card-title">Main Course</h4>
                      <div class="input-group" style="width: 500px;">
                          <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                          <div class="input-group-append">
                              <span class="input-group-text"><i class="fas fa-search"></i></span>
                          </div>
                      </div>
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                          Tambah Data Menu
                      </button>
                  </div>
                  <div class="card-body">
                      <!-- Responsive Table -->
                      <div class="table-responsive">
                        <table class="table table-bordered" id="menuTable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                <th scope="col">Image</th>
                                    <th scope="col">Menu</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Stock</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Note</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $index = 1;
                            while ($row = mysqli_fetch_assoc($maincourses)) {
                                echo "<tr>";
                                echo "<td>" . $index++ . "</td>";
                                echo "<td><img src='uploads/" . $row['image'] . "' width='50'></td>";
                                echo "<td>" . $row['nama_masakan'] . "</td>";
                                echo "<td>" . $row['harga'] . "</td>";
                                echo "<td>" . $row['status_masakan'] . "</td>";
                                echo "<td>" . $row['stock_menu'] . "</td>";
                                echo "<td>" . $row['category'] . "</td>";
                                echo "<td>" . $row['note'] . "</td>";

                                echo "<td>";
                                echo "<div class='btn-group' role='group'>
                                    <button type='button' class='btn btn-sm btn-warning' style='margin-right: 3px;' data-toggle='modal' data-target='#updateMenuModal' 
                                        onclick=\"fillUpdateForm('" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "')\">
                                        <i class='fas fa-pen'></i>
                                    </button>
                                    <button type='button' class='btn btn-sm btn-danger' style='margin-left: 2px;' onclick=\"deleteMenu('" . $row['id_masakan'] . "')\">
                                        <i class='fas fa-times'></i>
                                    </button>
                                    <button type='button' class='btn btn-sm btn-info' style='margin-left: 4px;' data-toggle='modal' data-target='#viewMenuModal' 
                                        onclick=\"viewMenuDetails('" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "')\">
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

                      <!-- End of Responsive Table -->
                  </div>
              </div>
          </div>
      </div>

      <!-- Snack Section -->
<div class="row justify-content-center">
    <div class="col-md-12">
    <div class="card mb-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Snack</h4>
                <div class="input-group" style="width: 500px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                    Tambah Data Menu
                </button>
            </div>
            <div class="card-body">
                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="menuTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Stock</th>
                                <th>Category</th>
                                <th>Note</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                       <tbody>
                        <?php
                        $index = 1;
                        while ($row = mysqli_fetch_assoc($snacks)) {
                            echo "<tr>";
                            echo "<td>" . $index++ . "</td>";
                            echo "<td><img src='uploads/" . $row['image'] . "' width='50'></td>";
                            echo "<td>" . $row['nama_masakan'] . "</td>";
                            echo "<td>" . $row['harga'] . "</td>";
                            echo "<td>" . $row['status_masakan'] . "</td>";
                            echo "<td>" . $row['stock_menu'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['note'] . "</td>";

                            echo "<td>";
                           echo "<div class='btn-group' role='group'>
                                  <button type='button' class='btn btn-sm btn-warning' style='margin-right: 3px;' data-toggle='modal' data-target='#updateMenuModal' 
                                      onclick=\"fillUpdateForm('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-pen'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-danger' style='margin-left: 2px;' onclick=\"deleteMenu('" . $row['id_masakan'] . "')\">
                                      <i class='fas fa-times'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-info' style='margin-left: 4px;' data-toggle='modal' data-target='#viewMenuModal' 
                                      onclick=\"viewMenuDetails('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-eye'></i>
                                  </button>
                              </div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>

                    </table>
                </div>
                <!-- End of Responsive Table -->
            </div>
        </div>
    </div>
</div>

<!-- Dessert Section -->
<div class="row justify-content-center">
    <div class="col-md-12">
    <div class="card mb-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Dessert</h4>
                <div class="input-group" style="width: 500px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                    Tambah Data Menu
                </button>
            </div>
            <div class="card-body">
                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="menuTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Price</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Status</th>
                                <th scope="col">Stock</th>
                                <th>Category</th>
                                <th scope="col">Note</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 1;
                        while ($row = mysqli_fetch_assoc($dessert)) {
                            echo "<tr>";
                            echo "<td>" . $index++ . "</td>";
                            echo "<td><img src='uploads/" . $row['image'] . "' width='50'></td>";
                            echo "<td>" . $row['nama_masakan'] . "</td>";
                            echo "<td>" . $row['harga'] . "</td>";
                            echo "<td>" . $row['status_masakan'] . "</td>";
                            echo "<td>" . $row['stock_menu'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['note'] . "</td>";
                            echo "<td>";
                           echo "<div class='btn-group' role='group'>
                                  <button type='button' class='btn btn-sm btn-warning' style='margin-right: 3px;' data-toggle='modal' data-target='#updateMenuModal' 
                                      onclick=\"fillUpdateForm('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-pen'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-danger' style='margin-left: 2px;' onclick=\"deleteMenu('" . $row['id_masakan'] . "')\">
                                      <i class='fas fa-times'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-info' style='margin-left: 4px;' data-toggle='modal' data-target='#viewMenuModal' 
                                      onclick=\"viewMenuDetails('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-eye'></i>
                                  </button>
                              </div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>

                    </table>
                </div>
                <!-- End of Responsive Table -->
            </div>
        </div>
    </div>
</div>

<!-- Softdrink Section -->
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Non Coffe</h4>
                <div class="input-group" style="width: 500px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                    Tambah Data Menu
                </button>
            </div>
            <div class="card-body">
                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="menuTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Stock</th>
                                <th>Category</th>
                                <th scope="col">Note</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 1;
                        while ($row = mysqli_fetch_assoc($softdrinks)) {
                            echo "<tr>";
                            echo "<td>" . $index++ . "</td>";
                            echo "<td><img src='uploads/" . $row['image'] . "' width='50'></td>";
                            echo "<td>" . $row['nama_masakan'] . "</td>";
                            echo "<td>" . $row['harga'] . "</td>";
                            echo "<td>" . $row['status_masakan'] . "</td>";
                            echo "<td>" . $row['stock_menu'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['note'] . "</td>";
                            echo "<td>";
                           echo "<div class='btn-group' role='group'>
                                  <button type='button' class='btn btn-sm btn-warning' style='margin-right: 3px;' data-toggle='modal' data-target='#updateMenuModal' 
                                      onclick=\"fillUpdateForm('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-pen'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-danger' style='margin-left: 2px;' onclick=\"deleteMenu('" . $row['id_masakan'] . "')\">
                                      <i class='fas fa-times'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-info' style='margin-left: 4px;' data-toggle='modal' data-target='#viewMenuModal' 
                                      onclick=\"viewMenuDetails('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-eye'></i>
                                  </button>
                              </div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- End of Responsive Table -->
            </div>
        </div>
    </div>
</div>

<!-- coffentea Section -->
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Coffe & Tea</h4>
                <div class="input-group" style="width: 500px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                    Tambah Data Menu
                </button>
            </div>
            <div class="card-body">
                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="menuTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Stock</th>
                                <th>Category</th>
                                <th scope="col">Note</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 1;
                        while ($row = mysqli_fetch_assoc($coffentea)) {
                            echo "<tr>";
                            echo "<td>" . $index++ . "</td>";
                            echo "<td><img src='uploads/" . $row['image'] . "' width='50'></td>";
                            echo "<td>" . $row['nama_masakan'] . "</td>";
                            echo "<td>" . $row['harga'] . "</td>";
                            echo "<td>" . $row['status_masakan'] . "</td>";
                            echo "<td>" . $row['stock_menu'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['note'] . "</td>";
                            echo "<td>";
                           echo "<div class='btn-group' role='group'>
                                  <button type='button' class='btn btn-sm btn-warning' style='margin-right: 3px;' data-toggle='modal' data-target='#updateMenuModal' 
                                      onclick=\"fillUpdateForm('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-pen'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-danger' style='margin-left: 2px;' onclick=\"deleteMenu('" . $row['id_masakan'] . "')\">
                                      <i class='fas fa-times'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-info' style='margin-left: 4px;' data-toggle='modal' data-target='#viewMenuModal' 
                                      onclick=\"viewMenuDetails('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-eye'></i>
                                  </button>
                              </div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- End of Responsive Table -->
            </div>
        </div>
    </div>
</div>

<!-- $milks Section -->
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Milks & Smothies</h4>
                <div class="input-group" style="width: 500px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                    Tambah Data Menu
                </button>
            </div>
            <div class="card-body">
                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="menuTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Stock</th>
                                <th>Category</th>
                                <th scope="col">Note</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 1;
                        while ($row = mysqli_fetch_assoc($milks)) {
                            echo "<tr>";
                            echo "<td>" . $index++ . "</td>";
                            echo "<td><img src='uploads/" . $row['image'] . "' width='50'></td>";
                            echo "<td>" . $row['nama_masakan'] . "</td>";
                            echo "<td>" . $row['harga'] . "</td>";
                            echo "<td>" . $row['status_masakan'] . "</td>";
                            echo "<td>" . $row['stock_menu'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['note'] . "</td>";
                            echo "<td>";
                           echo "<div class='btn-group' role='group'>
                                  <button type='button' class='btn btn-sm btn-warning' style='margin-right: 3px;' data-toggle='modal' data-target='#updateMenuModal' 
                                      onclick=\"fillUpdateForm('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-pen'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-danger' style='margin-left: 2px;' onclick=\"deleteMenu('" . $row['id_masakan'] . "')\">
                                      <i class='fas fa-times'></i>
                                  </button>
                                  <button type='button' class='btn btn-sm btn-info' style='margin-left: 4px;' data-toggle='modal' data-target='#viewMenuModal' 
                                      onclick=\"viewMenuDetails('" . $row['id_masakan'] . "','" . $row['image'] . "', '" . $row['nama_masakan'] . "', '" . $row['harga'] . "', '" . $row['status_masakan'] . "', '" . $row['category'] . "', '" . $row['stock_menu'] . "',  '" . $row['note'] . "')\">
                                      <i class='fas fa-eye'></i>
                                  </button>
                              </div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- End of Responsive Table -->
            </div>
        </div>
    </div>
</div>


     <!-- Add Menu Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="daftar_menu_1.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="menuImage">Upload Gambar</label>
                        <input type="file" class="form-control" id="menuImage" name="menuImage" required>
                    </div>
                    <div class="form-group">
                        <label for="menuName">Menu Name</label>
                        <input type="text" class="form-control" id="menuName" name="menuName" placeholder="Add menu name" required>
                    </div>
                    <div class="form-group">
                        <label for="menuPrice">Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                id="menuPrice"
                                name="menuPrice"
                                placeholder="Add Harga"
                                required
                                oninput="formatPrice(this)"
                            >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="menuStatus">Status</label>
                        <select class="form-control" id="menuStatus" name="menuStatus" required>
                            <option value="ready">Ready</option>
                            <option value="empty">Empty</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="menuStatus">Category</label>
                        <select class="form-control" id="category" name="menuCategory" required>
                            <option value="main_course">Main Course</option>
                            <option value="drink">Non Coffe</option>
                            <option value="coffentea">Coffe & Tea</option>
                            <option value="milks">Milks & Smoothies</option>
                            <option value="snack">Snack</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="menuStock">Stock</label>
                        <input type="number" class="form-control" id="menuStock" name="menuStock" placeholder="Add stock menu" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="note">Add note</label>
                        <input type="text" class="form-control" id="menuNote" name="menuNote" placeholder="Add note" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    </div>
  </div>
</div>

  <!-- Update Menu Modal -->
  <div class="modal fade" id="updateMenuModal" tabindex="-1" role="dialog" aria-labelledby="updateMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="updateMenuForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- ID Menu (Hidden) -->
                    <input type="hidden" id="updateMenuId" name="menuId">
                    
                    <div class="form-group">
                        <label for="updateMenuImage">Upload Gambar</label>
                        <input type="file" class="form-control" id="updateMenuImage" name="menuImage">
                        <small class="form-text text-muted">
                            If no image is uploaded, the previous one will be retained. 
                            <span style="color: red;">*</span>
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateMenuName">Menu Name</label>
                        <input type="text" class="form-control" id="updateMenuName" name="menuName" placeholder="Update menu name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateMenuPrice">Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                id="updateMenuPrice"
                                name="menuPrice"
                                placeholder="Update harga"
                                required
                                oninput="formatPrice(this)"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateMenuStatus">Status</label>
                        <select class="form-control" id="updateMenuStatus" name="menuStatus" required>
                            <option value="ready">Ready</option>
                            <option value="empty">Empty</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateMenuCategory">Category</label>
                        <select class="form-control" id="updateMenuCategory" name="menuCategory" required>
                        <option value="main_course">Main Course</option>
                            <option value="drink">Non Coffe</option>
                            <option value="coffentea">Coffe & Tea</option>
                            <option value="milks">Milks & Smoothies</option>
                            <option value="snack">Snack</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateMenuStock">Stock</label>
                        <input type="number" class="form-control" id="updateMenuStock" name="menuStock" placeholder="Update stock menu" required min="0">
                    </div>

                    <div class="form-group">
                        <label for="updateMenuNote">Update Note</label>
                        <input type="text" class="form-control" id="updateMenuNote" name="menuNote" placeholder="Update note">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- View Menu Modal -->
<div class="modal fade" id="viewMenuModal" tabindex="-1" role="dialog" aria-labelledby="viewMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMenuModalLabel">Menu Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Gambar -->
                <div class="form-group">
                    <label for="viewMenuImage">Image</label>
                    <div>
                        <img id="viewMenuImage" src="" alt="Menu Image" style="width: 100%; max-width: 300px;">
                    </div>
                </div>
                <!-- Nama Menu -->
                <div class="form-group">
                    <label for="viewMenuName">Menu Name</label>
                    <input type="text" class="form-control" id="viewMenuName" readonly>
                </div>
                <!-- Harga -->
                <div class="form-group">
                    <label for="viewMenuPrice">Price</label>
                    <input type="text" class="form-control" id="viewMenuPrice" readonly>
                </div>
                <!-- Status -->
                <div class="form-group">
                    <label for="viewMenuStatus">Status</label>
                    <input type="text" class="form-control" id="viewMenuStatus" readonly>
                </div>
                <!-- Kategori -->
                <div class="form-group">
                    <label for="viewMenuCategory">Category</label>
                    <input type="text" class="form-control" id="viewMenuCategory" readonly>
                </div>
                <!-- Stok -->
                <div class="form-group">
                    <label for="viewMenuStock">Stock</label>
                    <input type="text" class="form-control" id="viewMenuStock" readonly>
                </div>
                <!-- Catatan -->
                <div class="form-group">
                    <label for="viewMenuNote">Note</label>
                    <textarea class="form-control" id="viewMenuNote" rows="3" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function () {
        var searchTerm = this.value.toLowerCase();
        var tableRows = document.querySelectorAll('#menuTable tbody tr');
        
        tableRows.forEach(function (row) {
            var name = row.cells[2].textContent.toLowerCase();
            var price = row.cells[3].textContent.toLowerCase();
            var status = row.cells[4].textContent.toLowerCase();
            var stock = row.cells[5].textContent.toLowerCase();
            var category = row.cells[6].textContent.toLowerCase();
            
            if (name.includes(searchTerm) || price.includes(searchTerm) || stock.includes(searchTerm) || category.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
<script>

function deleteMenu(id_masakan) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna menekan tombol "Yes, delete it!"
            window.location.href = "delete_menu.php?id_masakan=" + id_masakan;
        }
    });
}

</script>
<script> 
function fillUpdateForm(id,image,name, price, status, category, stock, note, ) {
  
    document.getElementById('updateMenuId').value = id;
    document.getElementById('updateMenuName').value = name;
    document.getElementById('updateMenuPrice').value = price;
    document.getElementById('updateMenuStatus').value = status;
    document.getElementById('updateMenuCategory').value = category;
    document.getElementById('updateMenuStock').value = stock;
    document.getElementById('updateMenuNote').value = note;

    // Menghapus bagian untuk menampilkan gambar saat ini
    const imageField = document.getElementById('updateMenuImage');
    if (image && imageField) {
        // Hapus label atau elemen gambar yang ada sebelumnya
        const existingLabel = imageField.parentNode.querySelector('label');
        if (existingLabel) {
            existingLabel.remove();
        }
    }
}
</script>

<script>
  // Fill form with data (you can call this function when you open the modal)
  function fillUpdateForm(id, image, name, price, status, category, stock, note) {
    document.getElementById('updateMenuId').value = id;
    document.getElementById('updateMenuName').value = name;
    document.getElementById('updateMenuPrice').value = price;
    document.getElementById('updateMenuStatus').value = status;
    document.getElementById('updateMenuCategory').value = category;
    document.getElementById('updateMenuStock').value = stock;
    document.getElementById('updateMenuNote').value = note;
    // Optionally display the current image if you want
  }

  // Handle form submission using AJAX
  $('#updateMenuForm').submit(function(event) {
    event.preventDefault(); // Prevent default form submission

    var formData = new FormData(this); // Collect form data including files
    
    $.ajax({
      url: './update_menu.php', // PHP script to handle the form submission
      type: 'POST',
      data: formData,
      processData: false, // Don't process the files
      contentType: false, // Don't set content type
      success: function(response) {
    var result = JSON.parse(response); // Parse the JSON response

    // Display SweetAlert based on response
    if (result.status === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Update Successful',
            text: result.message,
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = 'daftar_menu_1.php'; // Ganti dengan URL yang sesuai jika ingin mengarah ke halaman lain
            // Atau jika Anda ingin me-refresh halaman:
            // window.location.reload();
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: result.message,
            confirmButtonText: 'OK'
        });
    }
}
    });
  });
</script>
<script>  
function viewMenuDetails(id, image, name, price, status, category, stock, note) {
    // Set data ke modal
    document.getElementById('viewMenuImage').src = 'uploads/' + image; // Gambar
    document.getElementById('viewMenuName').value = name; // Nama menu
    document.getElementById('viewMenuPrice').value = price; // Harga
    document.getElementById('viewMenuStatus').value = status; // Status
    document.getElementById('viewMenuCategory').value = category; // Kategori
    document.getElementById('viewMenuStock').value = stock; // Stok
    document.getElementById('viewMenuNote').value = note; // Catatan
}

</script>
<script>
    function formatPrice(input) {
        // Menghapus semua karakter selain angka
        let value = input.value.replace(/[^0-9]/g, '');

        // Format angka dengan pemisah ribuan menggunakan titik (.)
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

  </body>
</html>
