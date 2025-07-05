<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') { exit('Akses ditolak.'); }
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try { $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass); } catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

$id = $_GET['id'] ?? null;

// Keamanan: Pastikan admin tidak menghapus akunnya sendiri
if ($id && $id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: kelola_akun.php');
exit;
?>