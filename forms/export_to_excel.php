<?php
include 'koneksi.php';

// Set header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transactions_" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch data dari database
$sql = "SELECT id_order, date, user_role, payment_with, total_payment, status_order FROM transaksi";
$result = $conn->query($sql);

// Tampilkan data dalam format tabel HTML
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>ID Order</th>
        <th>Date</th>
        <th>User Role</th>
        <th>Payment With</th>
        <th>Total Payment</th>
        <th>Status Order</th>
      </tr>";

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['id_order']) . "</td>";
        echo "<td>" . date("d/m/Y", strtotime($row['date'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['user_role']) . "</td>";
        echo "<td>" . htmlspecialchars($row['payment_with']) . "</td>";
        echo "<td>" . number_format($row['total_payment'], 3) . "</td>";
        echo "<td>" . ucfirst(htmlspecialchars($row['status_order'])) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No transactions found</td></tr>";
}
echo "</table>";
?>
