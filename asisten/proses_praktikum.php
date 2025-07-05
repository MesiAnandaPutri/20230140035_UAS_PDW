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

$action = $_POST['action'] ?? '';

if ($action == 'create') {
    $sql = "INSERT INTO mata_praktikum (kode_mk, nama_mk, deskripsi) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['kode_mk'], $_POST['nama_mk'], $_POST['deskripsi']]);
}

if ($action == 'update') {
    $sql = "UPDATE mata_praktikum SET kode_mk = ?, nama_mk = ?, deskripsi = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['kode_mk'], $_POST['nama_mk'], $_POST['deskripsi'], $_POST['id']]);
}

header('Location: kelola_praktikum.php');
exit;
?>