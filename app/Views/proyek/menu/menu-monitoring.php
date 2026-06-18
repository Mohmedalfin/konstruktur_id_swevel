<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-[85rem] mx-auto py-2">
    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Total Permintaan</p>
                    <h3 id="stat-total" class="text-2xl font-black text-slate-800 leading-none">-</h3>
                </div>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Menunggu</p>
                    <h3 id="stat-pending" class="text-2xl font-black text-slate-800 leading-none">-</h3>
                </div>
            </div>
        </div>

        <!-- Diproses -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-cog text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Diproses</p>
                    <h3 id="stat-proses" class="text-2xl font-black text-slate-800 leading-none">-</h3>
                </div>
            </div>
        </div>

        <!-- Terkirim -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Terkirim</p>
                    <h3 id="stat-kirim" class="text-2xl font-black text-slate-800 leading-none">-</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIONS & FILTERS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <input type="month" id="filter-month" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:border-blue-500 cursor-pointer" value="<?= date('Y-m') ?>">
            </div>
            <div class="hidden sm:block w-px h-6 bg-slate-200"></div>
            <div class="flex flex-wrap items-center gap-2" id="status-filters">
                <button data-status="all" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800">
                    Semua
                </button>
            <button data-status="pending" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                Menunggu
            </button>
            <button data-status="disetujui" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                Diproses
            </button>
            <button data-status="selesai" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                Terkirim
            </button>
            <button data-status="ditolak" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                Ditolak
            </button>
        </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 mt-3 sm:mt-0">
            <a href="<?= base_url('permintaan/deviasi') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-bold shadow-sm transition-all focus:outline-none cursor-pointer">
                <i class="fas fa-chart-line text-[10px]"></i>
                <span>Laporan Deviasi</span>
            </a>
            <!-- Create Request Button -->
            <button type="button" data-hs-overlay="#modal-buat-permintaan" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-all focus:outline-none cursor-pointer">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Buat Permintaan Baru</span>
            </button>
        </div>
    </div>

    <!-- HISTORY CARDS CONTAINER -->
    <div id="history-container" class="space-y-4">
        <!-- Will be rendered dynamically via JS -->
        <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
            <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
            <p class="text-sm font-semibold">Memuat riwayat permintaan...</p>
        </div>
    </div>
</div>

<!-- DETAIL AJAX MODAL -->
<?php echo view('partials/modal-detail-permintaan'); ?>

<!-- CREATE AJAX MODAL -->
<?php echo view('partials/modal-buat-permintaan', ['projects' => $projects]); ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.PERMINTAAN_INIT = {
        userRole: <?= json_encode($userRole) ?>,
        baseUrl: <?= json_encode(base_url()) ?>,
        projects: <?= json_encode($projects) ?>,
        openCreateModal: <?= session()->getFlashdata('open_create_modal') ? 'true' : 'false' ?>
    };
</script>
<script type="module" src="<?= base_url('js/permintaan/index.js') ?>"></script>
<?= $this->endSection() ?>
