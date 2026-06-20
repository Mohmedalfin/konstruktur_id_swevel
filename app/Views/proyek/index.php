<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar') ?>
<header class="bg-primary text-white py-5">
    <h1 class="text-center text-4xl font-bold">DAFTAR PROYEK</h1>
</header>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Button Proyek Baru -->
<div class="mb-4">
    <a class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-white shadow-md hover:bg-primary/90"
        href="<?= base_url('proyek/create') ?>">
        <i class="fa-solid fa-circle-plus"></i>
        <span class="font-semibold">Proyek Baru</span>
    </a>
</div>

<!-- Panel Filter -->
<div class="overflow-visible rounded-xl bg-white shadow-md">

    <!-- Header -->
    <div class="flex items-center gap-2 bg-primary px-4 py-2.5 text-white rounded-t-xl">
        <i class="fa-solid fa-filter text-xs"></i>
        <span class="text-sm font-semibold">Tampilkan Berdasarkan</span>
    </div>

    <div>
        <?php
        $uniqueYears = [];
        if (!empty($cards)) {
            foreach ($cards as $card) {
                if (!empty($card['tgl'])) {
                    $yr = date('Y', strtotime($card['tgl']));
                    $uniqueYears[$yr] = $yr;
                }
            }
        }
        rsort($uniqueYears);
        ?>
        <div class="grid grid-cols-2 gap-3 p-3 md:grid-cols-3 md:gap-4 md:p-4">

            <!-- Nama Proyek -->
            <div class="col-span-2 md:col-span-1 relative">
                <label class="mb-1 block text-xs md:text-sm font-semibold text-text-primary">Nama Proyek</label>
                <div class="relative">
                    <input type="text" id="filter-nama" placeholder="Masukkan Nama Proyek"
                        class="w-full rounded-md border border-gray-300 px-2.5 py-1.5 pr-8 md:px-3 md:py-2 md:pr-8 text-xs md:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    <button type="button" id="clear-nama"
                        class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none z-10 p-1">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Lokasi Proyek -->
            <div class="relative">
                <label class="mb-1 block text-xs md:text-sm font-semibold text-text-primary">Lokasi Proyek</label>
                <div class="relative">
                    <select id="filter-lokasi" data-hs-select='{
              "hasSearch": true,
              "searchPlaceholder": "Cari Kabupaten/Kota...",
              "searchClasses": "block w-full text-sm bg-white border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500",
              "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
              "placeholder": "Semua Lokasi",
              "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
              "toggleClasses": "relative py-1.5 ps-2.5 pe-8 md:py-2 md:ps-3 md:pe-9 flex w-full cursor-pointer bg-white border border-gray-300 rounded-md text-start text-xs md:text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500",
              "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-md shadow-xl overflow-hidden overflow-y-auto",
              "optionClasses": "hs-selected:bg-gray-100 py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-50",
              "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\" fill=\"currentColor\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div>",
              "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"size-4 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\" fill=\"currentColor\"><path fill-rule=\"evenodd\" d=\"M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z\" clip-rule=\"evenodd\"/></svg></div>"
            }' class="hidden">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= $city['id'] ?>"><?= esc($city['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!-- X Button -->
                    <button type="button" id="clear-lokasi"
                        class="hidden absolute right-10 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none z-10 p-1">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Tahun -->
            <div class="relative">
                <label class="mb-1 block text-xs md:text-sm font-semibold text-text-primary">Tahun</label>
                <div class="relative">
                    <select id="filter-tahun" data-hs-select='{
              "hasSearch": false,
              "placeholder": "Semua Tahun",
              "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
              "toggleClasses": "relative py-1.5 ps-2.5 pe-8 md:py-2 md:ps-3 md:pe-9 flex w-full cursor-pointer bg-white border border-gray-300 rounded-md text-start text-xs md:text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500",
              "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-md shadow-xl overflow-hidden overflow-y-auto",
              "optionClasses": "hs-selected:bg-gray-100 py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-50",
              "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\" fill=\"currentColor\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div>",
              "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"size-4 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\" fill=\"currentColor\"><path fill-rule=\"evenodd\" d=\"M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z\" clip-rule=\"evenodd\"/></svg></div>"
            }' class="hidden">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($uniqueYears as $yr): ?>
                            <option value="<?= $yr ?>"><?= $yr ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="clear-tahun"
                        class="hidden absolute right-10 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none z-10 p-1">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>


<!-- Grid Cards -->
<div class="mt-6">
    <?php if (empty($cards)): ?>
        <div class="text-center text-gray-500 py-10 w-full bg-white rounded-xl shadow-sm border border-gray-100">
            <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
            <p>Belum ada proyek yang ditambahkan.</p>
        </div>
    <?php else: ?>
        <div id="proyek-grid" class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3 xl:grid-cols-5">
            <?php foreach ($cards as $card): ?>
                <?= view('partials/card-proyek', ['card' => $card]) ?>
            <?php endforeach; ?>
        </div>
        <div id="empty-state"
            class="hidden text-center text-gray-500 py-10 w-full mt-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
            <p>Proyek tidak ditemukan.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->include('partials/modal-rekonsiliasi') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/@floating-ui/core@1.6.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.6.3"></script>
<script>
    window.baseUrl = '<?= base_url() ?>';
</script>
<script type="module" src="<?= base_url('js/menu-rekonsiliasi/app.js') ?>"></script>
<?= $this->endSection() ?>