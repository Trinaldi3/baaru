<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit;
}

$employee_id = $_SESSION['id'];

if (isset($_POST['upload'])) {
    $request_id = mysqli_real_escape_string($conn, $_POST['request_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["pop"]["name"]);
    move_uploaded_file($_FILES["pop"]["tmp_name"], $target_file);

    mysqli_query($conn, "INSERT INTO trx_payments (employee_id, request_id, amount, pop, status) VALUES ('$employee_id','$request_id','$amount','$target_file','pending')");
    header("Location: payments_employee.php");
}

$requests = mysqli_query($conn, "SELECT * FROM requests WHERE employee_id=$employee_id AND status='accepted'");
$payments = mysqli_query($conn, "SELECT * FROM trx_payments WHERE employee_id=$employee_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>Upload Pembayaran - Koperasi Sawit</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100 min-h-screen flex flex-col font-sans">

    <!-- Header -->
    <header class="bg-green-700 p-4 text-white flex justify-between items-center shadow">
        <h1 class="text-xl font-bold">Upload Bukti Pembayaran - Koperasi Sawit</h1>
        <a href="dashboard_employee.php" class="bg-gray-500 hover:bg-gray-600 px-3 py-1 rounded">⬅ Kembali</a>
    </header>

    <main class="flex-grow p-6 space-y-8 w-full max-w-7xl mx-auto"> <!-- container diganti dengan w-full max-w biar responsive -->

        <!-- Form Upload -->
        <div class="bg-white p-6 rounded shadow animate-fade-in w-full">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Form Upload Bukti Pembayaran</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <select name="request_id" class="border p-2 w-full rounded" required>
                    <option value="">Pilih Request Disetujui</option>
                    <?php while($r=mysqli_fetch_assoc($requests)): ?>
                    <option value="<?= $r['id'] ?>">Request #<?= $r['id'] ?> - <?= htmlspecialchars($r['goal']) ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" name="amount" placeholder="Jumlah Pembayaran" class="border p-2 w-full rounded" required>
                <input type="file" name="pop" class="border p-2 w-full rounded" required>
                <button type="submit" name="upload" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 w-full sm:w-auto">Upload Bukti</button>
            </form>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="bg-white p-6 rounded shadow animate-fade-in w-full">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Riwayat Pembayaran Saya</h2>
            <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                <?php $i=1; while($p=mysqli_fetch_assoc($payments)): ?>
                <div class="bg-green-50 rounded p-4 shadow hover:shadow-md transition">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-green-700 font-semibold">#<?= $i++ ?></span>
                        <span class="text-xs px-2 py-1 rounded <?= 
                            $p['status']=='pending' ? 'bg-yellow-300' : ($p['status']=='accepted' ? 'bg-green-300' : 'bg-red-300') ?>">
                            <?= htmlspecialchars(ucfirst($p['status'])) ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 mb-1">Request ID: <span class="font-medium"><?= htmlspecialchars($p['request_id']) ?></span></p>
                    <p class="text-sm text-gray-700 mb-1">Jumlah: <span class="font-medium">Rp <?= number_format($p['amount']) ?></span></p>
                    <p class="text-xs text-gray-500 mb-2">Tanggal: <?= htmlspecialchars($p['created_at']) ?></p>
                    <div class="w-full h-32 overflow-hidden rounded">
                        <?php if($p['pop']): ?>
                        <a href="<?= htmlspecialchars($p['pop']) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($p['pop']) ?>" class="object-cover w-full h-full hover:scale-105 transition" alt="Bukti">
                        </a>
                        <?php else: ?>
                        <div class="text-gray-400 flex items-center justify-center h-full">Tidak ada bukti</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

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
