<?php
session_start();

// KONEKSI DATABASE
$host = 'localhost';
$dbname = 'db_simprak'; // Sesuaikan dengan nama database Anda
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil semua data mata praktikum
$stmt = $pdo->query("SELECT * FROM mata_praktikum ORDER BY nama_mk ASC");
$praktikum_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cek status pendaftaran untuk mahasiswa yang login
$pendaftaran_ids = [];
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa') {
    $id_mahasiswa = $_SESSION['user_id'];
    $stmt_pendaftaran = $pdo->prepare("SELECT id_matkul FROM pendaftaran WHERE id_mahasiswa = ?");
    $stmt_pendaftaran->execute([$id_mahasiswa]);
    $pendaftaran_ids = array_flip($stmt_pendaftaran->fetchAll(PDO::FETCH_COLUMN));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Mata Praktikum - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">SIMPRAK</h1>
            <div>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa'): ?>
                    <a href="mahasiswa/dashboard.php" class="text-gray-600 hover:text-blue-600 px-3 py-2">Dashboard</a>
                    <a href="logout.php" class="bg-red-500 text-white rounded-md px-4 py-2 hover:bg-red-600 ml-2">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-blue-600 px-3 py-2">Login</a>
                    <a href="register.php" class="bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 ml-2">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10 p-5">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Katalog Mata Praktikum</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_daftar'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Pendaftaran Berhasil!</strong>
                <span class="block sm:inline"> Anda sekarang bisa melihat praktikum di halaman "Praktikum Saya".</span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($praktikum_list)): ?>
                <p class="text-gray-600 col-span-3">Belum ada mata praktikum yang tersedia.</p>
            <?php else: ?>
                <?php foreach ($praktikum_list as $praktikum): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($praktikum['nama_mk']) ?></h3>
                            <p class="text-gray-700 text-base mb-4">
                                <?= htmlspecialchars($praktikum['deskripsi']) ?>
                            </p>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa'): ?>
                                <?php if (isset($pendaftaran_ids[$praktikum['id']])): ?>
                                    <a href="mahasiswa/praktikum_saya.php" class="block w-full text-center bg-green-500 text-white font-bold py-2 px-4 rounded">
                                        Sudah Terdaftar
                                    </a>
                                <?php else: ?>
                                    <a href="proses_pendaftaran.php?id_matkul=<?= $praktikum['id'] ?>" class="block w-full text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Daftar
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>