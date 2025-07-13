<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit;
}

$employee_id = $_SESSION['id'];

if (isset($_POST['order'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id"));
    $price = $product['price'];
    $total = $price * $quantity;

    mysqli_query($conn, "INSERT INTO orders (customer_id, total_price, status) VALUES ('$employee_id','$total','pending')");
    $order_id = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO order_items (product_id, order_id, quantity, price) VALUES ('$product_id','$order_id','$quantity','$price')");

    header("Location: orders_employee.php");
}

$products = mysqli_query($conn, "SELECT * FROM products");
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id=$employee_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif -->
    <title>Pesan Produk - Koperasi Sawit</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-green-50 min-h-screen flex flex-col font-sans">

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-green-700 py-4 px-6 flex justify-between items-center text-white shadow-md">
        <h1 class="text-2xl font-bold tracking-wide">🛒 Pesan Produk</h1>
        <a href="dashboard_employee.php" class="bg-gray-600 hover:bg-gray-700 transition px-4 py-2 rounded-full text-sm font-semibold shadow">🏠 Dashboard</a>
    </header>

    <main class="flex-grow p-6 max-w-7xl mx-auto space-y-16">

        <!-- Produk -->
        <section>
            <h2 class="text-3xl font-bold text-green-700 mb-8 text-center">Pilih & Pesan Produk</h2>
            <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                <?php while($p=mysqli_fetch_assoc($products)): ?>
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 relative overflow-hidden group">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-48 object-cover rounded-t-2xl group-hover:scale-105 transition duration-300">
                    <div class="absolute top-2 right-2 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Rp <?= number_format($p['price']) ?></div>
                    <div class="p-4 space-y-2">
                        <h3 class="text-lg font-bold text-green-700"><?= htmlspecialchars($p['name']) ?></h3>
                        <p class="text-gray-600 text-sm"><?= htmlspecialchars(mb_strimwidth($p['description'],0,50,"...")) ?></p>
                        <form method="POST" class="flex items-center space-x-2 mt-3">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" class="border rounded p-1 w-16 text-sm focus:ring-green-500 focus:border-green-500" required>
                            <button type="submit" name="order" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded font-semibold text-sm shadow">Pesan</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Pesanan -->
        <section class="bg-white p-8 rounded-2xl shadow-lg">
            <h2 class="text-3xl font-bold text-green-700 mb-6 text-center">📦 Pesanan Saya</h2>
            <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <?php $i=1; while($o=mysqli_fetch_assoc($orders)): ?>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 shadow-md hover:shadow-lg transition">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-green-700 font-bold">Pesanan #<?= $i++ ?></span>
                        <span class="text-xs font-bold px-2 py-1 rounded-full
                            <?= $o['status']=='pending' ? 'bg-yellow-400 text-yellow-900' : 
                                ($o['status']=='paid' ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900') ?>">
                            <?= htmlspecialchars(ucfirst($o['status'])) ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 mb-1">Total Harga: <span class="font-semibold">Rp <?= number_format($o['total_price']) ?></span></p>
                    <p class="text-xs text-gray-500">Tanggal: <?= htmlspecialchars($o['created_at']) ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-4 px-6 flex flex-col md:flex-row justify-between items-center">
            <p class="text-sm text-gray-600">
                © <?= date('Y') ?> Koperasi Sawit. All rights reserved.
            </p>
            <p class="text-sm text-gray-600 mt-2 md:mt-0">
                Developed by Tri Naldi Syaputra
            </p>
        </div>
    </footer>

</body>
</html>
