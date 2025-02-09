<?php
require_once('TCPDF-main/tcpdf.php');
include 'koneksi.php';

if (isset($_GET['id_order'])) {
    $id_order = $_GET['id_order'];

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

    // Buat objek PDF baru
    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Header - Nama Restoran
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Meifang Resto', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 5, 'Detail Orders', 0, 1, 'C');
    $pdf->Ln(5);

    // Gaya tabel dengan CSS inline
    $html = '
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color:rgb(182, 50, 50);
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border: 1px solid black;
        }
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
     
    </style>
    ';

    // Mulai tabel
    $html .= '<table>
                <thead>
                    <tr>
                        <th>Menu Name</th>
                        <th>Quantity</th>
                        <th>Subtotal (Rp)</th>
                    </tr>
                </thead>
                <tbody>';

    // Isi tabel
    foreach ($menuList as $menu) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($menu['nama_masakan']) . '</td>
                    <td>' . $menu['quantity'] . '</td>
                    <td>' . number_format($menu['price'], 3, ',', '.') . '</td>
                  </tr>';
    }

    // Baris total harga
    $html .= '</tbody>
              <tfoot>
                <tr class="total-row">
                    <td colspan="2">Total Pembayaran</td>
                    <td>' . number_format($totalPrice, 3, ',', '.') . '</td>
                </tr>
              </tfoot>
              </table>';

    // Tambahkan tabel ke PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Simpan file PDF dan kirimkan ke browser
    $pdf->Output('detail_order_' . $id_order . '.pdf', 'D');
    exit();
} else {
    echo "<script>alert('ID Order tidak ditemukan!'); window.location.href='order_menu.php';</script>";
    exit();
}
?>
