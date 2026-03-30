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

  <!-- Header -->
  <div class="flex items-center gap-2 bg-primary px-4 py-2.5 text-white rounded-t-xl">
    <span class="text-sm font-semibold">Tampilkan Berdasarkan</span>
  </div>

  <div>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 p-3 md:p-4">

      <!-- Nama Proyek -->
      <div class="md:col-span-5">
        <label class="mb-1 block text-xs md:text-sm font-semibold text-text-primary">Nama Proyek</label>
        <input
          type="text"
          id="filter-nama"
          placeholder="Masukkan Nama Proyek"
          class="w-full rounded-md border border-gray-300 px-2.5 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-1 focus:ring-primary" />
      </div>

      <!-- Lokasi Proyek (Preline Select Optgroup) -->
      <div class="relative md:col-span-5">
        <label class="mb-1 block text-xs md:text-sm font-semibold text-text-primary">Lokasi Proyek</label>
        <select id="filter-lokasi" data-hs-select='{
          "hasSearch": true,
          "isSearchDirectMatch": false,
          "searchPlaceholder": "Cari Kabupaten/Kota...",
          "searchClasses": "block w-full text-sm bg-white border border-gray-300 rounded-md px-3 py-2 mb-1 focus:border-primary focus:ring-1 focus:ring-primary",
          "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 z-10",
          "placeholder": "Semua Lokasi",
          "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
          "toggleClasses": "relative py-1.5 ps-2.5 pe-8 md:py-2 md:ps-3 md:pe-9 flex w-full cursor-pointer bg-white border border-gray-300 rounded-md text-start text-xs md:text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-primary",
          "dropdownClasses": "mt-2 z-50 w-full max-h-[300px] p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden overflow-y-auto",
          "optionClasses": "hs-selected:bg-primary/10 hs-selected:border-primary hs-selected:text-primary py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-50 rounded-md",
          "optionTemplate": "<div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div><div class=\"hs-selected:font-semibold text-sm\" data-title></div><div class=\"text-xs text-gray-500\" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-primary\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\" fill=\"currentColor\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
          "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
        }' class="hidden">
          <option value="">Semua Lokasi</option>
          <?php if(!empty($wilayah)): ?>
            <?php foreach($wilayah as $prov): ?>
                <?php foreach($prov['regencies'] as $reg): ?>
                  <option value="<?= esc($reg['name'] . ', ' . $prov['name']) ?>" data-hs-select-option='{"description":"<?= esc($prov['name']) ?>"}'>
                      <?= esc($reg['name']) ?>
                  </option>
                <?php endforeach; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <!-- Tahun -->
      <div class="md:col-span-2">
        <label class="mb-1 block text-xs md:text-sm font-semibold text-text-primary">Tahun</label>
        <input
          type="number"
          id="filter-tahun"
          placeholder="Tahun"
          class="w-full rounded-md border border-gray-300 px-2.5 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-1 focus:ring-primary" />
      </div>

    </div>
  </div>

</div>


<!-- Grid Cards -->
<div class="mt-6">
  <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3 xl:grid-cols-5">
    <?php
    if (isset($proyeks) && !empty($proyeks)) {
        foreach ($proyeks as $row):
            $card = [
                'title'  => $row['nama_proyek'],
                'lokasi' => $row['lokasi_proyek'],
                'nilai'  => null, // Diambil dari realisasi RAB nanti jika ada
                'pct'    => null,
                'tgl'    => !empty($row['tgl_mulai']) ? date('Y-m-d', strtotime($row['tgl_mulai'])) : date('Y-m-d'),
                'href'   => base_url('dashboard?mode=new&slug=' . (!empty($row['slug']) ? $row['slug'] : $row['id_proyek'])),
                'cover'  => $row['foto_project'],
                'id'     => $row['id_proyek'],
                'status' => $row['status'] ?? 'Berjalan'
            ];
            echo view('partials/card-proyek', ['card' => $card]);
        endforeach;
    } else {
        echo '<div class="col-span-full text-center py-10 text-gray-500 italic">Belum ada proyek yang dibuat.</div>';
    }
    ?>
  </div>

  <!-- Empty state untuk filter (JS) -->
  <div id="filter-empty-state" class="hidden flex-col items-center justify-center py-20 text-center animate-fade-in">
    <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 border border-gray-100 shadow-inner text-gray-300">
      <i class="fa-solid fa-folder-open text-3xl"></i>
    </div>
    <h3 class="text-base sm:text-lg font-semibold text-gray-700">Tidak Ada Proyek Ditemukan</h3>
    <p class="mt-1 max-w-sm mx-auto text-xs sm:text-sm text-gray-400">Data yang Anda cari tidak tersedia. Cobalah kata kunci lain atau ubah pengaturan filter Lokasi & Tahun Anda.</p>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (session()->getFlashdata('success')) : ?>
<script type="module">
    import { toast } from "<?= base_url('js/shared/ui/toast.js') ?>";
    toast.show("<?= esc(session()->getFlashdata('success')) ?>", "success", 4000);
</script>
<?php endif; ?>

<script type="module">
    import { confirmProyekHapus, confirmProyekSelesai } from "<?= base_url('js/shared/ui/confirm.js') ?>";

    window.handleProyekComplete = async function(id, title) {
        const confirmed = await confirmProyekSelesai(title);
        if (confirmed) {
            document.getElementById('form-complete-' + id).submit();
        }
    };

    window.handleProyekDelete = async function(id, title) {
        const confirmed = await confirmProyekHapus(title);
        if (confirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    };

    // Script Filter Real-Time Card Tanpa Reload (Client-side)
    document.addEventListener('DOMContentLoaded', () => {
        const inputNama = document.getElementById('filter-nama');
        const inputTahun = document.getElementById('filter-tahun');
        const selectLokasi = document.getElementById('filter-lokasi');
        const cards = document.querySelectorAll('.proyek-card');
        const emptyState = document.getElementById('filter-empty-state');
        const gridContainer = document.querySelector('.grid.grid-cols-2'); // Optional, to hide/margin the grid if wanted

        function filterCards() {
            const valNama = inputNama ? inputNama.value.toLowerCase().trim() : '';
            const valLokasi = selectLokasi ? selectLokasi.value.toLowerCase().trim() : '';
            const valTahun = inputTahun ? inputTahun.value.trim() : '';

            let visibleCount = 0;

            cards.forEach(card => {
                const cardNama = card.getAttribute('data-nama') || '';
                const cardLokasi = card.getAttribute('data-lokasi') || '';
                const cardTahun = card.getAttribute('data-tahun') || '';

                const matchNama = cardNama.includes(valNama);
                const matchTahun = valTahun === "" || cardTahun === valTahun;
                
                // Pencocokan fleksibel lokasi (karena format data bisa "Demak" vs "Kabupaten Demak, Jawa Tengah")
                const matchLokasi = valLokasi === "" || cardLokasi.includes(valLokasi) || valLokasi.includes(cardLokasi);

                if (matchNama && matchLokasi && matchTahun) {
                    card.style.display = ''; 
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan icon data kosong jika tidak ada yang matching
            if (emptyState) {
                if (visibleCount === 0 && cards.length > 0) {
                    emptyState.classList.remove('hidden');
                    emptyState.classList.add('flex');
                } else {
                    emptyState.classList.add('hidden');
                    emptyState.classList.remove('flex');
                }
            }
        }

        if (inputNama) inputNama.addEventListener('input', filterCards);
        if (inputTahun) inputTahun.addEventListener('input', filterCards);
        if (selectLokasi) selectLokasi.addEventListener('change', filterCards);
    });

</script>
<?= $this->endSection() ?>