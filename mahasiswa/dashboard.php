<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php'; 

// KONEKSI DATABASE & LOGIKA (TIDAK BERUBAH)
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage());}
$id_mahasiswa = $_SESSION['user_id'];
$stmt_diikuti = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE id_mahasiswa = ?");
$stmt_diikuti->execute([$id_mahasiswa]);
$praktikum_diikuti = $stmt_diikuti->fetchColumn();
$stmt_selesai = $pdo->prepare("SELECT COUNT(*) FROM laporan WHERE id_mahasiswa = ? AND nilai IS NOT NULL");
$stmt_selesai->execute([$id_mahasiswa]);
$tugas_selesai = $stmt_selesai->fetchColumn();
$stmt_menunggu = $pdo->prepare("SELECT COUNT(*) FROM laporan WHERE id_mahasiswa = ? AND nilai IS NULL");
$stmt_menunggu->execute([$id_mahasiswa]);
$tugas_menunggu = $stmt_menunggu->fetchColumn();
$stmt_aktivitas = $pdo->prepare("SELECT l.id_modul, l.nilai, m.judul_modul FROM laporan l JOIN modul m ON l.id_modul = m.id WHERE l.id_mahasiswa = ? ORDER BY l.tgl_kumpul DESC LIMIT 3");
$stmt_aktivitas->execute([$id_mahasiswa]);
$aktivitas_terbaru = $stmt_aktivitas->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-gradient-to-r from-purple-200 to-pink-200 text-purple-800 p-8 rounded-2xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-80">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
    
    <div class="bg-white p-6 rounded-2xl shadow-md text-center border-b-4 border-blue-200">
        <div class="text-5xl font-bold text-blue-500"><?= $praktikum_diikuti ?></div>
        <div class="mt-2 text-lg text-gray-500">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-md text-center border-b-4 border-purple-200">
        <div class="text-5xl font-bold text-purple-500"><?= $tugas_selesai ?></div>
        <div class="mt-2 text-lg text-gray-500">Tugas Selesai Dinilai</div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-md text-center border-b-4 border-pink-200">
        <div class="text-5xl font-bold text-pink-500"><?= $tugas_menunggu ?></div>
        <div class="mt-2 text-lg text-gray-500">Tugas Menunggu Nilai</div>
    </div>
    
</div>

<div class="bg-white p-6 rounded-2xl shadow-lg">
    <h3 class="text-2xl font-bold text-gray-700 mb-4">Aktivitas Terbaru</h3>
    <ul class="space-y-3">
        
        <?php if (empty($aktivitas_terbaru)): ?>
            <li class="text-gray-500 p-3">Belum ada aktivitas terbaru.</li>
        <?php else: ?>
            <?php foreach ($aktivitas_terbaru as $aktivitas): ?>
                <li class="flex items-center p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 rounded-lg">
                    <?php if ($aktivitas['nilai'] !== null): ?>
                        <div class="bg-blue-100 rounded-full p-2 mr-4">
                            <span class="text-xl">🔔</span>
                        </div>
                        <div>
                            Nilai untuk <a href="#" class="font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($aktivitas['judul_modul']) ?></a> telah diberikan.
                        </div>
                    <?php else: ?>
                        <div class="bg-green-100 rounded-full p-2 mr-4">
                            <span class="text-xl">✅</span>
                        </div>
                        <div>
                            Anda telah mengumpulkan laporan untuk <a href="#" class="font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($aktivitas['judul_modul']) ?></a>.
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        
    </ul>
</div>


<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>