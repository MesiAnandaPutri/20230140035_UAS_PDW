<?php
// Atur variabel untuk halaman ini
$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses'; // Dianggap bagian dari "Praktikum Saya"

// Memanggil header mahasiswa
require_once 'templates/header_mahasiswa.php';

// Validasi ID mata kuliah dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_courses.php");
    exit;
}

$id_matkul = $_GET['id'];
$id_mahasiswa = $_SESSION['user_id'];

// KONEKSI DATABASE
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

// 1. Ambil detail mata praktikum
$stmt_matkul = $pdo->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt_matkul->execute([$id_matkul]);
$matkul = $stmt_matkul->fetch(PDO::FETCH_ASSOC);

if (!$matkul) { header("Location: my_courses.php"); exit; }

// 2. Ambil semua modul untuk mata praktikum ini
$stmt_modul = $pdo->prepare("SELECT * FROM modul WHERE id_matkul = ? ORDER BY id ASC");
$stmt_modul->execute([$id_matkul]);
$modul_list = $stmt_modul->fetchAll(PDO::FETCH_ASSOC);

// 3. Ambil semua laporan yang sudah dikumpulkan mahasiswa untuk mata praktikum ini
$stmt_laporan = $pdo->prepare("SELECT id_modul, file_laporan, nilai, feedback FROM laporan WHERE id_mahasiswa = ? AND id_modul IN (SELECT id FROM modul WHERE id_matkul = ?)");
$stmt_laporan->execute([$id_mahasiswa, $id_matkul]);
$laporan_list = $stmt_laporan->fetchAll(PDO::FETCH_ASSOC);
$laporan_dikumpulkan = [];
foreach ($laporan_list as $laporan) { $laporan_dikumpulkan[$laporan['id_modul']] = $laporan; }

// Array warna pastel untuk border kartu modul
$card_colors = ['border-pink-400', 'border-blue-400', 'border-purple-400'];
?>

<div class="bg-gradient-to-r from-blue-200 to-purple-200 p-6 rounded-2xl shadow-lg mb-8">
    <a href="my_courses.php" class="text-sm text-purple-700 hover:underline mb-2 inline-block">&larr; Kembali ke Daftar Praktikum</a>
    <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($matkul['nama_mk']) ?></h1>
    <p class="text-gray-600 mt-1"><?= htmlspecialchars($matkul['deskripsi']) ?></p>
</div>

<div class="space-y-8">
    <?php if (empty($modul_list)): ?>
        <div class="bg-white text-center p-8 rounded-2xl shadow-lg">
            <p class="text-gray-600">Belum ada modul yang ditambahkan untuk praktikum ini.</p>
        </div>
    <?php else: ?>
        <?php foreach ($modul_list as $index => $modul): 
            $color = $card_colors[$index % count($card_colors)];
        ?>
            <div class="bg-white rounded-2xl shadow-xl flex flex-col md:flex-row overflow-hidden border-l-8 <?= $color ?>">
                <div class="p-6 md:w-2/3">
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($modul['judul_modul']) ?></h2>
                    <p class="text-gray-600 mt-1 mb-4"><?= htmlspecialchars($modul['deskripsi_modul']) ?></p>
                    <?php if (!empty($modul['file_materi'])): ?>
                        <a href="../uploads/materi/<?= htmlspecialchars($modul['file_materi']) ?>" class="inline-block bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 font-semibold shadow-md" download>
                           Unduh Materi
                        </a>
                    <?php endif; ?>
                </div>

                <div class="bg-gray-50 p-6 md:w-1/3">
                    <?php if (isset($laporan_dikumpulkan[$modul['id']])): 
                        $laporan = $laporan_dikumpulkan[$modul['id']]; ?>
                        <h3 class="font-semibold text-lg mb-2 text-gray-700">Status Laporan Anda</h3>
                        <div class="bg-green-100 text-green-800 p-3 rounded-lg text-center font-medium mb-3">Laporan sudah dikumpulkan.</div>
                        <?php if ($laporan['nilai'] !== null): ?>
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <p class="text-sm font-medium text-blue-800">Nilai Akhir:</p>
                                <p class="text-4xl font-bold text-blue-700"><?= htmlspecialchars($laporan['nilai']) ?></p>
                                <?php if(!empty($laporan['feedback'])): ?>
                                    <p class="text-sm font-medium text-blue-800 mt-2">Feedback Asisten:</p>
                                    <p class="text-sm text-blue-900 mt-1 p-2 bg-blue-200/50 rounded"><?= htmlspecialchars($laporan['feedback']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-yellow-100 text-yellow-800 p-3 rounded-lg text-center font-medium">Status: Menunggu Penilaian</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <h3 class="font-semibold text-lg mb-2 text-gray-700">Kumpulkan Laporan</h3>
                        <form action="proses_pengumpulan.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_modul" value="<?= $modul['id'] ?>">
                            <input type="hidden" name="id_matkul" value="<?= $id_matkul ?>">
                            <div>
                                <input type="file" name="file_laporan" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200" required>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                Kumpulkan
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>