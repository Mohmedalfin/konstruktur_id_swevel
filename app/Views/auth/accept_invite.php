<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung Tim - Kontraktor.id</title>
    
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
        .btn-premium {
            background: linear-gradient(90deg, #FBBF24 0%, #D97706 100%);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-premium:hover {
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="w-screen h-screen flex items-center justify-center overflow-hidden p-3 sm:p-6 relative">

    <div class="absolute inset-0 opacity-15 mix-blend-overlay pointer-events-none" 
         style="background-image: url('<?= base_url('assets/images/BackgroundLogin.png') ?>'); background-size: cover; background-position: center;"></div>
    
    <main class="w-full max-w-[500px] premium-card rounded-2xl overflow-hidden z-10 p-5 sm:p-8 flex flex-col justify-between max-h-[96vh] transition-all duration-300">
        
        <div class="space-y-4">
            <!-- Header Section (Compact) -->
            <div class="text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo" class="h-7 w-auto object-contain">
                    <span class="text-[#162345] text-lg font-extrabold tracking-wide uppercase">Kontraktor.id</span>
                </div>
                
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">Undangan Bergabung Tim</h1>
                <p class="text-[10px] text-slate-500 mt-0.5">
                    Diundang oleh <strong class="text-slate-700"><?= esc($admin->nama_pengguna ?? 'Admin') ?></strong> 
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[9px] uppercase border border-slate-200"><?= esc($admin->perusahaan ?? 'Kontraktor.id') ?></span>
                </p>
            </div>

            <!-- Info Badge (Ultra Compact) -->
            <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <i class="fas fa-user-shield text-xs"></i>
                </div>
                <div class="leading-snug">
                    <h4 class="text-[10px] font-bold text-amber-800">Akses Tim Ditetapkan:</h4>
                    <p class="text-[9px] text-amber-700">
                        Hak akses sebagai staff <span class="font-extrabold text-amber-900 underline"><?= esc(ucfirst($invitation->kategori_akun)) ?></span> pada proyek dan manajemen data.
                    </p>
                </div>
            </div>

            <!-- Form Setup -->
            <form id="form-accept-invite" class="space-y-3">
                <input type="hidden" name="token" value="<?= esc($token) ?>">

                <!-- Email (Read-Only) -->
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alamat Email (Terkunci)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400">
                            <i class="fas fa-envelope text-[10px]"></i>
                        </span>
                        <input type="email" value="<?= esc($invitation->email) ?>" disabled
                               class="w-full pl-8 pr-4 py-1.5 rounded-lg border border-slate-200 bg-slate-100/70 text-[10px] text-slate-500 font-semibold cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <!-- 2 Column Grid for Nama and Username to prevent vertical scroll -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_pengguna" class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="fas fa-user text-[10px]"></i>
                            </span>
                            <input type="text" id="nama_pengguna" name="nama_pengguna" required
                                   class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 text-[10px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                   placeholder="Nama lengkap">
                        </div>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Username <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="fas fa-at text-[10px]"></i>
                            </span>
                            <input type="text" id="username" name="username" required
                                   class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 text-[10px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                   placeholder="Username">
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400 group-focus-within:text-amber-500 transition-colors">
                            <i class="fas fa-lock text-[10px]"></i>
                        </span>
                        <input type="password" id="password" name="password" required minlength="6"
                               class="w-full pl-8 pr-10 py-1.5 rounded-lg border border-slate-200 text-[10px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                               placeholder="Minimal 6 karakter">
                        
                        <button type="button" id="btn-toggle-pwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-submit" 
                        class="w-full btn-premium text-white text-[11px] font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 mt-4 cursor-pointer">
                    <i class="fas fa-check-circle text-[12px]"></i>
                    <span>Aktifkan Akun Staff</span>
                </button>
            </form>
        </div>

        <p class="text-center text-[8px] text-slate-400 mt-5">Manajemen Konstruksi Modern © <?= date('Y') ?> Kontraktor.id</p>

    </main>

    <!-- Notification Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"></div>

    <script>
        // Toggle password visibility
        const pwdInput = document.getElementById('password');
        const toggleBtn = document.getElementById('btn-toggle-pwd');
        toggleBtn.addEventListener('click', () => {
            const isPwd = pwdInput.type === 'password';
            pwdInput.type = isPwd ? 'text' : 'password';
            toggleBtn.querySelector('i').className = isPwd ? 'fas fa-eye-slash text-xs' : 'fas fa-eye text-xs';
        });

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `p-3 rounded-xl border shadow-lg flex items-center gap-2.5 transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto ${
                type === 'success' 
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800' 
                    : 'bg-rose-50 border-rose-200 text-rose-800'
            }`;
            
            const icon = type === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-rose-500';
            toast.innerHTML = `
                <i class="fas ${icon} text-sm"></i>
                <p class="text-[10px] font-semibold">${message}</p>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 50);

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Form Submit handler
        const form = document.getElementById('form-accept-invite');
        const submitBtn = document.getElementById('btn-submit');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-[12px]"></i> <span>Memproses...</span>';

            const formData = new FormData(form);

            try {
                const response = await fetch('<?= base_url("accept-invite/submit") ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    
                    // Ganti isi card <main> dengan tampilan sukses premium & sederhana (Scroll-free)
                    const mainCard = document.querySelector('main');
                    mainCard.className = "w-full max-w-[450px] premium-card rounded-2xl overflow-hidden z-10 p-6 sm:p-8 text-center flex flex-col justify-between min-h-[350px] transition-all duration-300";
                    mainCard.innerHTML = `
                        <div class="text-center py-4 my-auto space-y-4">
                            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-500 flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <i class="fas fa-circle-check text-2xl"></i>
                            </div>
                            <h1 class="text-base font-extrabold text-slate-800 tracking-tight">Aktivasi Akun Berhasil!</h1>
                            <p class="text-[10px] text-slate-500 leading-relaxed px-2">
                                Akun staf Anda dengan email <strong class="text-slate-700">${result.email}</strong> dan akses <strong class="text-slate-700">${result.role}</strong> telah berhasil aktif.
                            </p>
                            
                            <!-- Premium Information Box -->
                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 text-left">
                                <h4 class="text-[10px] font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-slate-400"></i>
                                    Informasi Sistem
                                </h4>
                                <p class="text-[9px] text-slate-500 mt-1 leading-relaxed">
                                    Sistem khusus untuk akses <strong>${result.role}</strong> saat ini sedang dipersiapkan oleh tim pengembang. Anda dapat menutup tab halaman ini sekarang dengan aman.
                                </p>
                            </div>

                            <!-- Action button to Home -->
                            <a href="<?= base_url('/') ?>" 
                               class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-lg border border-slate-200 bg-white text-[10px] font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm cursor-pointer">
                                <i class="fas fa-house text-[10px]"></i>
                                Kembali ke Beranda
                            </a>
                        </div>
                        <p class="text-center text-[8px] text-slate-400 mt-4">Manajemen Konstruksi Modern © <?= date('Y') ?> Kontraktor.id</p>
                    `;
                } else {
                    showToast(result.message || 'Gagal mengaktifkan akun', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle text-[12px]"></i> <span>Aktifkan Akun Staff</span>';
                }
            } catch (err) {
                console.error(err);
                showToast('Koneksi server gagal atau terputus.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle text-[12px]"></i> <span>Aktifkan Akun Staff</span>';
            }
        });
    </script>
</body>
</html>
