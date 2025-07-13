<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit;
}

$employee_id = $_SESSION['id'];

if (isset($_POST['add'])) {
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    mysqli_query($conn, "INSERT INTO requests (employee_id, goal, duration, type, status) VALUES ('$employee_id','$goal','$duration','$type','pending')");
    header("Location: requests_employee.php");
}

$requests = mysqli_query($conn, "SELECT * FROM requests WHERE employee_id=$employee_id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>My Requests - Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-r from-green-100 via-green-200 to-green-100 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-green-700 p-5 text-white flex justify-between items-center shadow">
        <h1 class="text-2xl font-bold tracking-wide">📄 Ajukan Request</h1>
        <a href="dashboard_employee.php" class="bg-gray-600 hover:bg-gray-700 transition px-4 py-2 rounded font-medium">⬅ Kembali</a>
    </header>

    <!-- Content -->
    <main class="flex-grow p-6 container mx-auto">
        <!-- Form Request -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-10 animate-fade-in">
            <h2 class="text-xl font-semibold mb-4 text-green-800 border-b pb-2">📝 Form Pengajuan Request</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Tujuan Request</label>
                    <input type="text" name="goal" placeholder="Contoh: Beli pupuk organik" class="border p-3 w-full rounded-lg focus:ring-2 focus:ring-green-400" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Durasi (bulan)</label>
                    <input type="number" name="duration" placeholder="Durasi pinjaman (misal 3)" class="border p-3 w-full rounded-lg focus:ring-2 focus:ring-green-400" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Tipe Request</label>
                    <select name="type" class="border p-3 w-full rounded-lg focus:ring-2 focus:ring-green-400" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="money">Money</option>
                        <option value="pupuk">Pupuk</option>
                        <option value="alat">Alat</option>
                        <option value="bibit">Bibit</option>
                    </select>
                </div>
                <button type="submit" name="add" class="bg-green-600 hover:bg-green-700 transition text-white px-6 py-3 rounded-lg font-semibold shadow">Ajukan Request</button>
            </form>
        </div>

        <!-- Daftar Request -->
        <div class="bg-white rounded-2xl shadow-xl p-6 animate-fade-in">
            <h2 class="text-xl font-semibold mb-4 text-green-800 border-b pb-2">📜 Daftar Request Saya</h2>
            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full text-left text-gray-700">
                    <thead class="bg-green-200 text-green-800">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Goal</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Catatan Admin</th>
                            <th class="py-3 px-4">Tanggal Kembali</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php $i=1; while($r=mysqli_fetch_assoc($requests)): ?>
                        <tr class="hover:bg-green-50 transition">
                            <td class="py-3 px-4"><?= $i++ ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($r['goal']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($r['duration']) ?> bln</td>
                            <td class="py-3 px-4 capitalize"><?= htmlspecialchars($r['type']) ?></td>
                            <td class="py-3 px-4 font-semibold <?= $r['status']=='accepted'?'text-green-600':($r['status']=='decline'?'text-red-600':'text-yellow-600') ?>"><?= htmlspecialchars($r['status']) ?></td>
                            <td class="py-3 px-4"><?= $r['notes'] ? htmlspecialchars($r['notes']) : '-' ?></td>
                            <td class="py-3 px-4"><?= $r['date_must_return'] ? htmlspecialchars($r['date_must_return']) : '-' ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
