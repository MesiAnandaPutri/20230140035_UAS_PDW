<?php
// Atur variabel untuk halaman ini
$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';

// Sertakan header
include 'templates/header.php';

// Validasi ID Laporan dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: laporan.php");
    exit;
}
$id_laporan = $_GET['id'];

// KONEKSI DATABASE
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

// Ambil detail lengkap dari laporan yang akan dinilai
$sql = "SELECT l.id as laporan_id, l.file_laporan, l.nilai, l.feedback, u.nama as nama_mahasiswa, m.judul_modul, mp.nama_mk FROM laporan AS l JOIN users AS u ON l.id_mahasiswa = u.id JOIN modul AS m ON l.id_modul = m.id JOIN mata_praktikum AS mp ON m.id_matkul = mp.id WHERE l.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$laporan) { die("Laporan tidak ditemukan."); }
?>

<div class="mb-6">
    <a href="laporan.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-purple-700 font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
        Kembali ke Laporan Masuk
    </a>
</div>


<div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border-t-4 border-purple-400">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-2">
            <h2 class="text-2xl font-bold text-purple-800 mb-6">Detail Laporan</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Mahasiswa</p>
                    <p class="font-semibold text-lg text-gray-800"><?= htmlspecialchars($laporan['nama_mahasiswa']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mata Praktikum</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($laporan['nama_mk']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Modul</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($laporan['judul_modul']) ?></p>
                </div>
                <div class="pt-4">
                    <p class="text-sm text-gray-500 mb-2">File Laporan Mahasiswa</p>
                    <a href="../uploads/laporan/<?= htmlspecialchars($laporan['file_laporan']) ?>" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-5 rounded-lg shadow-md" download>
                        Unduh Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="md:col-span-1 border-t md:border-t-0 md:border-l border-gray-200 md:pl-8 pt-6 md:pt-0">
            <h2 class="text-2xl font-bold text-purple-800 mb-6">Form Penilaian</h2>
            <form action="proses_nilai.php" method="POST" class="space-y-6">
                <input type="hidden" name="id_laporan" value="<?= $laporan['laporan_id'] ?>">
                <div>
                    <label for="nilai" class="block text-sm font-medium text-gray-700 mb-1">Nilai (0-100)</label>
                    <input type="number" name="nilai" id="nilai" min="0" max="100" step="0.01" value="<?= htmlspecialchars($laporan['nilai'] ?? '') ?>" class="block w-full px-3 py-2 border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback (Opsional)</label>
                    <textarea name="feedback" id="feedback" rows="5" class="block w-full px-3 py-2 border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 rounded-md shadow-sm"><?= htmlspecialchars($laporan['feedback'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 rounded-lg shadow-md transition-transform hover:scale-105">
                    Simpan Nilai
                </button>
            </form>
        </div>
        
    </div>
</div>

<?php
// Memanggil footer
include 'templates/footer.php';
?>