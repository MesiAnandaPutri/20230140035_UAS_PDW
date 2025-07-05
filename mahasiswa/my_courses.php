<?php
// Atur variabel untuk halaman ini agar header bisa menandai menu aktif
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses'; // Disesuaikan dengan link di header

// Memanggil header mahasiswa yang sudah didesain
require_once 'templates/header_mahasiswa.php';

// KONEKSI DATABASE & LOGIKA (TIDAK BERUBAH)
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

$id_mahasiswa = $_SESSION['user_id'];

$sql = "SELECT mp.id, mp.kode_mk, mp.nama_mk, mp.deskripsi FROM mata_praktikum AS mp JOIN pendaftaran AS p ON mp.id = p.id_matkul WHERE p.id_mahasiswa = :id_mahasiswa ORDER BY mp.nama_mk ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_mahasiswa' => $id_mahasiswa]);
$praktikum_diikuti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Array warna pastel untuk border kartu
$card_colors = ['border-pink-400', 'border-blue-400', 'border-purple-400'];
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Praktikum yang Saya Ikuti</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (empty($praktikum_diikuti)): ?>
        <div class="col-span-full bg-white p-8 rounded-2xl shadow-lg text-center">
            <h2 class="text-2xl font-bold text-gray-700">Oops!</h2>
            <p class="text-gray-500 mt-2">Anda belum mendaftar pada mata praktikum manapun.</p>
            <a href="../katalog_praktikum.php" class="mt-6 inline-block bg-purple-500 text-white py-2 px-5 rounded-lg hover:bg-purple-600 font-semibold shadow-md">
                Lihat Katalog Praktikum
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($praktikum_diikuti as $index => $praktikum): 
            // Ambil warna untuk kartu secara bergiliran
            $color = $card_colors[$index % count($card_colors)];
        ?>
            <div class="bg-white rounded-2xl shadow-lg flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border-t-4 <?= $color ?>">
                <div class="p-6 flex-grow">
                    <p class="text-sm text-gray-500 mb-1"><?= htmlspecialchars($praktikum['kode_mk']) ?></p>
                    <h2 class="text-xl font-bold text-gray-800 mb-3"><?= htmlspecialchars($praktikum['nama_mk']) ?></h2>
                    <p class="text-gray-600 text-base">
                        <?= htmlspecialchars(substr($praktikum['deskripsi'], 0, 100)) ?>...
                    </p>
                </div>
                <div class="p-5 bg-gray-50 rounded-b-2xl mt-auto">
                    <a href="detail_praktikum.php?id=<?= $praktikum['id'] ?>" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                        Lihat Detail & Tugas
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// Memanggil footer
require_once 'templates/footer_mahasiswa.php';
?>