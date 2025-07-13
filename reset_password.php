<?php
session_start();
include 'config.php';

if (!isset($_GET['token'])) {
    die("Token tidak valid.");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// Hanya cek kecocokan token, tanpa expiry (development mode)
$query = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");

if (mysqli_num_rows($query) === 0) {
    die("Token tidak valid atau sudah kadaluarsa.");
}

$user = mysqli_fetch_assoc($query);

if (isset($_POST['submit'])) {
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    // Untuk produksi, gunakan: password_hash($password, PASSWORD_DEFAULT)
    mysqli_query($conn, "
        UPDATE users 
        SET password='$password',
            reset_token=NULL,
            token_expiry=NULL
        WHERE id='{$user['id']}'
    ");
    $success = "Password berhasil diubah. <a href='login.php' class='text-green-600 hover:underline'>Login di sini</a>.";
}
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>Reset Password - Koperasi Sawit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- Body pakai flex column supaya footer nempel di bawah -->
<body class="bg-green-50 flex flex-col min-h-screen">

  <!-- Konten utama pakai flex-grow -->
  <div class="flex-grow flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-green-700 mb-4 text-center">🔑 Reset Password</h1>

        <?php if(isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?= $success ?>
            </div>
        <?php else: ?>
            <form method="POST" class="space-y-4">
                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan password baru"
                        class="border p-3 w-full rounded focus:outline-green-500"
                        required
                    >
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-600 hover:text-green-800"
                        aria-label="Toggle password visibility"
                    >👁️</button>
                </div>

                <button
                    type="submit"
                    name="submit"
                    class="bg-green-600 hover:bg-green-700 w-full p-3 text-white rounded font-semibold"
                >Reset Password</button>
            </form>
        <?php endif; ?>

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

  <script>
  function togglePassword() {
      const pwd = document.getElementById('password');
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
  }
  </script>

</body>
</html>
