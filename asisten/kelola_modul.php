<?php
// Atur variabel untuk halaman ini
$pageTitle = 'Manajemen Modul';
$activePage = 'modul';

// Sertakan header dengan desain pastel
include 'templates/header.php';

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

// --- LOGIKA FILTER ---
$filter_praktikum = $_GET['filter_praktikum'] ?? '';
$sql = "SELECT m.id, m.judul_modul, m.file_materi, mp.nama_mk FROM modul AS m JOIN mata_praktikum AS mp ON m.id_matkul = mp.id";
$params = [];
if (!empty($filter_praktikum)) {
    $sql .= " WHERE m.id_matkul = :id_matkul";
    $params[':id_matkul'] = $filter_praktikum;
}
$sql .= " ORDER BY mp.nama_mk, m.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$modul_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
$praktikum_options_stmt = $pdo->query("SELECT id, nama_mk FROM mata_praktikum ORDER BY nama_mk");
$praktikum_options = $praktikum_options_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="tambah_modul.php" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-lg mb-4 shadow-md transition-transform hover:scale-105">
    + Tambah Modul Baru
</a>

<form action="kelola_modul.php" method="GET" class="bg-white p-4 rounded-lg mb-6 flex flex-wrap gap-2 items-end shadow">
    <div class="flex-grow">
        <label for="filter_praktikum" class="block text-sm font-medium text-gray-700">Filter Berdasarkan Mata Praktikum</label>
        <select name="filter_praktikum" id="filter_praktikum" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent sm:text-sm rounded-md">
            <option value="">Tampilkan Semua</option>
            <?php foreach ($praktikum_options as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($filter_praktikum == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nama_mk']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md self-end">Filter</button>
    <a href="kelola_modul.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-md shadow-sm self-end">Reset</a>
</form>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-purple-100">
            <tr>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Nama Modul</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Mata Praktikum</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">File Materi</th>
                <th class="py-3 px-6 text-center text-sm font-semibold text-purple-800 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if (empty($modul_list)): ?>
                <tr><td colspan="4" class="py-4 px-6 text-center">Data modul tidak ditemukan.</td></tr>
            <?php else: ?>
                <?php foreach ($modul_list as $modul): ?>
                    <tr class="border-b border-gray-200 hover:bg-pink-50">
                        <td class="py-3 px-6 font-medium"><?= htmlspecialchars($modul['judul_modul']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($modul['nama_mk']) ?></td>
                        <td class="py-3 px-6">
                            <?php if (!empty($modul['file_materi'])): ?>
                                <a href="../uploads/materi/<?= htmlspecialchars($modul['file_materi']) ?>" target="_blank" class="text-blue-600 hover:underline">
                                    Lihat File
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400">Tidak ada file</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <a href="edit_modul.php?id=<?= $modul['id'] ?>" class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full hover:bg-blue-200 text-xs font-semibold">Edit</a>
                            <a href="hapus_modul.php?id=<?= $modul['id'] ?>" class="bg-red-100 text-red-800 py-1 px-3 rounded-full hover:bg-red-200 text-xs font-semibold" onclick="return confirm('Yakin ingin menghapus modul ini?')">Hapus</a>
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