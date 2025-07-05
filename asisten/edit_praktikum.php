<?php
// (Pastikan ada session check di sini jika file ini akan diakses langsung)
// session_start();
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
//     header('Location: ../login.php');
//     exit;
// }

$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: kelola_praktikum.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt->execute([$id]);
$praktikum = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen">

    <div class="container my-10 p-8 bg-white rounded-2xl shadow-xl max-w-2xl w-full border-t-4 border-pink-400">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Form Edit Praktikum</h1>
        
        <form action="proses_praktikum.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $praktikum['id'] ?>">

            <div class="space-y-6">
                <div>
                    <label for="kode_mk" class="block text-sm font-medium text-gray-700 mb-1">Kode Mata Praktikum</label>
                    <input type="text" name="kode_mk" id="kode_mk" value="<?= htmlspecialchars($praktikum['kode_mk']) ?>" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
                </div>

                <div>
                    <label for="nama_mk" class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Praktikum</label>
                    <input type="text" name="nama_mk" id="nama_mk" value="<?= htmlspecialchars($praktikum['nama_mk']) ?>" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm"><?= htmlspecialchars($praktikum['deskripsi']) ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-start gap-4 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-5 rounded-lg shadow-md transition-transform hover:scale-105">Update</button>
                <a href="kelola_praktikum.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-5 rounded-lg transition-colors">Batal</a>
            </div>
        </form>
    </div>

</body>
</html>