<?php
// Atur variabel untuk halaman ini
$pageTitle = 'Manajemen Praktikum';
$activePage = 'praktikum';

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

// --- LOGIKA PENCARIAN ---
$search_query = $_GET['search'] ?? '';
$sql = "SELECT * FROM mata_praktikum";
$params = [];
if (!empty($search_query)) {
    $sql .= " WHERE nama_mk LIKE :search OR kode_mk LIKE :search";
    $params[':search'] = "%" . $search_query . "%";
}
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$praktikum_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="tambah_praktikum.php" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-lg mb-4 shadow-md transition-transform hover:scale-105">
    + Tambah Praktikum Baru
</a>

<form action="kelola_praktikum.php" method="GET" class="bg-white p-4 rounded-lg mb-6 flex flex-wrap gap-2 items-end shadow">
    <div class="flex-grow">
        <label for="search" class="block text-sm font-medium text-gray-700">Cari Berdasarkan Nama atau Kode MK</label>
        <input type="text" name="search" id="search" value="<?= htmlspecialchars($search_query) ?>" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent sm:text-sm rounded-md" placeholder="Contoh: Pemrograman Web">
    </div>
    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md self-end">Cari</button>
    <a href="kelola_praktikum.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-md shadow-sm self-end">Reset</a>
</form>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-purple-100">
            <tr>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Kode MK</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Nama Praktikum</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800 uppercase tracking-wider">Deskripsi</th>
                <th class="py-3 px-6 text-center text-sm font-semibold text-purple-800 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if (empty($praktikum_list)): ?>
                <tr><td colspan="4" class="py-4 px-6 text-center">Data tidak ditemukan.</td></tr>
            <?php else: ?>
                <?php foreach ($praktikum_list as $praktikum): ?>
                    <tr class="border-b border-gray-200 hover:bg-pink-50">
                        <td class="py-3 px-6"><?= htmlspecialchars($praktikum['kode_mk']) ?></td>
                        <td class="py-3 px-6 font-medium"><?= htmlspecialchars($praktikum['nama_mk']) ?></td>
                        <td class="py-3 px-6 text-sm"><?= htmlspecialchars(substr($praktikum['deskripsi'], 0, 70)) ?>...</td>
                        <td class="py-3 px-6 text-center">
                            <a href="edit_praktikum.php?id=<?= $praktikum['id'] ?>" class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full hover:bg-blue-200 text-xs font-semibold">Edit</a>
                            <a href="hapus_praktikum.php?id=<?= $praktikum['id'] ?>" class="bg-red-100 text-red-800 py-1 px-3 rounded-full hover:bg-red-200 text-xs font-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
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