<?php
session_start();
include 'config.php';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $q     = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($q) > 0) {
        $user   = mysqli_fetch_assoc($q);
        $token  = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        mysqli_query($conn, "
            UPDATE users
            SET reset_token='$token', token_expiry='$expiry'
            WHERE id='{$user['id']}'
        ");

        // Tampilkan link langsung untuk development/testing
        $link = "http://localhost/baaru/reset_password.php?token=$token";
        $success = "Link reset password (development mode):<br>"
                 . "<a href=\"$link\" class=\"text-green-600 hover:underline\">$link</a>";
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lupa Password - Koperasi Sawit</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- Body pakai flex column supaya footer nempel di bawah -->
<body class="bg-green-50 flex flex-col min-h-screen">

  <!-- Konten utama pakai flex-grow -->
  <div class="flex-grow flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
      <h1 class="text-2xl font-bold text-green-700 mb-4 text-center">🔒 Lupa Password</h1>

      <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if(isset($success)): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
      <?php else: ?>
        <form method="POST" class="space-y-4">
          <input
            type="email"
            name="email"
            placeholder="Masukkan email terdaftar"
            class="border p-3 w-full rounded focus:outline-green-500"
            required
          >
          <button
            type="submit"
            name="submit"
            class="bg-green-600 hover:bg-green-700 w-full p-3 text-white rounded font-semibold"
          >Buat Link Reset</button>
        </form>
      <?php endif; ?>

      <p class="text-center mt-4">
        <a href="login.php" class="text-sm text-green-600 hover:underline">🔙 Kembali ke Login</a>
      </p>
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
