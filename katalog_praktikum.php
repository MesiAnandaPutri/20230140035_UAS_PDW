<?php
session_start();

// KONEKSI DATABASE
$host = 'localhost';
$dbname = 'db_simprak';
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

// Array warna pastel untuk border kartu
$card_colors = ['border-pink-400', 'border-blue-400', 'border-purple-400'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Mata Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50">
    
    <nav class="bg-pink-100 shadow-md sticky top-0 z-50 border-b border-pink-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <span class="text-2xl font-bold text-purple-600">SIMPRAK</span>
                </div>
                <div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="mahasiswa/dashboard.php" class="text-gray-600 hover:text-purple-600 px-3 py-2 rounded-md font-medium">Dashboard</a>
                        <a href="logout.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg ml-2 transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-purple-600 px-3 py-2 rounded-md font-medium">Login</a>
                        <a href="register.php" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-lg ml-2 transition-colors">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10 p-5">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Katalog Mata Praktikum</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($praktikum_list)): ?>
                <p class="text-gray-600 col-span-3">Belum ada mata praktikum yang tersedia.</p>
            <?php else: ?>
                <?php foreach ($praktikum_list as $index => $praktikum): 
                    // Ambil warna untuk kartu secara bergiliran
                    $color = $card_colors[$index % count($card_colors)];
                ?>
                    <div class="bg-white rounded-2xl shadow-lg flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border-t-4 <?= $color ?>">
                        <div class="p-6 flex-grow">
                            <h3 class="text-xl font-bold text-gray-800 mb-3"><?= htmlspecialchars($praktikum['nama_mk']) ?></h3>
                            <p class="text-gray-600 text-base mb-4">
                                <?= htmlspecialchars(substr($praktikum['deskripsi'], 0, 120)) ?>...
                            </p>
                        </div>
                        <div class="p-5 bg-gray-50 rounded-b-2xl mt-auto">
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa'): ?>
                                <?php if (isset($pendaftaran_ids[$praktikum['id']])): ?>
                                    <a href="mahasiswa/my_courses.php" class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                        Sudah Terdaftar
                                    </a>
                                <?php else: ?>
                                    <a href="proses_pendaftaran.php?id_matkul=<?= $praktikum['id'] ?>" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
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