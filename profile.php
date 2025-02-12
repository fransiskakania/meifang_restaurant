<?php
session_start();

// Koneksi ke database
include 'koneksi.php';

// Pengecekan koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek apakah session id_user tersedia
$id_user = $_SESSION['id_user'] ?? null;
if ($id_user) {
    // Hindari SQL Injection dengan prepared statement
    $stmt = $conn->prepare("SELECT nama_lengkap, username, tipe_user FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id_user); // 'i' untuk tipe integer
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult && $userResult->num_rows > 0) {
        $row = $userResult->fetch_assoc();
        $nama_lengkap = $row['nama_lengkap'];
        $username = $row['username'];
        $role = $row['tipe_user']; // Ambil tipe_user sebagai role
    } else {
        $nama_lengkap = "Guest";
        $username = "Not available";
        $role = "Unknown"; // Jika tipe_user tidak ditemukan
    }

    $stmt->close();
} else {
    $nama_lengkap = "Guest";
    $username = "Not available";
    $role = "Unknown";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meifang Resto - Profile Page</title>
  <link rel="icon" href="assets/img/meifang_resto_logo/2.svg" type="image/x-icon"/>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <style>
    .profile-card {
      max-width: 500px;
      margin: 50px auto;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-card img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-top: -60px;
    }

    .profile-card .card-header {
      background:rgb(104, 144, 179);
      color: #fff;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      text-align: center;
      padding: 30px 20px 0;
    }

    .profile-card .card-body {
      text-align: center;
      padding: 20px;
    }

    .profile-card .btn-edit {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="profile-card card">
      <div class="card-header">
        <img
          src="./assets/img/profile/jane.png"
          alt="Profile Picture"
          id="profile-pic"
        />
        <h4 id="profile-name"><?php echo htmlspecialchars($nama_lengkap); ?></h4>
        <p> <?php echo htmlspecialchars(string: $username); ?></p>
      </div>
      <div class="card-body">
        <h5>Personal Info</h5>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($nama_lengkap); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars(string: $username); ?></p>
        <p><strong>Role:</strong> <span id="role"><?php echo htmlspecialchars($role); ?></span></p>
       
      </div>
    </div>
  </div>

  <!-- Edit Profile Modal -->


  <!-- Bootstrap and JavaScript -->
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"
  ></script>
  <script>
    function editProfile() {
      // Populate modal fields with current profile data
      document.getElementById('editFullName').value = document.getElementById(
        'full-name'
      ).innerText;
      document.getElementById('editEmail').value = document.getElementById(
        'email'
      ).innerText;
      document.getElementById('editRole').value = document.getElementById(
        'role'
      ).innerText;

      // Show the modal
      const editProfileModal = new bootstrap.Modal(
        document.getElementById('editProfileModal')
      );
      editProfileModal.show();
    }

    // Save changes made in the modal
    document.getElementById('editProfileForm').addEventListener('submit', function (e) {
      e.preventDefault();
      // Update profile card with new values
      document.getElementById('full-name').innerText = document.getElementById(
        'editFullName'
      ).value;
      document.getElementById('email').innerText = document.getElementById(
        'editEmail'
      ).value;
      document.getElementById('role').innerText = document.getElementById(
        'editRole'
      ).value;

      // Close the modal
      const editProfileModal = bootstrap.Modal.getInstance(
        document.getElementById('editProfileModal')
      );
      editProfileModal.hide();
    });
  </script>
</body>
</html>
