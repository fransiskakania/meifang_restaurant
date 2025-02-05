<?php
session_start();
include 'koneksi.php'; // Include your database connection file

// Query untuk mengambil nama_lengkap berdasarkan id_user
$id_user = $_SESSION['id_user'] ?? null; // Pastikan id_user berasal dari sesi atau sumber valid
if ($id_user) {
    $sql = "SELECT nama_lengkap,username FROM user WHERE id_user = '$id_user'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_lengkap = $row['nama_lengkap'];
        $username = $row['username'];

    } else {
        $nama_lengkap = "Guest";
        $username = "Not avalaible";

    }
} else {
    $nama_lengkap = "Guest";
    $username = "Not avalaible";

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cart - Meifang Restaurant authentic Chinese dishes</title>
    <link rel="icon" href="../meifang_resto/images/meifang_resto_logo/2.svg">
   
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <!-- Tambahkan SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>

<div class="container mt-5">
    <div class="row">
        <!-- Bagian Tabel Cart -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Your Cart</h2>
                <a href="index.php#menu" class="btn btn-danger">Add Menu</a>
            </div>
            
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                </tbody>
            </table>

            <!-- Button container with d-flex for centering -->
        
        </div>

        <!-- Bagian Order Summary -->
        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h4 class="fw-bold">Order Summary</h4>
                <hr>

                <div class="d-flex justify-content-between">
                    <span>Items</span>
                    <span id="total-items">0</span>
                </div>

                <div class="mt-3">
                    <label for="promo-code" class="form-label">Promo Code</label>
                    <input type="text" id="promo-code" class="form-control" placeholder="Enter your code">
                    <button class="btn btn-danger w-100 mt-2" onclick="applyPromo()">Apply</button>
                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold">
                    <span>Total Cost</span>
                    <span id="total-cost">Rp. 0</span>
                </div>

                <button class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#confirmOrderModal">Confirm Order</button>
                <!-- Modal Confirm Order -->
                <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmOrderLabel">Confirm Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="confirm_order.php">
                                <div class="modal-body">
                                <input type="hidden" id="cart_data" name="cart_data">

                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user_role" class="form-label">User Role</label>
                                        <input type="text" class="form-control" id="user_role" name="user_role" value="<?php echo htmlspecialchars($nama_lengkap); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="no_meja" class="form-label">No Meja</label>
                                        <input type="number" class="form-control" id="no_meja" name="no_meja" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="type_order" class="form-label">Type Order</label>
                                        <select class="form-select" id="type_order" name="type_order">
                                            <option value="Dine In">Dine In</option>
                                            <option value="Dine Out">Dine Out</option>
                                        </select>
                                        <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Confirm</button>
                                </div>
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Fungsi untuk memuat isi keranjang dari localStorage
function loadCart() { 
    let cartData = localStorage.getItem("cart");
    console.log("Data cart dari localStorage:", cartData); // Debugging

    let cart = JSON.parse(cartData) || [];
    console.log("Parsed cart:", cart); // Debugging

    let cartBody = document.getElementById("cart-body");
    let totalItems = 0;
    let totalPrice = 0;

    cartBody.innerHTML = "";

    if (cart.length === 0) {
        cartBody.innerHTML = `<tr><td colspan="6" class="text-center">Keranjang Kosong</td></tr>`;
        document.getElementById("total-items").innerText = "0";
        document.getElementById("total-cost").innerText = "Rp 0";
        return;
    }

    cart.forEach((item, index) => {
        let itemTotal = item.harga * item.qty;
        totalItems += item.qty;
        totalPrice += itemTotal;

        cartBody.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.namaMasakan}</td>
                <td>Rp ${item.harga.toLocaleString("id-ID")}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick="changeCartQuantity(${item.id}, -1)">-</button>
                    <span id="cart-qty-${item.id}" class="mx-2">${item.qty}</span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="changeCartQuantity(${item.id}, 1)">+</button>
                </td>
                <td>Rp ${itemTotal.toLocaleString("id-ID")}</td>
                <td>
                    <button class="btn btn-outline-danger" onclick="removeFromCart(${item.id})">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    // Update Order Summary
    document.getElementById("total-items").innerText = totalItems;
    document.getElementById("total-cost").innerText = "Rp " + totalPrice.toLocaleString("id-ID");

    // Simpan data keranjang ke dalam form sebelum submit
    document.getElementById("cart_data").value = JSON.stringify(cart);
}


    function changeCartQuantity(id, amount) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        let item = cart.find(item => item.id === id);

        if (!item) return;

        let newQty = item.qty + amount;

        if (newQty < 1) {
            removeFromCart(id);
            return;
        }

        if (newQty > item.stock) {
            alert("Stok tidak mencukupi!");
            return;
        }

        item.qty = newQty;
        localStorage.setItem("cart", JSON.stringify(cart));
        loadCart();
    }

    function removeFromCart(id) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart = cart.filter(item => item.id !== id);
        localStorage.setItem("cart", JSON.stringify(cart));
        loadCart();
    }

    function clearCart() {
        localStorage.removeItem("cart");
        loadCart();
    }

    document.addEventListener("DOMContentLoaded", loadCart);

    function updateOrderSummary() {
    let totalItems = 0;
    let totalPrice = 0;

    document.querySelectorAll("#cart-body tr").forEach(row => {
        let quantity = parseInt(row.querySelector(".quantity-input").value);
        let price = parseFloat(row.querySelector(".price-value").innerText.replace("Rp. ", "").replace(",", ""));
        
        totalItems += quantity;
        totalPrice += price * quantity;
    });

    document.getElementById("total-items").innerText = totalItems;
    document.getElementById("total-cost").innerText = "Rp. " + (totalPrice + 5000).toLocaleString();
}

function applyPromo() {
        let promoCode = document.getElementById("promo-code").value.trim();
        
        if (promoCode === "") {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Please enter a promo code!",
                confirmButtonColor: "#3085d6" // Tombol biru
            });
        } else {
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Promo applied: " + promoCode,
                confirmButtonColor: "#3085d6" // Tombol biru
            });
        }
    }

// Panggil fungsi ini setiap kali cart diperbarui
updateOrderSummary();


</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
