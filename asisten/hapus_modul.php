<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit;
}

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

// Ambil ID dari URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: kelola_modul.php');
    exit;
}

// 1. Ambil nama file untuk dihapus dari server
$stmt = $pdo->prepare("SELECT file_materi FROM modul WHERE id = ?");
$stmt->execute([$id]);
$modul = $stmt->fetch(PDO::FETCH_ASSOC);

if ($modul && !empty($modul['file_materi'])) {
    $path_file = '../uploads/materi/' . $modul['file_materi'];
    if (file_exists($path_file)) {
        unlink($path_file); // Hapus file fisik
    }
}

// 2. Hapus record dari database
$stmt_delete = $pdo->prepare("DELETE FROM modul WHERE id = ?");
$stmt_delete->execute([$id]);

// Redirect kembali ke halaman utama
header("Location: kelola_modul.php?status=hapus_sukses");
exit;
?>