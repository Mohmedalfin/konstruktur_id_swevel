<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pusat Notifikasi Purchasing') ?></title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { background-color: #f3f4f6; }
        .nav-item {
            color: #d1d5db;
            font-size: 13px;
            font-weight: 600;
            padding: 0 24px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .nav-item:hover { color: #ffffff; }
        .nav-item-active {
            color: #111827;
            font-size: 13px;
            font-weight: 700;
            padding: 0 32px;
            display: flex;
            align-items: center;
            background-color: #ffffff;
        }
    </style>
</head>

<body class="font-sans antialiased text-sm text-gray-800">

    <!-- Top Navigation & Header Container -->
    <div class="bg-[#111827] w-full shadow-md">
        <!-- Navbar -->
        <?= view('purchasing/partials/navbar', ['activeNav' => 'notification']) ?>

        <!-- Title -->
        <div class="py-12 flex justify-center items-center relative overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
            <div class="absolute inset-0 bg-[#111827]/80"></div>
            <h1 class="relative z-10 text-white text-4xl font-bold tracking-widest uppercase">NOTIFIKASI</h1>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-12">
        
        <!-- Header Page -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pusat Notifikasi Purchasing</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau semua pengajuan, persetujuan, dan pemberitahuan sistem purchasing.</p>
            </div>
            <button class="bg-[#3b82f6] hover:bg-[#2563eb] text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca
            </button>
        </div>

        <!-- Filter & Content Area -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            
            <!-- Tabs / Filters -->
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap gap-2 p-3 md:p-4" aria-label="Tabs">
                    <button type="button" class="notif-filter active-filter px-4 py-2 text-sm font-medium rounded-lg text-[#3b82f6] bg-[#3b82f6]/10" data-filter="all">
                        Semua <span class="ml-1 text-xs bg-[#3b82f6] text-white rounded-full px-2 py-0.5"><?= count($notifikasi ?? []) ?></span>
                    </button>
                    <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="purchase request">
                        Purchase Request
                    </button>
                    <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="purchase order">
                        Purchase Order
                    </button>
                    <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="gudang">
                        Gudang
                    </button>
                    <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="sistem">
                        Sistem
                    </button>
                </nav>
            </div>

            <!-- Notification List -->
            <div class="divide-y divide-gray-100" id="notif-list">
                <?php if (empty($notifikasi)): ?>
                    <div class="text-center text-gray-500 py-16">
                        <i class="fa-regular fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                        <p>Tidak ada notifikasi saat ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifikasi as $notif): ?>
                        <?php 
                            $bgClass = $notif['is_read'] ? 'bg-white' : 'bg-'.$notif['warna'].'-50/30'; 
                            $catLower = strtolower($notif['kategori']);
                        ?>
                        <div class="notif-item p-4 md:p-5 flex gap-4 hover:bg-gray-50 transition-colors <?= $bgClass ?>" data-category="<?= $catLower ?>">
                            
                            <!-- Icon -->
                            <div class="shrink-0 mt-1">
                                <div class="w-10 h-10 rounded-full bg-<?= $notif['warna'] ?>-100 text-<?= $notif['warna'] ?>-600 flex items-center justify-center">
                                    <i class="<?= $notif['ikon'] ?>"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-1 mb-1">
                                    <h3 class="text-sm md:text-base font-bold text-gray-800 flex items-center gap-2">
                                        <?= esc($notif['judul']) ?>
                                        <?php if (!$notif['is_read']): ?>
                                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full" title="Belum dibaca"></span>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] md:text-xs text-gray-400 font-medium whitespace-nowrap">
                                            <i class="fa-regular fa-clock mr-1"></i> <?= esc($notif['waktu']) ?>
                                        </span>
                                        <!-- Action Menu -->
                                        <div class="relative group inline-block">
                                            <button type="button" class="size-6 flex justify-center items-center text-sm font-semibold rounded-md border border-transparent text-gray-500 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="absolute right-0 mt-2 min-w-32 bg-white shadow-md border border-gray-200 rounded-lg p-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                                <a class="flex items-center gap-x-2.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100" href="#">
                                                    <i class="fa-solid fa-check w-4 text-gray-400"></i> Tandai Dibaca
                                                </a>
                                                <a class="flex items-center gap-x-2.5 py-2 px-3 rounded-lg text-sm text-red-600 hover:bg-red-50" href="#">
                                                    <i class="fa-regular fa-trash-can w-4"></i> Hapus
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">
                                    <?= esc($notif['pesan']) ?>
                                </p>
                                
                                <div class="flex items-center gap-4 mt-3">
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-<?= $notif['warna'] ?>-600">
                                        <?= esc($notif['kategori']) ?>
                                    </span>
                                    <?php if (in_array(strtolower($notif['kategori']), ['purchase request', 'purchase order', 'gudang'])): ?>
                                        <a href="#" class="text-xs font-semibold text-[#3b82f6] hover:text-[#2563eb] flex items-center gap-1 transition-colors">
                                            Lihat Detail <i class="fa-solid fa-arrow-right-long"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filters = document.querySelectorAll('.notif-filter');
            const items = document.querySelectorAll('.notif-item');
            
            filters.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Reset active styles
                    filters.forEach(f => {
                        f.classList.remove('active-filter', 'text-[#3b82f6]', 'bg-[#3b82f6]/10');
                        f.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
                        
                        const badge = f.querySelector('span');
                        if(badge) {
                            // Store the pure text for later reference
                            const textOnly = f.textContent.replace(/[0-9]/g, '').trim();
                            f.innerHTML = textOnly;
                        }
                    });
                    
                    // Set active style
                    btn.classList.add('active-filter', 'text-[#3b82f6]', 'bg-[#3b82f6]/10');
                    btn.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
                    
                    const filter = btn.getAttribute('data-filter');
                    let count = 0;
                    
                    items.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.style.display = 'flex';
                            count++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Add count badge to active button
                    const currentText = btn.textContent.trim();
                    btn.innerHTML = `${currentText} <span class="ml-1 text-xs bg-[#3b82f6] text-white rounded-full px-2 py-0.5">${count}</span>`;
                });
            });
        });
    </script>
</body>
</html>
