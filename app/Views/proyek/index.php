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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module">
    import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';

    // Same mixin as shared/ui/confirm.js
    const AppSwal = Swal.mixin({
        customClass: {
            popup: 'app-swal-popup',
            title: 'app-swal-title',
            htmlContainer: 'app-swal-html',
            confirmButton: 'app-swal-confirm',
            cancelButton: 'app-swal-cancel',
            icon: 'app-swal-icon',
        },
        buttonsStyling: false,
        reverseButtons: true,
        scrollbarPadding: false,
    });

    const baseUrl = '<?= base_url() ?>';

    // Tutup semua hs-dropdown yang sedang terbuka sebelum SweetAlert muncul
    // supaya dropdown tidak nyasar saat body mendapat overflow:hidden dari overlay.
    function closeAllDropdowns() {
        document.querySelectorAll('.hs-dropdown.open, .hs-dropdown[open]').forEach(dd => {
            // Gunakan Preline API jika tersedia
            if (window.HSDropdown) {
                const instance = HSDropdown.getInstance(dd, true);
                if (instance?.element) instance.element.close();
            }
            // Fallback: hapus class open dan sembunyikan menu secara langsung
            dd.removeAttribute('open');
            dd.classList.remove('open');
            const menu = dd.querySelector('.hs-dropdown-menu');
            if (menu) {
                menu.classList.add('hidden');
                menu.classList.remove('block', 'opacity-100', 'pointer-events-auto');
            }
        });
    }

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-selesai-proyek');
        if (!btn) return;
        e.stopPropagation();

        const id = btn.dataset.id;
        const nama = btn.dataset.nama;

        closeAllDropdowns();
        const { isConfirmed } = await AppSwal.fire({
            icon: 'question',
            title: 'Tandai Proyek Selesai?',
            html: `Proyek <strong>${nama}</strong> akan ditandai sebagai <strong style="color:#10b981">Selesai</strong>.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal',
            focusCancel: true,
        });

        if (!isConfirmed) return;

        try {
            const res = await fetch(`${baseUrl}proyek/selesai/${id}`, { method: 'POST' });
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal');

            // Update DOM in-place without reload
            const card = document.getElementById(`proyek-card-${id}`);
            if (card) {
                const dropdownWrap = card.querySelector('.hs-dropdown')?.closest('div.absolute');
                if (dropdownWrap) {
                    dropdownWrap.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/90 backdrop-blur-sm text-white text-xs font-semibold shadow">
                        <i class="fa-solid fa-circle-check"></i> Selesai
                    </span>`;
                }
                const footerLink = card.querySelector('.group-hover\\:underline');
                if (footerLink) {
                    footerLink.outerHTML = `
                    <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color:#10b981;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span class="hidden sm:inline">Selesai</span>
                    </span>`;
                }
            }

            AppSwal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: 'Proyek ditandai selesai!',
                showConfirmButton: false, timer: 2000, timerProgressBar: true,
            });

        } catch (err) {
            AppSwal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: err.message || 'Terjadi kesalahan',
                showConfirmButton: false, timer: 2500,
            });
        }
    });

    // Hapus Proyek
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-hapus-proyek');
        if (!btn) return;
        e.stopPropagation();

        const id = btn.dataset.id;
        const nama = btn.dataset.nama;

        closeAllDropdowns();
        const { isConfirmed } = await AppSwal.fire({
            icon: 'warning',
            title: 'Hapus Proyek Permanen?',
            html: `Proyek <strong>${nama}</strong> beserta seluruh data RAP dan AHS akan <strong style="color:#ef4444">dihapus selamanya</strong>.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            focusCancel: true,
        });

        if (!isConfirmed) return;

        try {
            const res = await fetch(`${baseUrl}proyek/delete/${id}`, { method: 'DELETE' });
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal');

            // Remove card from DOM with animation
            const card = document.getElementById(`proyek-card-${id}`);
            if (card) {
                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)';
                setTimeout(() => card.remove(), 400);
            }

            AppSwal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: 'Proyek berhasil dihapus!',
                showConfirmButton: false, timer: 2000, timerProgressBar: true
            });

        } catch (err) {
            AppSwal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: err.message || 'Gagal menghapus proyek',
                showConfirmButton: false, timer: 3000
            });
        }
    });
    // Filter Feature
    const filterNama = document.getElementById('filter-nama');
    const clearNama = document.getElementById('clear-nama');

    const filterLokasi = document.getElementById('filter-lokasi');
    const clearLokasi = document.getElementById('clear-lokasi');

    const filterTahun = document.getElementById('filter-tahun');
    const clearTahun = document.getElementById('clear-tahun');

    const emptyState = document.getElementById('empty-state');
    const proyekGrid = document.getElementById('proyek-grid');

    function applyFilters() {
        const namaVal = filterNama ? filterNama.value.toLowerCase() : '';

        const isLokasiSelected = filterLokasi && filterLokasi.value !== "";
        const lokasiVal = isLokasiSelected ? filterLokasi.options[filterLokasi.selectedIndex]?.text.toLowerCase() : "";

        const isTahunSelected = filterTahun && filterTahun.value !== "";
        const tahunVal = isTahunSelected ? filterTahun.value.toLowerCase() : "";

        // Toggle clear buttons
        if (clearNama) clearNama.classList.toggle('hidden', namaVal === '');
        if (clearLokasi) clearLokasi.classList.toggle('hidden', !isLokasiSelected);
        if (clearTahun) clearTahun.classList.toggle('hidden', !isTahunSelected);

        const cards = document.querySelectorAll('.proyek-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cNama = card.getAttribute('data-nama') || '';
            const cLokasi = card.getAttribute('data-lokasi') || '';
            const cTahun = card.getAttribute('data-tahun') || '';

            const matchNama = !namaVal || cNama.includes(namaVal);
            const matchLokasi = !isLokasiSelected || cLokasi.includes(lokasiVal);
            const matchTahun = !tahunVal || cTahun.includes(tahunVal);

            if (matchNama && matchLokasi && matchTahun) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            if (visibleCount === 0 && cards.length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    function resetPrelineSelect(selectId) {
        const el = document.getElementById(selectId);
        if (el) {
            el.value = '';
            el.dispatchEvent(new Event('change', { bubbles: true }));

            const wrap = el.closest('.relative');
            if (wrap) {
                const titleSpan = wrap.querySelector('[data-title]');
                const placeholder = JSON.parse(el.getAttribute('data-hs-select') || '{}').placeholder || 'Semua';
                if (titleSpan) titleSpan.innerHTML = '<span class="text-gray-500">' + placeholder + '</span>';

                const dropdown = wrap.querySelector('.hs-select-dropdown');
                if (dropdown) {
                    dropdown.querySelectorAll('.hs-selected').forEach(n => {
                        n.classList.remove('hs-selected');
                        n.classList.remove('bg-gray-100');
                        const check = n.querySelector('svg');
                        if (check && check.parentElement) check.parentElement.classList.add('hidden');
                    });
                }
            }
            applyFilters();
        }
    }

    if (filterNama) {
        filterNama.addEventListener('input', applyFilters);
        if (clearNama) {
            clearNama.addEventListener('click', () => {
                filterNama.value = '';
                applyFilters();
            });
        }
    }

    if (filterTahun) {
        filterTahun.addEventListener('change', applyFilters);
        if (clearTahun) {
            clearTahun.addEventListener('click', (e) => {
                e.stopPropagation();
                resetPrelineSelect('filter-tahun');
            });
        }
    }

    if (filterLokasi) {
        filterLokasi.addEventListener('change', applyFilters);
        if (clearLokasi) {
            clearLokasi.addEventListener('click', (e) => {
                e.stopPropagation();
                resetPrelineSelect('filter-lokasi');
            });
        }
    }

</script>
<?= $this->endSection() ?>