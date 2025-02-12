<?php
session_start();

function cekLogin() {
    if (!isset($_SESSION['id_user'])) {
        echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='../login.php';</script>";
        exit();
    }
}
?>
