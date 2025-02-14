<?php
session_start();
include 'koneksi.php'; // Include connection file

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
                    window.location.href = '$redirectUrl';
                });
            });
        </script>";
    exit();
}

function checkPassword($inputPassword, $storedPassword) {
    return password_verify($inputPassword, $storedPassword);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Query to check user by username
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if password matches
        if (checkPassword($password, $row['password'])) {
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['tipe_user'] = $row['tipe_user'];
            $_SESSION['id_level'] = $row['id_level'];

            // Redirect based on user level
            switch ($row['id_level']) {
                case 2: // Customer
                    $_SESSION['login_success'] = "Login berhasil, selamat datang!";
                    header("Location: ./meifang_resto/index.php");
                    exit();
                case 4: // Owner
                    $_SESSION['login_success'] = "Login berhasil, selamat datang Owner!";
                    header("Location: ./owner/index.php");
                    exit();
                case 5: // Kasir
                    $_SESSION['login_success'] = "Login berhasil, selamat datang Kasir!";
                    header("Location: ./kasir/index.php");
                    exit();
                case 3: // Waiter
                    $_SESSION['login_success'] = "Login berhasil, selamat datang Waiter!";
                    header("Location: ./waiter/index.php");
                    exit();
                case 1: // Administrator
                    $_SESSION['login_success'] = "Login berhasil, selamat datang Administrator!";
                    header("Location: ./index.php");
                    exit();
                default:
                    // Handle unexpected roles
                    showErrorAndExit('Role is unknown or does not have appropriate access!', './login.php');
            }
        } else {
            // Password is incorrect
            showErrorAndExit('Password does not match the email entered!', './login.php');
        }
    } else {
        // Username not found
        showErrorAndExit('Username not found!', './login.php');
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meifang Resto</title>
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="icon" href="assets/img/meifang_resto_logo/2.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
/* Efek Hover dan Styling Tambahan */

/* Hover untuk Input dan Select */
.login-form input,
.login-form select {
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.login-form input:hover,
.login-form select:hover {
    border-color: #4facfe;
    box-shadow: 0 0 5px rgba(79, 172, 254, 0.5);
}

.login-form input:focus,
.login-form select:focus {
    border-color: #0056b3;
    box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
    outline: none;
}

/* Hover untuk Tombol */
.login-form button {
    transition: background-color 0.3s ease, color 0.3s ease;
}

.login-form button:hover {
    background-color: #4facfe;
    color: white;
}

/* Hover untuk Tautan */
.login-form a {
    text-decoration: none;
    color: #4facfe;
    font-weight: bold;
    transition: color 0.3s ease, text-decoration 0.3s;
}

.login-form a:hover {
    text-decoration: underline;
    color: #0056b3;
}

/* Hover untuk Ikon Mata */
.login-form .fa {
    color: #999;
    transition: color 0.3s ease;
}

.login-form .fa:hover {
    color: #4facfe;
}

/* Responsivitas */
@media (max-width: 480px) {
    .login-form input,
    .login-form select {
        font-size: 0.9rem;
    }

    .login-form button {
        font-size: 0.9rem;
    }
}
</style>

<body>
<div class="container">
        <img src="./assets/img/meifang_resto_logo/1.svg" class="logo" alt="Meifang Resto Logo">
        <div class="form-container">
            <form method="post" class="login-form">
                <h2>Login To Your Account</h2>
                <p>Enter your username & password to login</p>
                <input type="text" name="username" placeholder="Username" required>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" placeholder="Password" required style="width: 100%; padding-right: 10px;">
                    <i id="togglePassword" class="fa fa-eye" style="position: absolute; top: 30%; right: 10px; font-size: 18px; color: #555;"></i>
                </div>
                <select name="id_level" class="custom-select" required>
                    <option value="" disabled selected>Select User Role</option>
                    <option value="1">Administrator</option>
                    <option value="2">Customer</option>
                    <option value="3">Waiter</option>
                    <option value="4">Owner</option>
                    <option value="5">Kasir</option>
                </select>
                <button type="submit" class="btn">Login</button>
                <p class="signup-text">
                    <span style="float: center;">Don't have an account? <a href="./signup.php">Signup</a></span>
                </p>
                <p class="forgot-password">
                    <a href="./forgot_password.php" style="float: center; font-size: 14px; color: #555;">Forget Password?</a> <br>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
  
</script>


    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const passwordField = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            // Toggle tipe input password
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);

            // Ganti ikon eye
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>

</html>
