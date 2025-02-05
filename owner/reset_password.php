<?php
if (isset($_GET['token'])) {
    $token = $_GET['token'];
?>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label for="password">Password Baru:</label>
        <input type="password" name="password" id="password" required>
        <label for="confirm_password">Konfirmasi Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <button type="submit">Reset Password</button>
    </form>
<?php
} else {
    echo "Token tidak valid.";
}
?>
