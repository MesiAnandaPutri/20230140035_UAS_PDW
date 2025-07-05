<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen">
    <div class="p-8 bg-white rounded-2xl shadow-xl max-w-md w-full border-t-4 border-pink-400">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Form Tambah Akun</h1>
        <form action="proses_akun.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="space-y-6">
                <div><label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label><input type="text" name="nama" id="nama" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required></div>
                <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" id="email" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required></div>
                <div><label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label><input type="password" name="password" id="password" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required></div>
                <div><label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label><select name="role" id="role" class="block w-full px-3 py-2 border-2 border-purple-200 rounded-md" required><option value="mahasiswa">Mahasiswa</option><option value="asisten">Asisten</option></select></div>
            </div>
            <div class="flex items-center gap-4 mt-8 pt-6 border-t"><button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-5 rounded-lg">Simpan</button><a href="kelola_akun.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-5 rounded-lg">Batal</a></div>
        </form>
    </div>
</body>
</html>