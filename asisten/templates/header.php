<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login atau bukan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
    <aside class="w-64 bg-purple-100 text-purple-800 flex flex-col z-10 shadow-lg">
        <div class="p-6 text-center border-b border-purple-200">
            <h3 class="text-xl font-bold">Panel Asisten</h3>
            <p class="text-sm text-purple-600 mt-1"><?php echo htmlspecialchars($_SESSION['nama'] ?? 'Asisten'); ?></p>
        </div>
        <nav class="flex-grow">
            <ul class="space-y-2 p-4">
                <?php 
                    // Style baru: Aksen BIRU untuk menu aktif
                    $activeClass = 'bg-blue-500 text-white shadow-md font-semibold';
                    $inactiveClass = 'text-purple-700 hover:bg-purple-200';
                ?>
                <li>
                    <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-lg transition-all duration-200">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_praktikum.php" class="<?php echo ($activePage == 'praktikum') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-lg transition-all duration-200">
                        <span>Manajemen Praktikum</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_modul.php" class="<?php echo ($activePage == 'modul') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-lg transition-all duration-200">
                        <span>Manajemen Modul</span>
                    </a>
                </li>
                <li>
                    <a href="laporan.php" class="<?php echo ($activePage == 'laporan') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-lg transition-all duration-200">
                        <span>Laporan Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_akun.php" class="<?php echo ($activePage == 'kelola') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-lg transition-all duration-200">
                        <span>Kelola Akun</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 border-t border-purple-200">
             <a href="../logout.php" class="flex items-center justify-center bg-pink-400 hover:bg-pink-500 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
        <header class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
        </header>