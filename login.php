<?php
session_start();
require_once 'config.php'; // Pastikan Anda memiliki file koneksi ini

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'asisten') {
        header("Location: asisten/dashboard.php");
    } elseif ($_SESSION['role'] == 'mahasiswa') {
        header("Location: mahasiswa/dashboard.php");
    }
    exit();
}

$message = '';
$message_type = 'error'; // defaultnya error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'asisten') {
                    header("Location: asisten/dashboard.php");
                } elseif ($user['role'] == 'mahasiswa') {
                    header("Location: mahasiswa/dashboard.php");
                }
                exit();
            } else {
                $message = "Password yang Anda masukkan salah.";
            }
        } else {
            $message = "Akun dengan email tersebut tidak ditemukan.";
        }
        $stmt->close();
    }
}

// Cek status registrasi berhasil
if (isset($_GET['status']) && $_GET['status'] == 'registered') {
    $message = 'Registrasi berhasil! Silakan login.';
    $message_type = 'success';
}

// $conn->close(); // Sebaiknya ditutup di akhir skrip
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen">

    <div class="container my-10 p-8 bg-white rounded-2xl shadow-xl max-w-md w-full border-t-4 border-purple-400">
        <h2 class="text-3xl font-bold text-purple-800 mb-2 text-center">Login SIMPRAK</h2>
        <p class="text-center text-gray-500 mb-6">Masuk untuk melanjutkan</p>
        
        <?php if (!empty($message)): ?>
            <?php if ($message_type == 'success'): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <?php else: ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <?php endif; ?>
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" class="block w-full px-3 py-2 text-base border-2 border-purple-200 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent rounded-md shadow-sm" required>
            </div>
            
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg shadow-md transition-transform hover:scale-105">
                Login
            </button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Belum punya akun? 
                <a href="register.php" class="font-medium text-pink-600 hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>
</body>
</html>