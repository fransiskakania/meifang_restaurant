<?php
include 'koneksi.php';

// Set header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transactions_" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch data dari database
$sql = "SELECT date, total_payment FROM transaksi";
$result = $conn->query($sql);

// Tampilkan data dalam format tabel HTML
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Date</th>
        <th>Total Payment</th>
      </tr>";

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . date("d/m/Y", strtotime($row['date'])) . "</td>";
        echo "<td>" . number_format($row['total_payment'], 3) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No transactions found</td></tr>";
}
echo "</table>";
?>
