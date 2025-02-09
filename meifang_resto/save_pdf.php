<?php
include 'koneksi.php';
require_once('TCPDF-main/tcpdf.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_order'])) {
    $id_order = $_POST['id_order'];

    $sql = "SELECT nama_masakan, quantity, price FROM transaksi WHERE id_order = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();

    $menuList = [];
    $totalPrice = 0;

    while ($row = $result->fetch_assoc()) {
        $menuList[] = [
            'nama_masakan' => $row['nama_masakan'],
            'quantity' => $row['quantity'],
            'price' => $row['price'] * $row['quantity']
        ];
        $totalPrice += $row['price'] * $row['quantity'];
    }

    $stmt->close();
    $conn->close();

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Detail Orders', 0, 1, 'C');

    $pdf->Cell(60, 10, 'Menu Name', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Quantity', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C');

    foreach ($menuList as $menu) {
        $pdf->Cell(60, 10, $menu['nama_masakan'], 1, 0, 'C');
        $pdf->Cell(40, 10, $menu['quantity'], 1, 0, 'C');
        $pdf->Cell(40, 10, 'Rp ' . number_format($menu['price'], 3, ',', '.'), 1, 1, 'C');
    }

    $pdf->Cell(100, 10, 'Total Pembayaran', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Rp ' . number_format($totalPrice, 3, ',', '.'), 1, 1, 'C');

    $pdf->Output('order_details.pdf', 'D'); // Force download
} else {
    die("Invalid request.");
}
?>
