<?= $this->extend('gudang/layouts/main') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-[85rem] mx-auto py-2">
    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Permintaan</p>
                    <h3 id="stat-total" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
                </div>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu</p>
                    <h3 id="stat-pending" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
                </div>
            </div>
        </div>

        <!-- Diproses -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-cog text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Diproses</p>
                    <h3 id="stat-proses" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
                </div>
            </div>
        </div>

        <!-- Terkirim -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terkirim</p>
                    <h3 id="stat-kirim" class="text-xl font-bold text-slate-800 mt-0.5">-</h3>
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
<?php echo view('gudang/partials/modal-detail-permintaan'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.PERMINTAAN_INIT = {
        userRole: <?= json_encode($userRole ?? 'Gudang') ?>,
        baseUrl: <?= json_encode(base_url()) ?>
    };
</script>
<script type="module" src="<?= base_url('js/gudang-permintaan/index.js') ?>"></script>
<?= $this->endSection() ?>
