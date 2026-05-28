<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="mx-auto max-w-5xl">
    
    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pusat Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau semua permintaan, pengajuan, dan pemberitahuan sistem.</p>
        </div>
        <button class="bg-primary hover:bg-primary-hover text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca
        </button>
    </div>

    <!-- Filter & Content Area -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        
        <!-- Tabs / Filters -->
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap gap-2 p-3 md:p-4" aria-label="Tabs">
                <button type="button" class="notif-filter active-filter px-4 py-2 text-sm font-medium rounded-lg text-primary bg-primary/10" data-filter="all">
                    Semua <span class="ml-1 text-xs bg-primary text-white rounded-full px-2 py-0.5"><?= count($notifikasi) ?></span>
                </button>
                <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="gudang">
                    Gudang
                </button>
                <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="purchasing">
                    Purchasing
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
                        $filterCat = in_array($catLower, ['gudang', 'purchasing']) ? $catLower : 'sistem';
                    ?>
                    <div class="notif-item p-4 md:p-5 flex gap-4 hover:bg-gray-50 transition-colors <?= $bgClass ?>" data-category="<?= $filterCat ?>">
                        
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
                                    <div class="hs-dropdown relative inline-flex">
                                        <button id="hs-dropdown-notif-<?= $notif['id'] ?>" type="button" class="hs-dropdown-toggle size-6 flex justify-center items-center text-sm font-semibold rounded-md border border-transparent text-gray-500 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] hs-dropdown-open:opacity-100 opacity-0 hidden min-w-32 bg-white shadow-md border border-gray-200 rounded-lg p-1 space-y-0.5 mt-2 z-10" aria-labelledby="hs-dropdown-notif-<?= $notif['id'] ?>">
                                            <a class="flex items-center gap-x-2.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100" href="#">
                                                <i class="fa-solid fa-check w-4 text-gray-400"></i> Tandai Dibaca
                                            </a>
                                            <a class="flex items-center gap-x-2.5 py-2 px-3 rounded-lg text-sm text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50" href="#">
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
                                <?php if (in_array(strtolower($notif['kategori']), ['gudang', 'purchasing'])): ?>
                                    <a href="#" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1 transition-colors">
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filters = document.querySelectorAll('.notif-filter');
        const items = document.querySelectorAll('.notif-item');
        
        filters.forEach(btn => {
            btn.addEventListener('click', () => {
                // Reset active styles
                filters.forEach(f => {
                    f.classList.remove('active-filter', 'text-primary', 'bg-primary/10');
                    f.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
                    
                    const badge = f.querySelector('span');
                    if(badge) {
                        // Store the pure text for later reference
                        const textOnly = f.textContent.replace(/[0-9]/g, '').trim();
                        f.innerHTML = textOnly;
                    }
                });
                
                // Set active style
                btn.classList.add('active-filter', 'text-primary', 'bg-primary/10');
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
                btn.innerHTML = `${currentText} <span class="ml-1 text-xs bg-primary text-white rounded-full px-2 py-0.5">${count}</span>`;
            });
        });
    });
</script>
<?= $this->endSection() ?>
