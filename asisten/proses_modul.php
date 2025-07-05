<?php
session_start();
// Pastikan hanya asisten yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die("Akses ditolak.");
}

// --- KONEKSI DATABASE DITAMBAHKAN DI SINI ---
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
// --- AKHIR BLOK KONEKSI ---

// Direktori target untuk upload
$target_dir = "../uploads/materi/";

// Fungsi untuk menangani upload file, mengembalikan nama file unik atau null
function uploadFile($file_input, $target_dir) {
    if (isset($file_input) && $file_input['error'] == 0) {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $nama_file_unik = time() . '_' . basename($file_input["name"]);
        $target_file = $target_dir . $nama_file_unik;
        if (move_uploaded_file($file_input["tmp_name"], $target_file)) {
            return $nama_file_unik;
        }
    }
    return null;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// AKSI: Membuat Modul Baru
if ($action == 'create' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $id_matkul = $_POST['id_matkul'];
    $judul_modul = $_POST['judul_modul'];
    $deskripsi_modul = $_POST['deskripsi_modul'];
    
    $nama_file_db = uploadFile($_FILES['file_materi'], $target_dir);

    $sql = "INSERT INTO modul (id_matkul, judul_modul, deskripsi_modul, file_materi) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_matkul, $judul_modul, $deskripsi_modul, $nama_file_db]);

    header("Location: kelola_modul.php?status=sukses_tambah");
    exit;
}

// AKSI: Mengupdate Modul
if ($action == 'update' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $id_modul = $_POST['id'];
    $id_matkul = $_POST['id_matkul'];
    $judul_modul = $_POST['judul_modul'];
    $deskripsi_modul = $_POST['deskripsi_modul'];
    $file_lama = $_POST['file_lama'];

    $nama_file_db = $file_lama;

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $file_baru = uploadFile($_FILES['file_materi'], $target_dir);
        if ($file_baru) {
            $nama_file_db = $file_baru;
            if (!empty($file_lama) && file_exists($target_dir . $file_lama)) {
                unlink($target_dir . $file_lama);
            }
        }
    }

    $sql = "UPDATE modul SET id_matkul = ?, judul_modul = ?, deskripsi_modul = ?, file_materi = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_matkul, $judul_modul, $deskripsi_modul, $nama_file_db, $id_modul]);
    
    header("Location: kelola_modul.php?status=sukses_update");
    exit;
}


// Jika tidak ada aksi yang cocok, redirect
header('Location: kelola_modul.php');
exit;
?>