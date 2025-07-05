<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit;
}

$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

$id = $_GET['id'] ?? null;
if ($id) {
    // Hapus dulu semua modul dan pendaftaran terkait (karena ada FOREIGN KEY)
    // Ini penting agar tidak terjadi error.
    $stmt_modul = $pdo->prepare("DELETE FROM modul WHERE id_matkul = ?");
    $stmt_modul->execute([$id]);

    $stmt_pendaftaran = $pdo->prepare("DELETE FROM pendaftaran WHERE id_matkul = ?");
    $stmt_pendaftaran->execute([$id]);
    
    // Baru hapus mata praktikumnya
    $stmt = $pdo->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: kelola_praktikum.php');
exit;
?>