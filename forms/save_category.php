<?php
include 'koneksi.php'; // Pastikan file ini berisi koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = trim($_POST["category"]);

    if (!empty($category)) {
        $stmt = $conn->prepare("INSERT INTO masakan (category) VALUES (?)");
        $stmt->bind_param("s", $category);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error";
        }

        $stmt->close();
    }
}

$conn->close();
?>
