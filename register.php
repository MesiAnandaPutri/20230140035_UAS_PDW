<?php
require_once 'config.php'; // Pastikan Anda memiliki file koneksi ini

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
    } else {
        // Cek duplikasi email
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            // Hash password dan simpan pengguna baru
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_insert = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);

            if ($stmt_insert->execute()) {
                header("Location: login.php?status=registered");
                exit();
            } else {
                $message = "Terjadi kesalahan. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
// $conn->close(); // Sebaiknya ditutup di akhir skrip jika tidak ada HTML lagi
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen">

    <div class="container my-10 p-8 bg-white rounded-2xl shadow-xl max-w-md w-full border-t-4 border-pink-400">
        <h2 class="text-3xl font-bold text-purple-800 mb-2 text-center">Buat Akun Baru</h2>
        <p class="text-center text-gray-500 mb-6">Selamat datang di SIMPRAK!</p>
        
        <?php if (!empty($message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form action="register.php" method="post" class="space-y-6">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Daftar Sebagai</label>
                <select id="role" name="role" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="asisten">Asisten</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 rounded-lg shadow-md transition-transform hover:scale-105">
                Daftar
            </button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Sudah punya akun? 
                <a href="login.php" class="font-medium text-purple-600 hover:underline">Login di sini</a>
            </p>
        </div>
    </div>
</body>
</html>