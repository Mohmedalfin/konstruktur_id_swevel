<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Tidak Valid - Kontraktor.id</title>
    
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at center, #243565 0%, #162345 100%);
        }
        .premium-card {
            background: rgba(254, 253, 248, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        }
    </style>
</head>
<body class="w-screen h-screen flex items-center justify-center overflow-hidden p-3 sm:p-6 relative">

    <div class="absolute inset-0 opacity-15 mix-blend-overlay pointer-events-none" 
         style="background-image: url('<?= base_url('assets/images/BackgroundLogin.png') ?>'); background-size: cover; background-position: center;"></div>
    
    <main class="w-full max-w-[440px] premium-card rounded-2xl overflow-hidden z-10 p-6 sm:p-8 text-center flex flex-col justify-between max-h-[96vh] transition-all duration-300">
        
        <div class="space-y-4 my-auto">
            <!-- Icon Error (Compact) -->
            <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fas fa-link-slash text-xl"></i>
            </div>

            <h1 class="text-base font-extrabold text-slate-800 tracking-tight">Undangan Tidak Valid / Kedaluwarsa</h1>
            
            <p class="text-[10px] text-slate-500 leading-relaxed px-1">
                Maaf, tautan undangan yang Anda gunakan sudah tidak berlaku, kedaluwarsa (lebih dari 48 jam), atau mungkin sudah pernah digunakan sebelumnya untuk mendaftar.
            </p>

            <!-- Instruction Card (Compact) -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-left">
                <h4 class="text-[10px] font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-slate-400"></i>
                    Apa yang harus dilakukan?
                </h4>
                <p class="text-[9px] text-slate-500 mt-1 leading-relaxed">
                    Silakan hubungi administrator atau pemilik proyek yang mengundang Anda untuk **mengirimkan ulang tautan undangan baru** melalui sistem kelola akun tim di dashboard mereka.
                </p>
            </div>

            <!-- Back Button (Compact) -->
            <a href="<?= base_url('/') ?>" 
               class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-lg border border-slate-200 bg-white text-[10px] font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm cursor-pointer">
                <i class="fas fa-house text-[10px]"></i>
                Kembali ke Beranda
            </a>
        </div>

        <p class="text-center text-[8px] text-slate-400 mt-5">Manajemen Konstruksi Modern © <?= date('Y') ?> Kontraktor.id</p>

    </main>
</body>
</html>
