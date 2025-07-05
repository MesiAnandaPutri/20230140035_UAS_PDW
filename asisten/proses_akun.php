<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') { exit('Akses ditolak.'); }
$host = 'localhost'; $dbname = 'db_simprak'; $user = 'root'; $pass = '';
try { $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass); } catch (PDOException $e) { die("Koneksi gagal: " . $e->getMessage()); }

$action = $_POST['action'] ?? '';

if ($action == 'create') {
    $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['nama'], $_POST['email'], $hashed_password, $_POST['role']]);
}

if ($action == 'update') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // Jika password diisi, update dengan password baru
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $email, $role, $hashed_password, $id]);
    } else {
        // Jika password kosong, update tanpa mengubah password
        $sql = "UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $email, $role, $id]);
    }
}

header('Location: kelola_akun.php');
exit;
?>