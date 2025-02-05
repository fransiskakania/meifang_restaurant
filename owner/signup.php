<?php
// Include the database connection file
include 'koneksi.php';

$alert_message = '';
$alert_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['nama_lengkap'];
    $user_type = $_POST['tipe_user'];

    // Map user_type to id_level
    $role_mapping = [
        'Administrator' => 1,
        'Customer' => 2,
        'Waiter' => 3,
        'Owner' => 4,
        'Cashier' => 5,
    ];

    // Validate user_type
    if (!array_key_exists($user_type, $role_mapping)) {
        $alert_message = "Invalid user type!";
        $alert_type = 'error';
    } elseif ($user_type == 'Owner' || $user_type == 'Customer') {
        $alert_message = "Registration is not allowed for Owner or Customer roles!";
        $alert_type = 'error';
    } else {
        $id_level = $role_mapping[$user_type];

        // Validate email, password, etc.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $alert_message = "Invalid email format!";
            $alert_type = 'error';
        } elseif ($password !== $confirm_password) {
            $alert_message = "Password and confirm password do not match!";
            $alert_type = 'error';
        } elseif (strlen($password) < 8) {
            $alert_message = "Password must be at least 8 characters!";
            $alert_type = 'error';
        } else {
            // Check if the email already exists in the database
            $check_query = $conn->prepare("SELECT * FROM user WHERE username = ?");
            $check_query->bind_param("s", $email);
            $check_query->execute();
            $check_result = $check_query->get_result();

            if ($check_result->num_rows > 0) {
                // Email already exists
                $alert_message = "Email is already registered!";
                $alert_type = 'error';
            } else {
                // Encrypt the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Query to insert into the database
                $stmt = $conn->prepare("INSERT INTO user (username, password, nama_lengkap, tipe_user, id_level) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $email, $hashed_password, $full_name, $user_type, $id_level);

                if ($stmt->execute()) {
                    $alert_message = "Registration successful!";
                    $alert_type = 'success';

                    // Redirect to login page after successful signup
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>";
                } else {
                    $alert_message = "Error: " . $stmt->error;
                    $alert_type = 'error';
                }
                $stmt->close();
            }

            $check_query->close();
        }
    }

    $conn->close();

    // Output the SweetAlert JavaScript
    echo "<script>
        Swal.fire({
            icon: '{$alert_type}',
            title: '{$alert_message}',
            showConfirmButton: true
        });
    </script>";
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Meifang Resto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/signup.css">
    <!-- <link rel="stylesheet" href="./assets/css/kaiadmin.css"> -->
    <link rel="icon" href="assets/img/meifang_resto_logo/2.svg" type="image/x-icon"/>
    <style>
/* Efek Hover dan Styling Tambahan */

/* Hover untuk Input dan Select */
.signup-form input,
.signup-form select {
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.signup-form input:hover,
.signup-form select:hover {
    border-color: #4facfe;
    box-shadow: 0 0 5px rgba(79, 172, 254, 0.5);
}

.signup-form input:focus,
.signup-form select:focus {
    border-color: #0056b3;
    box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
    outline: none;
}

/* Hover untuk Tombol */


/* Hover untuk Tautan */
.signup-form a {
    text-decoration: none;
    color: #4facfe;
    font-weight: bold;
    transition: color 0.3s ease, text-decoration 0.3s;
}

.signup-form a:hover {
    text-decoration: underline;
    color: #0056b3;
}

/* Hover untuk Ikon Mata */
.signup-form .fas {
    color: #999;
    transition: color 0.3s ease;
}

.signup-form .fas:hover {
    color: #4facfe;
}

/* Responsivitas */
@media (max-width: 480px) {
    .signup-form input,
    .signup-form select {
        font-size: 0.9rem;
    }

    .signup-form button {
        font-size: 0.9rem;
    }
}
</style>
</head>
<body>
    
<div class="containers">
    <img src="./assets/img/meifang_resto_logo/1.svg" class="logos" alt="Meifang Resto Logo">
    <div class="form-containers">
        <form method="post" class="signup-form">
            <h1><strong>Signup For Free</strong></h1>
            <p>Create a new account by entering your details</p>

            <!-- Email Input -->
            <input type="email" name="email" placeholder="Email" required>

            <!-- Password Input with Toggle -->
            <div style="position: relative;">
                <input type="password" name="password" id="password" placeholder="Create password" required style="padding-right: 30px;">
                <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); cursor: pointer;"></i>
            </div>

            <!-- Confirm Password Input with Toggle -->
            <div style="position: relative;">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required style="padding-right: 30px;">
                <i class="fas fa-eye" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); cursor: pointer;"></i>
            </div>

            <!-- Select User Type -->
            <select name="tipe_user" id="tipe_user" required>
                <option value="Administrator">Administrator</option>
                <option value="Waiter">Waiter</option>
                <option value="Kasir">Kasir</option>
            </select>

            <!-- Full Name Input -->
            <input type="text" name="nama_lengkap" placeholder="Full Name" required>

            <!-- Submit Button -->
            <button type="submit">Signup</button>
            <p>Already have an account? <a href="./login.php">Login</a></p>
        </form>
    </div>
</div>

        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
        });

        // Toggle confirm password visibility
        const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
        const confirmPassword = document.querySelector("#confirm_password");

        toggleConfirmPassword.addEventListener("click", function () {
            const type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
            confirmPassword.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
        });
    </script>
<!-- SweetAlert Notification -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (!empty($alert_message)) { ?>
        Swal.fire({
            icon: '<?php echo $alert_type; ?>', // success, error, warning
            title: '<?php echo $alert_message; ?>',
            showConfirmButton: true,
            timer: <?php echo $alert_type === 'success' ? 2000 : 3000; ?> // Faster redirect for success
        });
    <?php } ?>
</script>
</body>
</html>
