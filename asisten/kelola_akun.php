<?php
$pageTitle = 'Kelola Akun Pengguna';
$activePage = 'akun';
include 'templates/header.php';

// KONEKSI & LOGIKA PENCARIAN
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try { $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass); } catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }
$search_query = $_GET['search'] ?? '';
$sql = "SELECT * FROM users";
$params = [];
if (!empty($search_query)) {
    $sql .= " WHERE nama LIKE :search OR email LIKE :search";
    $params[':search'] = "%" . $search_query . "%";
}
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="tambah_akun.php" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-lg mb-4">
    + Tambah Akun Baru
</a>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-purple-100">
            <tr>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800">Nama</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800">Email</th>
                <th class="py-3 px-6 text-left text-sm font-semibold text-purple-800">Peran</th>
                <th class="py-3 px-6 text-center text-sm font-semibold text-purple-800">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php foreach ($user_list as $user_item): ?>
                <tr class="border-b hover:bg-pink-50">
                    <td class="py-3 px-6"><?= htmlspecialchars($user_item['nama']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($user_item['email']) ?></td>
                    <td class="py-3 px-6 capitalize"><?= htmlspecialchars($user_item['role']) ?></td>
                    <td class="py-3 px-6 text-center">
                        <a href="edit_akun.php?id=<?= $user_item['id'] ?>" class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">Edit</a>
                        <?php if ($_SESSION['user_id'] != $user_item['id']): // Jangan biarkan admin menghapus diri sendiri ?>
                            <a href="hapus_akun.php?id=<?= $user_item['id'] ?>" class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs font-semibold" onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'templates/footer.php'; ?>