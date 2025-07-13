<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: products.php");
}

if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowed)) {
        die("Hanya file JPG, PNG, atau GIF yang diizinkan.");
    }

    $filename = time() . '_' . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        mysqli_query($conn, "INSERT INTO products (name, price, description, image, type) VALUES ('$name','$price','$desc','$target_file','$type')");
        header("Location: products.php");
        exit;
    } else {
        die("Upload gambar gagal. Pastikan folder 'uploads/' bisa ditulis server.");
    }
}

$products = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Biar responsive -->
    <title>Kelola Produk - Koperasi Sawit</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100 min-h-screen font-sans">
    <div class="relative bg-green-700 h-56 flex flex-col justify-center items-center text-white overflow-hidden">
        <img src="img/foto3.jpg" alt="Sawit" class="absolute w-full h-full object-cover opacity-40">
        <div class="relative z-10 text-center">
            <h1 class="text-3xl font-extrabold drop-shadow-lg">Kelola Produk</h1>
            <p class="mt-1 font-medium">Koperasi Sawit</p>
        </div>
        <a href="dashboard_admin.php" class="absolute top-4 right-6 bg-gray-500 hover:bg-gray-600 px-3 py-1 rounded z-10">Kembali</a>
    </div>

    <div class="p-6 space-y-8">
        <div class="bg-white p-6 rounded shadow animate-fade-in">
            <h2 class="text-xl font-bold text-green-700 mb-4">Tambah Produk Baru</h2>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="name" placeholder="Nama Produk" class="border p-2 rounded" required>
                <input type="text" name="price" placeholder="Harga (Rp)" class="border p-2 rounded" required>
                <select name="type" class="border p-2 rounded md:col-span-2" required>
                    <option value="">Pilih Tipe</option>
                    <option value="bibit">Bibit</option>
                    <option value="alat">Alat</option>
                    <option value="pupuk">Pupuk</option>
                </select>
                <textarea name="description" placeholder="Deskripsi Produk" class="border p-2 rounded md:col-span-2" required></textarea>
                <input type="file" name="image" class="border p-2 rounded md:col-span-2" required>
                <button type="submit" name="add" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition md:col-span-2">Tambah Produk</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow animate-fade-in">
            <h2 class="text-xl font-bold text-green-700 mb-4">Daftar Produk</h2>
            <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <?php $i=1; while($p=mysqli_fetch_assoc($products)): ?>
                <div class="bg-green-50 rounded shadow hover:shadow-lg transition transform hover:-translate-y-1 animate-fade-in">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded-t w-full h-40 object-cover">
                    <div class="p-4 space-y-2">
                        <h3 class="text-lg font-bold text-green-700"><?= htmlspecialchars($p['name']) ?></h3>
                        <p class="text-sm text-gray-700">Tipe: <span class="font-medium"><?= htmlspecialchars(ucfirst($p['type'])) ?></span></p>
                        <p class="text-sm text-gray-700">Harga: <span class="font-medium">Rp <?= htmlspecialchars(number_format($p['price'])) ?></span></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($p['description']) ?></p>
                        <div class="flex justify-end">
                            <a href="products.php?delete=<?= $p['id'] ?>" class="inline-flex items-center bg-red-500 text-white text-sm px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Hapus produk ini?')">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Hapus
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
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
