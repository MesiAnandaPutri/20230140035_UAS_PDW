<?php
session_start();
// Pastikan hanya asisten yang login yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit;
}

// Pastikan data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $id_laporan = $_POST['id_laporan'];
    $nilai = $_POST['nilai'];
    $feedback = $_POST['feedback'];

    // Validasi sederhana
    if (empty($id_laporan) || !is_numeric($nilai)) {
        // Jika data tidak valid, kembalikan
        header("Location: laporan.php?status=gagal");
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

    // Update data nilai dan feedback di database
    try {
        $sql = "UPDATE laporan SET nilai = ?, feedback = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nilai, $feedback, $id_laporan]);
        
        // Jika berhasil, redirect kembali ke halaman laporan masuk
        header("Location: laporan.php?status=nilai_sukses");
        exit;

    } catch (PDOException $e) {
        die("Gagal menyimpan nilai: " . $e->getMessage());
    }

} else {
    // Jika halaman diakses langsung, redirect
    header("Location: laporan.php");
    exit;
}
?>