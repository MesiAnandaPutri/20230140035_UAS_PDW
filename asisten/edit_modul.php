<?php
session_start();
// Pastikan hanya asisten yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit;
}

// KONEKSI DATABASE
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

// Ambil ID dari URL dan data modul yang akan diedit
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: kelola_modul.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM modul WHERE id = ?");
$stmt->execute([$id]);
$modul = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$modul) { die("Modul tidak ditemukan."); }

// Ambil daftar praktikum untuk dropdown
$praktikum_options_stmt = $pdo->query("SELECT id, nama_mk FROM mata_praktikum ORDER BY nama_mk");
$praktikum_options = $praktikum_options_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Modul</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen">

    <div class="container my-10 p-8 bg-white rounded-2xl shadow-xl max-w-2xl w-full border-t-4 border-pink-400">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Form Edit Modul</h1>
        
        <form action="proses_modul.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $modul['id'] ?>">
            <input type="hidden" name="file_lama" value="<?= htmlspecialchars($modul['file_materi']) ?>">

            <div class="space-y-6">
                <div>
                    <label for="id_matkul" class="block text-sm font-medium text-gray-700 mb-1">Pilih Mata Praktikum</label>
                    <select name="id_matkul" id="id_matkul" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 rounded-md shadow-sm" required>
                        <?php foreach ($praktikum_options as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($p['id'] == $modul['id_matkul']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_mk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="judul_modul" class="block text-sm font-medium text-gray-700 mb-1">Judul Modul</label>
                    <input type="text" name="judul_modul" id="judul_modul" value="<?= htmlspecialchars($modul['judul_modul']) ?>" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="deskripsi_modul" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi_modul" id="deskripsi_modul" rows="4" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 rounded-md shadow-sm"><?= htmlspecialchars($modul['deskripsi_modul']) ?></textarea>
                </div>
                <div>
                    <label for="file_materi" class="block text-sm font-medium text-gray-700 mb-1">Upload File Baru (Opsional)</label>
                    <input type="file" name="file_materi" id="file_materi" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200" accept=".pdf,.doc,.docx">
                    <?php if (!empty($modul['file_materi'])): ?>
                        <p class="text-xs text-gray-600 mt-1">File saat ini: <?= htmlspecialchars($modul['file_materi']) ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti file.</p>
                </div>
            </div>

            <div class="flex items-center justify-start gap-4 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-5 rounded-lg shadow-md">
                    Update Modul
                </button>
                <a href="kelola_modul.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-5 rounded-lg">
                    Batal
                </a>
            </div>
        </form>
    </div>

</body>
</html>