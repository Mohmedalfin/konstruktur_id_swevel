<?= $this->extend('gudang/layouts/main') ?>
<?= $this->section('content') ?>

<div class="mx-auto max-w-5xl">    
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pusat Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau semua permintaan, pengajuan, dan pemberitahuan sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="btn-mark-all-read" class="bg-primary hover:bg-primary-hover text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca
            </button>
            <button id="btn-clear-all" class="border border-gray-200 bg-white hover:bg-red-50 hover:border-red-200 hover:text-red-600 text-gray-600 text-sm font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2">
                <i class="fa-regular fa-trash-can"></i> Hapus Semua
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap gap-2 p-3 md:p-4" aria-label="Tabs">
                <button type="button" class="notif-filter active-filter px-4 py-2 text-sm font-medium rounded-lg text-primary bg-primary/10" data-filter="all">
                    Semua <span id="count-all" class="ml-1 text-xs bg-primary text-white rounded-full px-2 py-0.5">0</span>
                </button>
                <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="gudang">
                    Gudang <span id="count-gudang" class="ml-1 text-xs bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 hidden">0</span>
                </button>
                <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="purchasing">
                    Purchasing <span id="count-purchasing" class="ml-1 text-xs bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 hidden">0</span>
                </button>
                <button type="button" class="notif-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg" data-filter="sistem">
                    Sistem <span id="count-sistem" class="ml-1 text-xs bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 hidden">0</span>
                </button>
            </nav>
        </div>

        <div class="divide-y divide-gray-100" id="notif-list">
            <!-- Dynamic content loaded via JS -->
            <div class="text-center text-gray-500 py-16">
                <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-3 text-gray-300"></i>
                <p>Memuat notifikasi...</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/notifikasi/index.js') ?>"></script>
<?= $this->endSection() ?>
