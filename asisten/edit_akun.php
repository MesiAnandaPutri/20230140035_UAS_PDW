<?php
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try { $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass); } catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: kelola_akun.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user_item = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen">
    <div class="p-8 bg-white rounded-2xl shadow-xl max-w-md w-full border-t-4 border-pink-400">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Form Edit Akun</h1>
        <form action="proses_akun.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $user_item['id'] ?>">
            <div class="space-y-6">
                <div><label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label><input type="text" name="nama" id="nama" value="<?= htmlspecialchars($user_item['nama']) ?>" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required></div>
                <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" id="email" value="<?= htmlspecialchars($user_item['email']) ?>" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required></div>
                <div><label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label><input type="password" name="password" id="password" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" placeholder="Kosongkan jika tidak diubah"></div>
                <div><label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label><select name="role" id="role" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required><option value="mahasiswa" <?= ($user_item['role'] == 'mahasiswa') ? 'selected' : '' ?>>Mahasiswa</option><option value="asisten" <?= ($user_item['role'] == 'asisten') ? 'selected' : '' ?>>Asisten</option></select></div>
            </div>
            <div class="flex items-center gap-4 mt-8 pt-6 border-t"><button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-5 rounded-lg">Update</button><a href="kelola_akun.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-5 rounded-lg">Batal</a></div>
        </form>
    </div>
</body>
</html>