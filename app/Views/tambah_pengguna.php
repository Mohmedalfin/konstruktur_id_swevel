<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna - Estimator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 mb-10">
        <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white py-4 px-6">
                <h2 class="text-2xl font-semibold"><i class="fas fa-user-plus mr-2"></i> Tambah Pengguna Baru</h2>
                <p class="text-blue-100 text-sm">Input data akun untuk sistem Estimator Alpha</p>
            </div>

            <?php if (session()->getFlashdata('msg')) : ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-6" role="alert">
                    <p><?= session()->getFlashdata('msg') ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('registrasi/simpan') ?>" method="post" class="p-8">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-700 border-b pb-2">Informasi Akun</h3>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Username</label>
                            <input type="text" name="username" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Username unik" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Password</label>
                            <input type="password" name="password" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="••••••••" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Kategori Akun</label>
                            <input type="text" name="kategori_akun" maxlength="1" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Contoh: 1" required>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-700 border-b pb-2">Informasi Profil</h3>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_pengguna" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Nama Lengkap" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">No WhatsApp</label>
                            <input type="text" name="no_wa" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0812xxxx" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">ID Wilayah</label>
                            <input type="text" name="id_wilayah" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="7 Digit" required>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-bold text-gray-700">Profil / Keahlian</label>
                    <textarea name="profil" rows="3" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Jelaskan keahlian singkat..." required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Harga Min</label>
                        <input type="number" name="harga_min" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Harga Max</label>
                        <input type="number" name="harga_max" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Bisa Nego?</label>
                        <select name="nego" class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="Y">Ya</option>
                            <option value="T">Tidak</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="reset" class="px-6 py-2 mr-4 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-300">Reset</button>
                    <button type="submit" class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow-md transition duration-300">
                        <i class="fas fa-save mr-2"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
        <p class="text-center text-gray-500 text-xs mt-4">
            &copy; 2026 Estimator.id - Project Testing Mode
        </p>
    </div>

</body>
</html>