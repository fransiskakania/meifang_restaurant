<?php
session_start();
include 'koneksi.php'; // Include connection file

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

        // Verify password using password_verify
        if (password_verify($password, $row['password'])) {
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['tipe_user'] = $row['tipe_user'];
            $_SESSION['id_level'] = $row['id_level'];
        
            // Redirect based on user level
            if ($row['id_level'] == 2) { // Customer
                $_SESSION['login_success'] = "Login berhasil, selamat datang!";
                header("Location: ./meifang_resto/index.html");
                exit();
            } elseif ($row['id_level'] == 4) { // Owner
                $_SESSION['login_success'] = "Login berhasil, selamat datang Owner!";
                header("Location: /meifang_resto_admin/owner/index.php");
                exit();
            } else { // Other user roles (Administrator, Waiter, etc.)
                $_SESSION['login_success'] = "Login berhasil!";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "Username atau password salah!";
            header("Location: ./login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Username tidak ditemukan!";
        header("Location: ./login.php");
        exit();
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
    // Fungsi untuk mendapatkan parameter URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Ambil status dan redirect dari URL
    const status = getQueryParam('status');
    const redirect = getQueryParam('redirect');

    if (status === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: redirect === 'customer'
                ? 'Redirecting to Customer Dashboard...'
                : 'Redirecting to Admin Dashboard...',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            if (redirect === 'customer') {
                window.location.href = './meifang_resto/index.html';
            } else {
                window.location.href = 'index.php';
            }
        });
    } else if (status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: 'Invalid username or password.',
            timer: 2000,
            showConfirmButton: false
        });
    }
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
