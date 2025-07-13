<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Update status pembayaran
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE trx_payments SET status='$status', admin_id='{$_SESSION['id']}' WHERE id=$id");
    header("Location: payments.php");
}

$payments = mysqli_query($conn, "SELECT trx_payments.*, u1.name AS employee, u2.name AS admin FROM trx_payments
    JOIN users u1 ON trx_payments.employee_id=u1.id
    LEFT JOIN users u2 ON trx_payments.admin_id=u2.id ORDER BY trx_payments.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>Kelola Payments - Koperasi Sawit</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100 min-h-screen font-sans">
    <div class="bg-green-700 p-4 text-white flex justify-between items-center">
        <h1 class="text-xl font-bold">Kelola Payments - Koperasi Sawit</h1>
        <a href="dashboard_admin.php" class="bg-gray-500 hover:bg-gray-600 px-3 py-1 rounded">Kembali</a>
    </div>

    <div class="p-6 space-y-8">
        <h2 class="text-2xl font-bold text-green-700">Daftar Pembayaran</h2>

        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
            <?php $i=1; while($p=mysqli_fetch_assoc($payments)): ?>
            <div class="bg-white rounded p-4 shadow hover:shadow-lg transition animate-fade-in relative">
                <div class="absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded <?= 
                    $p['status']=='pending' ? 'bg-yellow-300' : ($p['status']=='accepted' ? 'bg-green-300' : 'bg-red-300') ?>">
                    <?= htmlspecialchars(ucfirst($p['status'])) ?>
                </div>
                <div class="mb-2 text-green-700 font-semibold">#<?= $i++ ?></div>
                <p class="text-sm mb-1">Employee: <span class="font-medium"><?= htmlspecialchars($p['employee']) ?></span></p>
                <p class="text-sm mb-1">Request ID: <?= htmlspecialchars($p['request_id']) ?></p>
                <p class="text-sm mb-1">Jumlah: <span class="font-medium">Rp <?= number_format($p['amount']) ?></span></p>
                <p class="text-xs text-gray-500 mb-2">Tanggal: <?= htmlspecialchars($p['created_at']) ?></p>
                <div class="w-full h-32 overflow-hidden rounded mb-3">
                    <?php if($p['pop']): ?>
                    <a href="<?= htmlspecialchars($p['pop']) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($p['pop']) ?>" class="object-cover w-full h-full hover:scale-105 transition" alt="Bukti">
                    </a>
                    <?php else: ?>
                    <div class="text-gray-400 flex items-center justify-center h-full">Tidak ada bukti</div>
                    <?php endif; ?>
                </div>
                <button onclick="document.getElementById('edit<?= $p['id'] ?>').classList.remove('hidden')" class="bg-blue-500 text-white w-full py-2 rounded hover:bg-blue-600 transition">Edit Status</button>
            </div>

            <!-- Modal Edit -->
            <div id="edit<?= $p['id'] ?>" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white p-8 rounded shadow w-96 max-w-full animate-fade-in">
                    <h2 class="text-xl font-bold mb-4 text-green-700">Update Status Pembayaran</h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <select name="status" class="border p-2 w-full rounded" required>
                            <option value="">Pilih Status</option>
                            <option value="pending" <?= $p['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="accepted" <?= $p['status']=='accepted'?'selected':'' ?>>Accepted</option>
                            <option value="decline" <?= $p['status']=='decline'?'selected':'' ?>>Decline</option>
                        </select>
                        <button type="submit" name="update" class="bg-green-600 text-white w-full py-2 rounded hover:bg-green-700 transition">Update</button>
                        <button type="button" onclick="document.getElementById('edit<?= $p['id'] ?>').classList.add('hidden')" class="bg-red-500 text-white w-full py-2 rounded hover:bg-red-600 transition">Tutup</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
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
