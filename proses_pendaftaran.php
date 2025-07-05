<?php
session_start();

// 1. Pastikan hanya mahasiswa yang sudah login yang bisa mendaftar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    // Jika bukan mahasiswa atau belum login, kembalikan ke halaman login
    header("Location: login.php");
    exit;
}

// 2. Validasi ID mata kuliah yang dikirim dari URL
if (!isset($_GET['id_matkul']) || !is_numeric($_GET['id_matkul'])) {
    // Jika tidak ada ID, kembalikan ke katalog
    header("Location: katalog_praktikum.php");
    exit;
}

$id_matkul = $_GET['id_matkul'];
$id_mahasiswa = $_SESSION['user_id'];

// KONEKSI DATABASE
$host = 'localhost';
$dbname = 'db_simprak'; // Sesuaikan dengan nama database Anda
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// 3. Masukkan data pendaftaran ke dalam tabel 'pendaftaran'
try {
    $sql = "INSERT INTO pendaftaran (id_mahasiswa, id_matkul) VALUES (:id_mahasiswa, :id_matkul)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_mahasiswa' => $id_mahasiswa,
        ':id_matkul' => $id_matkul
    ]);

    // 4. Jika berhasil, kembalikan ke katalog dengan pesan sukses
    header("Location: katalog_praktikum.php?status=sukses_daftar");
    exit;

} catch (PDOException $e) {
    // Tangani jika terjadi error, misalnya mahasiswa mencoba mendaftar dua kali.
    // Ini akan mencegah aplikasi crash jika ada UNIQUE constraint di database.
    die("Error saat mendaftar. Kemungkinan Anda sudah terdaftar di praktikum ini. Error: " . $e->getMessage());
}

?>