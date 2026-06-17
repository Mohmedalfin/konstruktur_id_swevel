<?= $this->extend('gudang/layouts/main') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-[85rem] mx-auto py-2">
    <!-- CRITICAL STOCK ALERT PANEL -->
    <div id="alert-panel-container" class="hidden mb-6">
        <div class="bg-slate-800 rounded-xl p-4 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between border border-slate-700 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/30">
                    <i class="fas fa-magic"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        Smart Auto-Fill Tersedia
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5" id="alert-message">
                        Terdapat <span class="font-bold text-white" id="kritis-count">0</span> item barang yang membutuhkan restock (berada di bawah batas minimum).
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 self-end sm:self-auto shrink-0">
                <button id="btn-smart-procurement" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-sm transition-all active:scale-95 whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                    Buat Pengajuan
                </button>
                <button type="button" class="text-slate-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-slate-700 focus:outline-none" onclick="document.getElementById('alert-panel-container').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6" id="stats-container">
        <!-- Will be rendered via JS DashboardStats -->
    </div>

    <!-- ACTIONS & FILTERS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <!-- Actions -->
        <div class="flex gap-2">
            <button id="btn-create-manual" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                <i class="fas fa-plus"></i>
                <span>Pengajuan Manual</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <input type="month" id="filter-month" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:border-indigo-500 cursor-pointer" value="<?= date('Y-m') ?>">
            </div>
            <div class="hidden sm:block w-px h-6 bg-slate-200"></div>
            <div class="flex flex-wrap items-center gap-2" id="status-filters">
                <button data-status="all" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800">
                    Semua
                </button>
                <button data-status="pending" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                    Menunggu (Draft)
                </button>
                <button data-status="approved" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                    Disetujui
                </button>
                <button data-status="ordered" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                    Diproses PO
                </button>
                <button data-status="completed" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                    Selesai
                </button>
                <button data-status="rejected" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">
                    Ditolak
                </button>
            </div>
        </div>
    </div>

    <!-- DATATABLE CONTAINER -->
    <div id="table-container" class="space-y-4">
        <!-- Will be rendered dynamically via JS -->
        <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
            <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
            <p class="text-sm font-semibold">Memuat riwayat pengajuan...</p>
        </div>
    </div>
</div>

<!-- AJAX MODALS -->
<?php echo view('gudang/partials/modal-create-pengadaan'); ?>
<?php echo view('gudang/partials/modal-detail-pengadaan'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.PENGADAAN_INIT = {
        userRole: <?= json_encode($userRole ?? 'Gudang') ?>,
        baseUrl: <?= json_encode(base_url()) ?>
    };
</script>
<script type="module" src="<?= base_url('js/gudang-pengadaan/index.js') ?>"></script>
<?= $this->endSection() ?>
