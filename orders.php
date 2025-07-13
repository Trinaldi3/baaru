<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Update status pesanan
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: orders.php");
}

$orders = mysqli_query($conn, "SELECT orders.*, users.name FROM orders JOIN users ON orders.customer_id=users.id");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Tambah ini biar responsive -->
    <title>Kelola Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<!-- Body pakai flex column agar footer nempel di bawah -->
<body class="bg-green-100 flex flex-col min-h-screen">
    <div class="bg-green-700 p-4 text-white flex justify-between items-center">
        <h1 class="text-xl font-bold">Kelola Orders</h1>
        <a href="dashboard_admin.php" class="bg-gray-500 hover:bg-gray-600 px-3 py-1 rounded">Kembali</a>
    </div>

    <!-- Konten utama pakai flex-grow -->
    <div class="p-6 flex-grow">
        <h2 class="text-2xl font-semibold mb-6 text-green-700">Daftar Pesanan</h2>
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-green-200 text-green-900">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Created At</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php $i=1; while($o=mysqli_fetch_assoc($orders)): ?>
                    <tr class="border-t hover:bg-green-50">
                        <td class="py-2 px-4"><?= $i++ ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($o['name']) ?></td>
                        <td class="py-2 px-4">Rp <?= number_format($o['total_price']) ?></td>
                        <td class="py-2 px-4">
                            <span class="<?= 
                                $o['status']=='paid' ? 'text-green-600 font-semibold' : 
                                ($o['status']=='cancel' ? 'text-red-600 font-semibold' : 'text-yellow-600 font-semibold') ?>">
                                <?= htmlspecialchars(ucfirst($o['status'])) ?>
                            </span>
                        </td>
                        <td class="py-2 px-4"><?= htmlspecialchars($o['created_at']) ?></td>
                        <td class="py-2 px-4 text-center">
                            <button onclick="document.getElementById('edit<?= $o['id'] ?>').classList.remove('hidden')" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</button>

                            <!-- Modal -->
                            <div id="edit<?= $o['id'] ?>" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                <div class="bg-white p-6 rounded shadow w-full max-w-md max-w-full relative">
                                    <h2 class="text-xl font-semibold mb-4 text-green-700">Update Order #<?= $o['id'] ?></h2>
                                    <form method="POST" class="space-y-3">
                                        <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                        <label class="block">
                                            <span class="text-gray-700">Status Pesanan</span>
                                            <select name="status" class="border p-2 w-full rounded" required>
                                                <option value="">Pilih Status</option>
                                                <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Pending</option>
                                                <option value="paid" <?= $o['status']=='paid'?'selected':'' ?>>Paid</option>
                                                <option value="cancel" <?= $o['status']=='cancel'?'selected':'' ?>>Cancel</option>
                                            </select>
                                        </label>
                                        <div class="flex justify-end space-x-2 pt-2">
                                            <button type="submit" name="update" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
                                            <button type="button" onclick="document.getElementById('edit<?= $o['id'] ?>').classList.add('hidden')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Tutup</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- End Modal -->
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer tetap di dasar halaman -->
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
