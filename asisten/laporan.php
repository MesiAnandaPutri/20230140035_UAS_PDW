<?php
// Atur variabel untuk halaman ini
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';

// Sertakan header dengan desain pastel
include 'templates/header.php';

// KONEKSI DATABASE & LOGIKA FILTER (TIDAK BERUBAH)
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }
$filter_praktikum = $_GET['filter_praktikum'] ?? '';
$filter_mahasiswa = $_GET['filter_mahasiswa'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$sql = "SELECT l.id as laporan_id, l.tgl_kumpul, l.nilai, u.nama as nama_mahasiswa, m.judul_modul, mp.nama_mk FROM laporan AS l JOIN users AS u ON l.id_mahasiswa = u.id JOIN modul AS m ON l.id_modul = m.id JOIN mata_praktikum AS mp ON m.id_matkul = mp.id WHERE 1=1";
$params = [];
if (!empty($filter_praktikum)) { $sql .= " AND mp.id = :id_praktikum"; $params[':id_praktikum'] = $filter_praktikum; }
if (!empty($filter_mahasiswa)) { $sql .= " AND u.nama LIKE :nama_mahasiswa"; $params[':nama_mahasiswa'] = "%" . $filter_mahasiswa . "%"; }
if ($filter_status === 'dinilai') { $sql .= " AND l.nilai IS NOT NULL"; } elseif ($filter_status === 'belum_dinilai') { $sql .= " AND l.nilai IS NULL"; }
$sql .= " ORDER BY l.tgl_kumpul DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$laporan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
$praktikum_options_stmt = $pdo->query("SELECT id, nama_mk FROM mata_praktikum ORDER BY nama_mk");
$praktikum_options = $praktikum_options_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form action="laporan.php" method="GET" class="bg-white p-4 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end shadow">
    <div>
        <label for="filter_mahasiswa" class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
        <input type="text" name="filter_mahasiswa" id="filter_mahasiswa" value="<?= htmlspecialchars($filter_mahasiswa) ?>" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md">
    </div>
    <div>
        <label for="filter_praktikum" class="block text-sm font-medium text-gray-700">Mata Praktikum</label>
        <select name="filter_praktikum" id="filter_praktikum" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md">
            <option value="">Semua</option>
            <?php foreach ($praktikum_options as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($filter_praktikum == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['nama_mk']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="filter_status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="filter_status" id="filter_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md">
            <option value="">Semua</option>
            <option value="dinilai" <?= ($filter_status == 'dinilai') ? 'selected' : '' ?>>Sudah Dinilai</option>
            <option value="belum_dinilai" <?= ($filter_status == 'belum_dinilai') ? 'selected' : '' ?>>Belum Dinilai</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md">Filter</button>
        <a href="laporan.php" class="w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-md shadow-sm">Reset</a>
    </div>
</form>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-purple-100">
            <tr>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Mahasiswa</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Praktikum & Modul</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Tgl Kumpul</th>
                <th class="py-3 px-6 text-center text-sm font-semibold text-purple-800 uppercase tracking-wider">Status</th>
                <th class="py-3 px-6 text-center text-sm font-semibold text-purple-800 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if (empty($laporan_list)): ?>
                <tr><td colspan="5" class="py-4 px-6 text-center">Tidak ada laporan yang sesuai dengan filter.</td></tr>
            <?php else: ?>
                <?php foreach ($laporan_list as $laporan): ?>
                    <tr class="border-b border-gray-200 hover:bg-pink-50">
                        <td class="py-3 px-6 font-medium"><?= htmlspecialchars($laporan['nama_mahasiswa']) ?></td>
                        <td class="py-3 px-6">
                            <span class="block font-semibold"><?= htmlspecialchars($laporan['nama_mk']) ?></span>
                            <span class="block text-sm text-gray-500"><?= htmlspecialchars($laporan['judul_modul']) ?></span>
                        </td>
                        <td class="py-3 px-6 text-sm"><?= date('d M Y, H:i', strtotime($laporan['tgl_kumpul'])) ?></td>
                        <td class="py-3 px-6 text-center">
                            <?php if ($laporan['nilai'] !== null): ?>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Sudah Dinilai</span>
                            <?php else: ?>
                                <span class="bg-pink-100 text-pink-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Belum Dinilai</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <a href="beri_nilai.php?id=<?= $laporan['laporan_id'] ?>" class="bg-purple-100 text-purple-800 py-1 px-3 rounded-full hover:bg-purple-200 text-xs font-semibold">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Memanggil footer
include 'templates/footer.php';
?>