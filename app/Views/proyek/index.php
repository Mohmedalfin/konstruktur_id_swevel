<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar') ?>
<header class="bg-primary text-white py-5">
  <h1 class="text-center text-4xl font-bold">DAFTAR PROYEK</h1>
</header>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Button Proyek Baru -->
<div class="mb-4">
  <a class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-white shadow-md hover:bg-primary/90" href="<?= base_url('proyek/create') ?>">
    <i class="fa-solid fa-circle-plus"></i>
    <span class="font-semibold">Proyek Baru</span>
  </a>
</div>

<!-- Panel Filter -->
<div class="overflow-visible rounded-xl bg-white shadow-md">
  <div class="flex items-center gap-2 bg-primary px-4 py-2 text-white rounded-t-xl">
    <i class="fa-solid fa-filter"></i>
    <span class="font-semibold">Tampilkan Berdasarkan</span>
  </div>

  <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-3">
    <!-- Nama Proyek -->
    <div>
      <label class="mb-1 block text-sm font-semibold text-text-primary">Nama Proyek</label>
      <input
        type="text"
        placeholder="Masukkan Nama Proyek"
        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
    </div>

    <!-- Lokasi Proyek (Preline Select) -->
    <div class="relative">
      <label class="mb-1 block text-sm font-semibold text-text-primary">Lokasi Proyek</label>

      <!-- Select (punyamu) -->
      <select id="hs-search-both-value-and-description" data-hs-select='{
        "hasSearch": true,
        "isSearchDirectMatch": false,
        "searchPlaceholder": "Search...",
        "searchClasses": "block w-full text-sm bg-white border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500",
        "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
        "placeholder": "Pilih Lokasi Proyek",
        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
        "toggleClasses": "relative py-2 ps-3 pe-9 flex w-full cursor-pointer bg-white border border-gray-300 rounded-md text-start text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500",
        "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden overflow-y-auto",
        "optionClasses": "hs-selected:bg-gray-100 py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-50 rounded-md",
        "optionTemplate": "<div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div><div class=\"hs-selected:font-semibold text-sm\" data-title></div><div class=\"text-xs text-gray-500\" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"size-4\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\" fill=\"currentColor\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"size-4 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\" fill=\"currentColor\"><path fill-rule=\"evenodd\" d=\"M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z\" clip-rule=\"evenodd\"/></svg></div>"
      }' class="hidden">
        <option value="">Choose</option>
        <option selected value="1" data-hs-select-option='{"icon":"<img class=\"shrink-0 size-5 rounded-full\" src=\"https://images.unsplash.com/photo-1659482633369-9fe69af50bfb?auto=format&fit=facearea&facepad=3&w=64&h=64&q=80\" />","description":"Kab. Sleman, DIY"}'>Sleman</option>
        <option value="2" data-hs-select-option='{"icon":"<img class=\"shrink-0 size-5 rounded-full\" src=\"https://images.unsplash.com/photo-1541101767792-f9b2b1c4f127?auto=format&fit=facearea&facepad=3&w=64&h=64&q=80\" />","description":"Kota Semarang, Jateng"}'>Semarang</option>
        <option value="3" data-hs-select-option='{"icon":"<img class=\"shrink-0 size-5 rounded-full\" src=\"https://images.unsplash.com/photo-1601935111741-ae98b2b230b0?auto=format&fit=facearea&facepad=2.5&w=64&h=64&q=80\" />","description":"Kota Surabaya, Jatim"}'>Surabaya</option>
      </select>
      <!-- End Select -->
    </div>

    <!-- Tahun -->
    <div>
      <label class="mb-1 block text-sm font-semibold text-text-primary">Tahun</label>
      <input
        type="text"
        placeholder="Masukkan Tahun Proyek"
        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
    </div>
  </div>
</div>

<!-- Grid Cards -->
<div class="mt-6">
  <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <?php
    $cards = [
        // Cards with existing RAB data → readonly mode
        ['title' => 'Pembangunan Gedung Klinik Pratama',  'lokasi' => 'Kab. Sleman, DIY',       'nilai' => 'Rp 3.250.000.000', 'pct' => '-2,1%', 'tgl' => '2026-02-12', 'href' => base_url('menu-rap?id=1')],
        ['title' => 'Renovasi Gedung Kantor Dinas',       'lokasi' => 'Kota Semarang, Jateng',  'nilai' => 'Rp 1.800.000.000', 'pct' => '+0,8%', 'tgl' => '2026-01-20', 'href' => base_url('menu-rap?id=2')],
        // Cards without RAB yet → editable / new mode
        ['title' => 'Pembangunan Jembatan Desa',          'lokasi' => 'Kab. Banyumas, Jateng',  'nilai' => null,               'pct' => null,    'tgl' => '2026-03-05', 'href' => base_url('menu-rap?mode=new')],
        ['title' => 'Rehabilitasi Gedung Sekolah',        'lokasi' => 'Kota Surabaya, Jatim',   'nilai' => null,               'pct' => null,    'tgl' => '2026-03-10', 'href' => base_url('menu-rap?mode=new')],
        ['title' => 'Pembangunan Embung Irigasi',         'lokasi' => 'Kab. Bantul, DIY',       'nilai' => null,               'pct' => null,    'tgl' => '2026-03-18', 'href' => base_url('menu-rap?mode=new')],
        ['title' => 'Peningkatan Jalan Kabupaten',        'lokasi' => 'Kab. Magelang, Jateng',  'nilai' => null,               'pct' => null,    'tgl' => '2026-03-22', 'href' => base_url('menu-rap?mode=new')],
    ];

    foreach ($cards as $card):
        echo view('partials/card-proyek', ['card' => $card]);
    endforeach;
    ?>
  </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<?= $this->endSection() ?>