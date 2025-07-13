<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Hapus user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

// Tambah user
if (isset($_POST['add'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    mysqli_query($conn, "
        INSERT INTO users (name, username, email, password, role)
        VALUES ('$name', '$username', '$email', '$password', '$role')
    ");
    header("Location: users.php");
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>Kelola Users – Koperasi Sawit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-100 min-h-screen font-sans">

    <!-- Header -->
    <div class="relative bg-green-700 h-56 flex flex-col justify-center items-center text-white overflow-hidden">
        <img src="img/foto2.jpeg"
             alt="Sawit"
             class="absolute w-full h-full object-cover opacity-40">
        <div class="relative z-10 text-center">
            <h1 class="text-3xl font-extrabold drop-shadow-lg">Kelola Users</h1>
            <p class="mt-1 font-medium">Koperasi Sawit</p>
        </div>
        <a href="dashboard_admin.php"
           class="absolute top-4 right-6 bg-gray-500 hover:bg-gray-600 px-3 py-1 rounded z-10">
            Kembali
        </a>
    </div>

    <div class="p-6 space-y-8">

        <!-- Form Tambah User -->
        <div class="bg-white p-6 rounded-xl shadow animate-fade-in">
            <h2 class="text-xl font-bold text-green-700 mb-4">Tambah User Baru</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="name" placeholder="Nama Lengkap"
                       class="border p-2 rounded" required>
                <input type="text" name="username" placeholder="Username"
                       class="border p-2 rounded" required>
                <input type="email" name="email" placeholder="Email"
                       class="border p-2 rounded" required>

                <!-- Password with toggle -->
                <div class="relative">
                    <input id="new-password"
                           type="password"
                           name="password"
                           placeholder="Password"
                           class="border p-2 rounded w-full pr-10"
                           required>
                    <button type="button"
                            onclick="toggleNewPassword()"
                            class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-600 hover:text-gray-800">
                        👁️
                    </button>
                </div>

                <select name="role"
                        class="border p-2 rounded md:col-span-2"
                        required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>

                <button type="submit"
                        name="add"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition md:col-span-2">
                    Tambah User
                </button>
            </form>
        </div>

        <!-- Daftar Users -->
        <div class="bg-white p-6 rounded-xl shadow animate-fade-in">
            <h2 class="text-xl font-bold text-green-700 mb-4">Daftar Users</h2>
            <div class="overflow-x-auto rounded">
                <table class="min-w-full divide-y divide-green-200">
                    <thead class="bg-green-500 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">#</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Username</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Role</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-100">
                        <?php $i = 1; while($u = mysqli_fetch_assoc($users)): ?>
                        <tr class="hover:bg-green-50 transition">
                            <td class="py-2 px-4"><?= $i++ ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($u['name']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($u['role']) ?></td>
                            <td class="py-2 px-4 text-center">
                                <a href="users.php?delete=<?= $u['id'] ?>"
                                   class="inline-flex items-center bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
                                   onclick="return confirm('Hapus user ini?')">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function toggleNewPassword() {
        const pw = document.getElementById('new-password');
        pw.type = pw.type === 'password' ? 'text' : 'password';
    }
    </script>

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
