<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar') ?>
<header class="bg-primary text-white py-5">
  <h1 class="text-center text-4xl font-bold">DAFTAR PROYEK</h1>
</header>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Button Proyek Baru -->
<div class="mb-4">
  <button class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-white shadow-md">
    <i class="fa-solid fa-circle-plus"></i>
    <span class="font-semibold">Proyek Baru</span>
  </button>
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
<div class="mt-6 rounded-2xl bg-white p-6 shadow-md">
  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <?php for ($i = 0; $i < 6; $i++): ?>
      <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-black/5">

        <!-- COVER -->
        <div class="relative h-50 w-full overflow-hidden rounded-t-2xl">
          <img
            src="<?= base_url('assets/images/BackgroundLogin.png') ?>"
            class="h-full w-full object-cover"
            alt="Cover Proyek">

          <!-- tombol ... (tidak diubah) -->
          <div class="absolute top-0 right-0 z-20">
            <div class="hs-dropdown relative inline-flex">
              <button type="button"
                class="hs-dropdown-toggle inline-flex size-6 items-center justify-center rounded-tr-lg rounded-bl-xl bg-primary text-white shadow-md ring-1 ring-black/10 hover:bg-white hover:text-primary px-5 ransition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-110 hover:bg-indigo-500">
                <i class="fa-solid fa-ellipsis"></i>
              </button>

              <!-- dropdown menu -->
              <div
                class="hs-dropdown-menu hidden z-50 mt-2 w-44 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10"
                role="menu">
                <a class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" href="#">
                  <i class="fa-solid fa-user-plus w-4 text-light"></i>
                  Undang Tim
                </a>
                <a class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" href="#">
                  <i class="fa-regular fa-copy w-4"></i>
                  Duplikat
                </a>
                <a class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" href="#">
                  <i class="fa-regular fa-trash-can w-4"></i>
                  Hapus
                </a>
                <a class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" href="#">
                  <i class="fa-solid fa-circle-check w-4"></i>
                  Selesaikan
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- BODY -->
        <div class="p-5">
          <h3 class="text-base font-bold leading-snug text-text-primary text-center">
            Pembangunan Gedung Klinik<br>Pratama
          </h3>

          <div class="mt-4 space-y-2 text-sm text-slate-700">
            <div class="flex items-center gap-3">
              <i class="fa-solid fa-location-dot w-4 text-primary"></i>
              <span>Kab. Sleman, DIY</span>
            </div>

            <div class="flex items-center gap-3">
              <i class="fa-solid fa-money-bill-wave w-4 text-primary"></i>
              <span>Rp 3.250.000.000</span>
            </div>

            <div class="flex items-center gap-3">
              <i class="fa-solid fa-chart-simple w-4 text-primary"></i>
              <span>-2,1%</span>
            </div>

            <div class="flex items-center gap-3">
              <i class="fa-solid fa-calendar-days w-4 text-primary"></i>
              <span>2026-02-12</span>
            </div>
          </div>
        </div>
      </div>
    <?php endfor; ?>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>