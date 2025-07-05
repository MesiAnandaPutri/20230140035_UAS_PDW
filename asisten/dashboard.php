<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// 2. Panggil Header
require_once 'templates/header.php';

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

// --- MENGAMBIL DATA UNTUK KARTU STATISTIK ---
$total_modul = $pdo->query("SELECT COUNT(*) FROM modul")->fetchColumn();
$total_laporan = $pdo->query("SELECT COUNT(*) FROM laporan")->fetchColumn();
$laporan_belum_dinilai = $pdo->query("SELECT COUNT(*) FROM laporan WHERE nilai IS NULL")->fetchColumn();

// --- MENGAMBIL DATA UNTUK AKTIVITAS TERBARU ---
$stmt_aktivitas = $pdo->query("
    SELECT u.nama as nama_mahasiswa, m.judul_modul, l.tgl_kumpul
    FROM laporan AS l
    JOIN users AS u ON l.id_mahasiswa = u.id
    JOIN modul AS m ON l.id_modul = m.id
    ORDER BY l.tgl_kumpul DESC LIMIT 5
");
$aktivitas_terbaru = $stmt_aktivitas->fetchAll(PDO::FETCH_ASSOC);

// Array warna pastel untuk lingkaran inisial
$pastel_colors = [
    'bg-blue-200 text-blue-800',
    'bg-pink-200 text-pink-800',
    'bg-purple-200 text-purple-800',
];
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <div class="transform hover:scale-105 transition-transform duration-300 bg-gradient-to-br from-blue-200 to-purple-200 text-purple-900 p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium opacity-80">Total Modul Diajarkan</p>
                <p class="text-4xl font-bold"><?= $total_modul ?></p>
            </div>
            <div class="bg-white/30 p-3 rounded-full">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </div>
        </div>
    </div>

    <div class="transform hover:scale-105 transition-transform duration-300 bg-gradient-to-br from-purple-200 to-pink-200 text-purple-900 p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium opacity-80">Total Laporan Masuk</p>
                <p class="text-4xl font-bold"><?= $total_laporan ?></p>
            </div>
            <div class="bg-white/30 p-3 rounded-full">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <div class="transform hover:scale-105 transition-transform duration-300 bg-gradient-to-br from-pink-200 to-orange-200 text-purple-900 p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium opacity-80">Laporan Belum Dinilai</p>
                <p class="text-4xl font-bold"><?= $laporan_belum_dinilai ?></p>
            </div>
            <div class="bg-white/30 p-3 rounded-full">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8 border-t-4 border-purple-300">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        
        <?php if (empty($aktivitas_terbaru)): ?>
            <p class="text-gray-500">Belum ada aktivitas laporan.</p>
        <?php else: ?>
            <?php foreach ($aktivitas_terbaru as $index => $aktivitas): 
                $inisial = '';
                $nama_parts = explode(' ', $aktivitas['nama_mahasiswa']);
                $inisial .= !empty($nama_parts[0]) ? strtoupper(substr($nama_parts[0], 0, 1)) : '';
                $inisial .= !empty($nama_parts[1]) ? strtoupper(substr($nama_parts[1], 0, 1)) : $inisial;
                
                // Mengambil warna dari array secara bergiliran
                $color_class = $pastel_colors[$index % count($pastel_colors)];
            ?>
                <div class="flex items-center p-2 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4 flex-shrink-0 <?= $color_class ?>">
                        <span class="font-bold"><?= $inisial ?></span>
                    </div>
                    <div>
                        <p class="text-gray-800"><strong><?= htmlspecialchars($aktivitas['nama_mahasiswa']) ?></strong> mengumpulkan laporan untuk <strong><?= htmlspecialchars($aktivitas['judul_modul']) ?></strong></p>
                        <p class="text-sm text-gray-500"><?= date('d M Y, H:i', strtotime($aktivitas['tgl_kumpul'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>