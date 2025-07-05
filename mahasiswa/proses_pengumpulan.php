<?php
session_start();

// Pastikan hanya mahasiswa yang login yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

// Pastikan data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $id_modul = $_POST['id_modul'];
    $id_matkul = $_POST['id_matkul']; // Untuk redirect kembali
    $id_mahasiswa = $_SESSION['user_id'];

    // Cek apakah file sudah diunggah tanpa error
    if (isset($_FILES["file_laporan"]) && $_FILES["file_laporan"]["error"] == 0) {
        
        $target_dir = "../uploads/laporan/"; // Path ke folder penyimpanan
        // Buat nama file unik untuk menghindari tumpang tindih
        $nama_file_unik = time() . '_' . $id_mahasiswa . '_' . basename($_FILES["file_laporan"]["name"]);
        $target_file = $target_dir . $nama_file_unik;

        // Pindahkan file dari lokasi temporary ke folder tujuan
        if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
            
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

            // Simpan informasi file ke database
            try {
                $sql = "INSERT INTO laporan (id_modul, id_mahasiswa, file_laporan) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_modul, $id_mahasiswa, $nama_file_unik]);
                
                // Jika berhasil, redirect kembali ke halaman detail dengan pesan sukses
                header("Location: detail_praktikum.php?id=" . $id_matkul . "&status=sukses_upload");
                exit;

            } catch (PDOException $e) {
                die("Gagal menyimpan data ke database: " . $e->getMessage());
            }

        } else {
            // Jika gagal memindahkan file
            header("Location: detail_praktikum.php?id=" . $id_matkul . "&status=gagal_upload");
            exit;
        }
    } else {
        // Jika tidak ada file yang diunggah atau ada error lain
        header("Location: detail_praktikum.php?id=" . $id_matkul . "&status=file_error");
        exit;
    }
} else {
    // Jika halaman diakses langsung tanpa metode POST, redirect
    header("Location: praktikum_saya.php");
    exit;
}
?>