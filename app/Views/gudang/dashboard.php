<?= $this->extend('gudang/layouts/main') ?>

<?= $this->section('content') ?>
<style>
    /* ── Design tokens ───────────────────────────── */
    :root {
        --db-card:      #ffffff;
        --db-primary:   #1a2e4a;
        --db-accent:    #f59e0b;
        --db-blue:      #3b82f6;
        --db-red:       #ef4444;
        --db-green:     #10b981;
        --db-text:      #1e293b;
        --db-muted:     #64748b;
        --db-border:    #e2e8f0;
        --db-radius:    12px;
        --db-shadow:    0 2px 12px rgba(0,0,0,.07);
    }

    /* ── Donut area ──────────────────────────────── */
    .donut-wrap { position: relative; width: 160px; height: 160px; flex-shrink: 0; margin: 0 auto; }
    .donut-center {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%,-50%);
        text-align: center;
        pointer-events: none;
    }
    .donut-center .dc-num  { font-size: 1.6rem; font-weight: 800; line-height: 1; color: var(--db-text); }
    .donut-center .dc-lbl  { font-size: .65rem; color: var(--db-muted); font-weight: 600; text-transform: uppercase; margin-top: 2px;}

    .legend-row { display: flex; align-items: center; gap: 7px; margin-bottom: 8px; font-size: .8rem; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
    .legend-num { margin-left: auto; font-weight: 700; color: var(--db-text); }

    /* Custom Scrollbar for activity list */
    .activity-list::-webkit-scrollbar { width: 4px; }
    .activity-list::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .activity-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .activity-list::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="w-full max-w-[85rem] mx-auto py-2">
    
    <!-- WELCOME BANNER -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-800" id="welcome-greeting">Halo, Memuat...</h2>
            <p class="text-sm text-slate-500 mt-1">Selamat datang di pusat kendali logistik dan inventaris gudang.</p>
        </div>
        <div class="flex flex-col items-end">
            <div class="text-sm font-semibold text-slate-800" id="current-date"></div>
            <div class="text-xs text-slate-500" id="current-time"></div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-blue-50 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-boxes text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Barang</p>
                    <h3 id="stat-total" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
                </div>
            </div>
        </div>

        <!-- Stok Kritis -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-red-50 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Kritis / Habis</p>
                    <h3 id="stat-kritis" class="text-xl font-bold text-red-600 mt-0.5">-</h3>
                </div>
            </div>
        </div>

        <!-- Permintaan Menunggu -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-amber-50 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clipboard-list text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Permintaan Pending</p>
                    <h3 id="stat-permintaan" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
                </div>
            </div>
        </div>

        <!-- Pengadaan Aktif -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-emerald-50 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengadaan Aktif</p>
                    <h3 id="stat-pengadaan" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- LEFT COLUMN: Charts & Alerts -->
        <div class="col-span-1 lg:col-span-2 space-y-6">
            
            <!-- Health Chart & Summary Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Doughnut Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <h3 class="text-sm font-bold text-slate-800 mb-6">Kesehatan Stok Keseluruhan</h3>
                    
                    <div id="health-chart-container" class="flex flex-col sm:flex-row items-center justify-center gap-8">
                        <!-- Chart will be injected here -->
                        <div class="text-center py-8 text-slate-400">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Alerts / Info -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl shadow-sm border border-slate-700 p-6 flex flex-col justify-center relative overflow-hidden text-white">
                    <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4">
                        <i class="fas fa-warehouse text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-2">Aksi Cepat</h3>
                        <p class="text-sm text-slate-300 mb-6 line-clamp-2">Gunakan menu di bawah ini untuk mengakses fitur utama gudang dengan cepat.</p>
                        
                        <div class="grid grid-cols-1 gap-3">
                            <a href="<?= base_url('gudang/permintaan') ?>" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 border border-white/5 transition-all text-sm font-semibold">
                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center"><i class="fas fa-check-double"></i></div>
                                Proses Permintaan
                            </a>
                            <a href="<?= base_url('gudang/pengadaan') ?>" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 border border-white/5 transition-all text-sm font-semibold">
                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center"><i class="fas fa-plus"></i></div>
                                Buat Pengadaan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kritis Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800">Daftar Barang Kritis & Kosong</h3>
                    <a href="<?= base_url('gudang/stok') ?>?filter=kritis" class="text-xs font-bold text-blue-600 hover:text-blue-700">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">Kode</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">Nama Barang</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-center">Aktual</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-center">Minimum</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kritis-table-body">
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                    <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                    <p class="text-xs font-semibold">Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Timeline / Activities -->
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 h-full flex flex-col">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800">Aktivitas Terbaru</h3>
                </div>
                <div class="p-5 flex-1 overflow-hidden">
                    <!-- Custom Scroll container -->
                    <div class="h-full max-h-[500px] overflow-y-auto activity-list pr-2 space-y-6" id="activity-timeline">
                        <div class="text-center py-8 text-slate-400">
                            <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                            <p class="text-xs font-semibold">Memuat riwayat...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<!-- Pass basic auth / role info if needed -->
<script>
    window.DASHBOARD_INIT = {
        userName: <?= json_encode(session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Pengguna') ?>,
        baseUrl: <?= json_encode(base_url()) ?>
    };
</script>

<!-- Entry Point for Dashboard -->
<script type="module" src="<?= base_url('js/gudang-dashboard/index.js') ?>"></script>
<?= $this->endSection() ?>
