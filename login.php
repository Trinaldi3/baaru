<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query  = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row                = mysqli_fetch_assoc($result);
        $_SESSION['id']     = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']   = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: dashboard_admin.php");
        } elseif ($row['role'] === 'employee') {
            header("Location: dashboard_employee.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>Login Koperasi Sawit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- Body pakai flex column supaya footer nempel di bawah -->
<body class="bg-gradient-to-r from-green-400 to-green-600 flex flex-col min-h-screen">

    <!-- Konten utama pakai flex-grow -->
    <div class="flex-grow flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-green-50 rotate-6 scale-150"></div>
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold mb-6 text-green-700 text-center drop-shadow">Login Koperasi Sawit</h1>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 animate-pulse">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        class="border-2 border-green-300 w-full p-3 rounded focus:outline-none focus:border-green-500 transition"
                        required
                    >

                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Password"
                            class="border-2 border-green-300 w-full p-3 rounded focus:outline-none focus:border-green-500 transition"
                            required
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-600 hover:text-green-800"
                        >👁️</button>
                    </div>

                    <button
                        type="submit"
                        name="login"
                        class="bg-green-600 hover:bg-green-700 transition text-white w-full p-3 rounded text-lg font-semibold shadow-md"
                    >🔓 Login</button>
                </form>

                <p class="text-center mt-4">
                    <a href="forgot_password.php" class="text-sm text-green-600 hover:underline">
                        Lupa kata sandi?
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
    }
    </script>

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
