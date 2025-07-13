<?php
session_start();
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username='$username'"));

$total_request = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM requests WHERE employee_id={$user['id']}"))['total'];
$total_lunas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM trx_payments WHERE employee_id={$user['id']} AND status='accepted'"))['total'];
$total_belum_lunas = $total_request - $total_lunas;

$data = mysqli_query($conn, "SELECT status, COUNT(*) as jumlah FROM trx_payments WHERE employee_id={$user['id']} GROUP BY status");
$labels = [];
$values = [];
while ($row = mysqli_fetch_assoc($data)) {
    $labels[] = ucfirst($row['status']);
    $values[] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif -->
    <title>Dashboard Employee - Koperasi Sawit</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { scroll-behavior: smooth; }
    </style>
    <script>
        tailwind.config = { darkMode: 'class' };
         if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

</script>

    </script>
</head>
<body class="bg-green-50 dark:bg-gray-900 min-h-screen flex flex-col font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300">

<!-- Tombol Toggle Sidebar -->
<button onclick="toggleSidebar()" class="fixed top-4 left-4 z-50 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg focus:outline-none">
    ☰
</button>

<!-- Sidebar -->
<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full transition-transform duration-300 z-40 rounded-r-2xl flex flex-col">
    <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-blue-700 dark:text-white">📘 Menu Karyawan</h2>
    </div>
    <nav class="flex flex-col gap-4 p-4 text-gray-700 dark:text-gray-200 flex-grow">
        <a href="requests_employee.php" class="flex items-center gap-2 hover:text-blue-600 dark:hover:text-yellow-300 transition">
            📄 <span>Ajukan Request</span>
        </a>
        <a href="orders_employee.php" class="flex items-center gap-2 hover:text-blue-600 dark:hover:text-yellow-300 transition">
            🛒 <span>Pesan Produk</span>
        </a>
        <a href="payments_employee.php" class="flex items-center gap-2 hover:text-blue-600 dark:hover:text-yellow-300 transition">
            💸 <span>Upload Pembayaran</span>
        </a>
    </nav>
    <div class="p-4 border-t">
        <a href="logout.php" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-xl text-center flex items-center justify-center gap-2">
            🔒 Logout
        </a>
    </div>
</div>

<!-- Main Content -->
<div id="mainContent" class="transition-all duration-300 ml-0">

    <!-- Header -->
    <header class="relative flex justify-between items-center h-56 px-6 md:px-12 text-white rounded-b-3xl shadow-lg overflow-hidden bg-green-700 dark:bg-green-800">
        <img src="https://thewire.signingdaysports.com/wp-content/uploads/IMG-Academy-1.jpg" alt="Sawit" class="absolute w-full h-full object-cover brightness-50">
        <div class="relative z-10">
            <h1 class="text-4xl font-extrabold drop-shadow">Hai, <?= htmlspecialchars($user['name']) ?> 👋</h1>
            <p class="mt-1 text-lg md:text-xl font-medium drop-shadow">Selamat datang di Dashboard Karyawan</p>
        </div>
        <div class="relative z-10 flex items-center gap-2">
            <button onclick="toggleDarkMode()" class="bg-gray-800 hover:bg-gray-700 dark:bg-yellow-400 dark:hover:bg-yellow-300 transition text-white dark:text-gray-800 px-3 py-2 rounded-full text-sm shadow">
                🌙 / ☀️
            </button>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 transition px-5 py-2 rounded-full text-sm font-semibold shadow">🚪 Logout</a>
        </div>
    </header>

    <main class="flex-grow p-6 max-w-6xl mx-auto space-y-12">

        <!-- Motivasi -->
        <section class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow hover:shadow-xl transition text-center">
            <h2 class="text-2xl font-bold text-green-700 dark:text-green-400 mb-4">Semangat Terus, <?= htmlspecialchars($user['name']) ?>!</h2>
            <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Bekerjalah dengan hati, majukan Koperasi Sawit, dan wujudkan kesejahteraan bersama!
            </p>
        </section>

        <!-- Statistik -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg text-center hover:scale-105 transition">
                <p class="uppercase text-xs mb-1 tracking-widest">Total Request</p>
                <p class="text-4xl font-bold"><?= $total_request ?></p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-lg text-center hover:scale-105 transition">
                <p class="uppercase text-xs mb-1 tracking-widest">Request Lunas</p>
                <p class="text-4xl font-bold"><?= $total_lunas ?></p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-6 rounded-2xl shadow-lg text-center hover:scale-105 transition">
                <p class="uppercase text-xs mb-1 tracking-widest">Belum Lunas</p>
                <p class="text-4xl font-bold"><?= $total_belum_lunas ?></p>
            </div>
        </section>

        <!-- Grafik Pembayaran -->
        <section class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow hover:shadow-xl transition">
            <h2 class="text-xl font-bold text-green-700 dark:text-green-400 mb-6 text-center">Status Pembayaran Kamu</h2>
            <div class="flex justify-center">
                <div class="w-full max-w-xs sm:max-w-sm md:max-w-md aspect-w-1 aspect-h-1">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Navigasi Fitur -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center">
            <a href="requests_employee.php" class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-2xl shadow-lg text-center font-semibold w-full max-w-xs transition">Ajukan Request</a>
            <a href="orders_employee.php" class="bg-yellow-500 hover:bg-yellow-600 text-white p-6 rounded-2xl shadow-lg text-center font-semibold w-full max-w-xs transition">Pesan Produk</a>
            <a href="payments_employee.php" class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-2xl shadow-lg text-center font-semibold w-full max-w-xs transition">Upload Pembayaran</a>
        </section>
    </main>

    <script>
        const ctx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($values) ?>,
                    backgroundColor: [
                        'rgba(34,197,94,0.6)',
                        'rgba(239,68,68,0.6)',
                        'rgba(253,224,71,0.6)'
                    ],
                    borderColor: [
                        'rgba(34,197,94,1)',
                        'rgba(239,68,68,1)',
                        'rgba(253,224,71,1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' }},
                maintainAspectRatio: false,
                responsive: true
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            sidebar.classList.toggle("-translate-x-full");
            mainContent.classList.toggle("ml-64");
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        }

        // Load mode dari localStorage saat awal
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
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

</div>
</body>
</html>
