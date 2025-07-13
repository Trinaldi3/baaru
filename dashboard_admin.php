<?php
session_start();
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Statistik
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$total_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM requests"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
$total_payments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM trx_payments"))['total'];

// Grafik: request per tipe
$data = mysqli_query($conn, "SELECT type, COUNT(*) as jumlah FROM requests GROUP BY type");
$labels = [];
$values = [];
while ($row = mysqli_fetch_assoc($data)) {
    $labels[] = ucfirst($row['type']);
    $values[] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard Admin - Koperasi Sawit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
</head>
<body class="bg-green-50 dark:bg-gray-900 font-sans min-h-screen text-gray-900 dark:text-gray-100 transition-colors duration-300">

<!-- Tombol Toggle Sidebar -->
<button onclick="toggleSidebar()" class="fixed top-4 left-4 z-50 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg focus:outline-none">
    ☰
</button>

<!-- Sidebar -->
<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 z-40 rounded-r-2xl flex flex-col">
    <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-blue-700">📘 Menu Admin</h2>
    </div>
    <nav class="flex flex-col gap-4 p-4 text-gray-700 flex-grow">
        <a href="users.php" class="flex items-center gap-2 hover:text-blue-600 transition">
            👥 <span>Manajemen User</span>
        </a>
        <a href="products.php" class="flex items-center gap-2 hover:text-blue-600 transition">
            🔐 <span>Produk</span>
        </a>
        <a href="orders.php" class="flex items-center gap-2 hover:text-blue-600 transition">
            📦 <span>Order</span>
        </a>
        <a href="requests.php" class="flex items-center gap-2 hover:text-blue-600 transition">
            📄 <span>Permintaan</span>
        </a>
        <a href="#" class="flex items-center gap-2 hover:text-blue-600 transition">
            💰 <span>Penggajian</span>
        </a>
        <a href="#" class="flex items-center gap-2 hover:text-blue-600 transition">
            ⚙️ <span>Pengaturan</span>
        </a>
    </nav>
    <div class="p-4 border-t">
        <a href="logout.php" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-xl text-center flex items-center justify-center gap-2">
            🔒 Logout
        </a>
    </div>
</div>

<div id="mainContent" class="transition-all duration-300 ml-0">

<!-- Header -->
<header class="bg-green-700 dark:bg-green-800 relative h-48 md:h-56 flex flex-col md:flex-row justify-between items-center px-6 md:px-12 text-white rounded-b-3xl shadow-lg overflow-hidden text-center md:text-left">
    <img src="img/foto1.jpg" alt="Sawit" class="absolute w-full h-full object-cover opacity-30">
    <div class="relative z-10 mb-4 md:mb-0">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold drop-shadow">Dashboard Admin</h1>
        <p class="text-base sm:text-lg md:text-xl mt-1">Manajemen Koperasi Sawit</p>
    </div>
    <div class="relative z-10 flex flex-wrap justify-center md:justify-end items-center gap-2">
        <button onclick="toggleDarkMode()" class="bg-gray-800 hover:bg-gray-700 dark:bg-yellow-400 dark:hover:bg-yellow-300 transition text-white dark:text-gray-800 px-3 py-2 rounded-full text-sm shadow">
            🌙 / ☀️
        </button>
    </div>
</header>

<main class="flex-grow p-6 max-w-7xl mx-auto space-y-10">

    <!-- Tentang -->
    <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-md transition">
        <h2 class="text-2xl font-bold text-green-700 dark:text-green-400 mb-2">Tentang Koperasi Sawit</h2>
        <p class="text-gray-700 dark:text-gray-300">
            Koperasi Sawit mendukung petani dengan layanan keuangan & kebutuhan pertanian. Dashboard ini memberi admin kemampuan untuk memonitor semua aktivitas secara real-time dan efisien.
        </p>
    </section>

    <!-- Statistik -->
    <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-xl shadow text-center hover:scale-105 transition">
            <p class="text-sm">Users</p>
            <p class="text-2xl font-bold"><?= $total_users ?></p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-4 rounded-xl shadow text-center hover:scale-105 transition">
            <p class="text-sm">Products</p>
            <p class="text-2xl font-bold"><?= $total_products ?></p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-xl shadow text-center hover:scale-105 transition">
            <p class="text-sm">Requests</p>
            <p class="text-2xl font-bold"><?= $total_requests ?></p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-xl shadow text-center hover:scale-105 transition">
            <p class="text-sm">Orders</p>
            <p class="text-2xl font-bold"><?= $total_orders ?></p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-4 rounded-xl shadow text-center hover:scale-105 transition">
            <p class="text-sm">Payments</p>
            <p class="text-2xl font-bold"><?= $total_payments ?></p>
        </div>
    </section>

    <!-- Grafik Permintaan -->
    <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-md transition">
        <h2 class="text-xl font-bold text-green-700 dark:text-green-400 mb-4">Statistik Permintaan Berdasarkan Tipe</h2>
        <div class="max-w-lg mx-auto">
            <canvas id="requestChart" height="300"></canvas>
        </div>
    </section>

    <!-- Navigasi Fitur -->
    <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <a href="users.php" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-xl shadow text-center font-semibold transition">Kelola Users</a>
        <a href="products.php" class="bg-yellow-500 hover:bg-yellow-600 text-white p-4 rounded-xl shadow text-center font-semibold transition">Kelola Products</a>
        <a href="requests.php" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-xl shadow text-center font-semibold transition">Kelola Requests</a>
        <a href="orders.php" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-xl shadow text-center font-semibold transition">Kelola Orders</a>
        <a href="payments.php" class="bg-red-500 hover:bg-red-600 text-white p-4 rounded-xl shadow text-center font-semibold transition">Kelola Payments</a>
    </section>
</main>
</div>

<script>
    let sidebarVisible = false;
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("mainContent");
        sidebarVisible = !sidebarVisible;

        if (sidebarVisible) {
            sidebar.classList.remove("-translate-x-full");
            mainContent.classList.add("ml-64");
        } else {
            sidebar.classList.add("-translate-x-full");
            mainContent.classList.remove("ml-64");
        }
    }

    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
    }

    const ctx = document.getElementById('requestChart').getContext('2d');
    const requestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Jumlah Permintaan',
                data: <?= json_encode($values) ?>,
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 8
            }]
        },
        options: {
            plugins: { legend: { display: false }},
            scales: { y: { beginAtZero: true }},
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

<!-- Footer -->
<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto py-4 px-6 flex flex-col md:flex-row justify-between items-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            © <?= date('Y') ?> Koperasi Sawit. All rights reserved.
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 md:mt-0">
            Developed by Tri Naldi Syaputra
        </p>
    </div>
</footer>

</body>
</html>
