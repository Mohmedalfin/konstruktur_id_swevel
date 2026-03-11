/**
 * Data structure:
 *   { categories: [ { id, name, items: [ { no, uraian, volume, satuan, hargaBahan, hargaAlat, hargaUpah, hargaKeseluruhan } ] } ] }
 * Modes:
 *  'readonly' — card clicked → category headers + sub-rows, accordion collapsible, Detail button
 *  'editable' — Add RAB clicked → category headers only, "+ Tambah Item" per category
 */

(function () {

    'use strict';

    const state = {
        mode: null,
        currentId: null,
        collapsed: {}
    };

    const wrapper              = document.getElementById('rab-table-wrapper');
    const tbody                = document.getElementById('rab-tbody');
    const addRowBtn            = document.getElementById('rab-add-row-btn');
    const totalJumlah          = document.getElementById('rab-total-jumlah');
    const totalPpn             = document.getElementById('rab-total-ppn');
    const totalFinal           = document.getElementById('rab-total-final');
    const addRabBtn            = document.getElementById('addRabBtn');
    const cards                = document.querySelectorAll('.rab-card');
    const boqImportBtn         = document.getElementById('boq-import-btn');
    const boqFileInput         = document.getElementById('boq-file-input');
    const boqDownloadTplBtn    = document.getElementById('boq-download-template-btn');

    if (!wrapper || !tbody) return;

    const dummyDatabase = {
        1: {
            categories: [
                {
                    id: 'persiapan',
                    name: 'Pekerjaan Persiapan',
                    items: [
                        { no: 1, uraian: 'Pembuatan gudang semen dan peralatan', volume: 1,    satuan: 'm²', hargaBahan: 18000.00,  hargaAlat: 5000.00,  hargaUpah: 9621.60,  hargaKeseluruhan: 32621.60  },
                        { no: 2, uraian: 'Buangan tanah galian',                 volume: 12.5, satuan: 'm³', hargaBahan: 0,         hargaAlat: 25000.00, hargaUpah: 20000.00, hargaKeseluruhan: 562500.00 }
                    ]
                },
                {
                    id: 'struktur',
                    name: 'Pekerjaan Struktur',
                    items: [
                        { no: 1, uraian: 'Pengecoran pondasi beton',    volume: 5,   satuan: 'm³', hargaBahan: 600000.00, hargaAlat: 150000.00, hargaUpah: 200000.00, hargaKeseluruhan: 4750000.00  },
                        { no: 2, uraian: 'Pemasangan besi tulangan D16', volume: 200, satuan: 'kg', hargaBahan: 10000.00,  hargaAlat: 1500.00,   hargaUpah: 3000.00,   hargaKeseluruhan: 2900000.00  },
                        { no: 3, uraian: 'Bekisting kolom',             volume: 30,  satuan: 'm²', hargaBahan: 70000.00,  hargaAlat: 20000.00,  hargaUpah: 35000.00,  hargaKeseluruhan: 3750000.00  }
                    ]
                },
                {
                    id: 'arsitektur',
                    name: 'Pekerjaan Arsitektur',
                    items: [
                        { no: 1, uraian: 'Pasangan dinding bata merah 1:4', volume: 80,  satuan: 'm²', hargaBahan: 110000.00, hargaAlat: 15000.00, hargaUpah: 60000.00, hargaKeseluruhan: 14800000.00 },
                        { no: 2, uraian: 'Plesteran & acian dinding',       volume: 160, satuan: 'm²', hargaBahan: 40000.00,  hargaAlat: 8000.00,  hargaUpah: 24000.00, hargaKeseluruhan: 11520000.00 }
                    ]
                }
            ]
        },
        2: {
            categories: [
                {
                    id: 'persiapan',
                    name: 'Pekerjaan Persiapan',
                    items: [
                        { no: 1, uraian: 'Pembongkaran atap lama', volume: 1, satuan: 'ls', hargaBahan: 500000.00, hargaAlat: 800000.00, hargaUpah: 1200000.00, hargaKeseluruhan: 2500000.00 }
                    ]
                },
                {
                    id: 'struktur',
                    name: 'Pekerjaan Struktur',
                    items: [
                        { no: 1, uraian: 'Perkuatan balok eksisting', volume: 8, satuan: 'm',  hargaBahan: 250000.00, hargaAlat: 80000.00, hargaUpah: 120000.00, hargaKeseluruhan: 3600000.00 },
                        { no: 2, uraian: 'Cor plat lantai t=12cm',    volume: 6, satuan: 'm²', hargaBahan: 450000.00, hargaAlat: 130000.00, hargaUpah: 200000.00, hargaKeseluruhan: 4680000.00 }
                    ]
                }
            ]
        }
    };

    const defaultCategories = [
        { id: 'persiapan',  name: 'Pekerjaan Persiapan'  },
        { id: 'struktur',   name: 'Pekerjaan Struktur'    },
        { id: 'arsitektur', name: 'Pekerjaan Arsitektur'  },
        { id: 'mep',        name: 'Pekerjaan MEP'         },
        { id: 'finishing',  name: 'Pekerjaan Finishing'   }
    ];

    function fetchRabData(id) {
        return new Promise(resolve => {
            setTimeout(() => resolve(dummyDatabase[id] || { categories: [] }), 350);
        });
    }

    const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2 });

    function renderLoading() {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                    <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Memuat data…
                </td>
            </tr>`;
        updateTotals(0);
    }

    function renderReadonly(data) {
        const categories = data.categories || [];
        const grandTotal = categories.reduce(
            (sum, cat) => sum + cat.items.reduce((s, i) => s + Number(i.hargaKeseluruhan), 0), 0
        );

        if (categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-10 text-table-subtle text-xs">Tidak ada data pekerjaan.</td></tr>`;
            updateTotals(0);
            return;
        }

        let html = '';

        categories.forEach(cat => {
            const catTotal   = cat.items.reduce((s, i) => s + Number(i.hargaKeseluruhan), 0);
            const isOpen     = !state.collapsed[cat.id];
            const subClass   = isOpen ? '' : 'hidden';
            const chevronRot = isOpen ? '' : 'rotate-180';

            html += `
                <tr class="rab-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors duration-200"
                    data-cat="${cat.id}" role="button" tabindex="0">
                    <td class="w-12 md:w-14 px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <div class="relative flex items-center justify-center w-5 h-5 mx-auto">
                            <!-- Minus: visible when open -->
                            <svg class="cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? '' : 'hidden'}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <!-- Plus: visible when closed -->
                            <svg class="cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? 'hidden' : ''}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </td>
                    <td colspan="9" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                            ${cat.name}
                        </span>
                    </td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-right text-[10px] md:text-xs tabular-nums opacity-80">${fmt(catTotal)}</td>
                    <td class="w-20 md:w-24 px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <svg class="cat-chevron w-3.5 h-3.5 md:w-4 md:h-4 mx-auto opacity-60 transition-transform duration-300 ${chevronRot}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
            if (cat.items.length === 0) {
                html += `
                    <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border">
                        <td colspan="12" class="px-5 py-3 text-center text-table-subtle text-xs italic">
                            Belum ada item pekerjaan.
                        </td>
                    </tr>`;
            } else {
                cat.items.forEach(item => {
                    html += `
                        <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150">
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.no}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium min-w-[250px] lg:min-w-[350px] whitespace-normal leading-relaxed">${escHtml(item.uraian)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums">${item.volume}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.satuan}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(item.hargaBahan)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(item.hargaAlat)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(item.hargaUpah)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(item.hargaBahan * (item.volume || 1))}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(item.hargaAlat * (item.volume || 1))}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(item.hargaUpah * (item.volume || 1))}</td>
                            <td class="rab-harga-cell-${cat.id}-${item.no} px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong whitespace-nowrap">${fmt(item.hargaKeseluruhan)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                                <div class="hs-dropdown relative inline-flex">
                                    <button type="button"
                                        class="hs-dropdown-toggle inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle hover:text-table-body transition-colors focus:outline-none"
                                        title="Opsi">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div class="hs-dropdown-menu hidden z-50 mt-1 w-44 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 end-0" role="menu">
                                        <button type="button"
                                            class="readonly-item-detail flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition-colors"
                                            data-url="${(window.RAB_INIT && window.RAB_INIT.rincianAhsUrl) || '/menu-rap/rincian-ahs'}">
                                            <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Input Rincian AHS
                                        </button>
                                        <div class="border-t border-table-border my-1"></div>
                                        <button type="button"
                                            class="readonly-item-delete flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition-colors">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>`;

                });
            }
        });

        tbody.innerHTML = html;
        updateTotals(grandTotal);
        bindCategoryToggle();
        bindReadonlyDropdowns();
        try { window.HSStaticMethods?.autoInit(['dropdown']); } catch (_) {}
    }

    function renderEditable(categories) {
        if (categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="12" class="text-center py-10 text-table-subtle text-xs">Tidak ada kategori.</td></tr>`;
            updateTotals(0);
            return;
        }

        let html = '';

        categories.forEach(cat => {
            html += `
                <tr class="rab-category bg-table-category text-white">
                    <td class="w-12 md:w-14 px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <button
                            class="edit-cat-toggle-btn relative flex items-center justify-center w-5 h-5 mx-auto focus:outline-none"
                            data-cat="${cat.id}" title="Buka / Tutup">
                            <!-- Plus: shown when closed (hidden initially since rows start open) -->
                            <svg class="edit-cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 hidden"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <!-- Minus: shown when open (visible initially since rows start open) -->
                            <svg class="edit-cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </td>
                    <td colspan="10" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                            ${cat.name}
                        </span>
                    </td>
                    <td class="px-2 md:px-3 py-2.5 md:py-3 text-center">
                        <div class="inline-flex items-center gap-1">
                            <button
                                class="add-subitem-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}" data-catname="${cat.name}" title="Tambah AHS">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                            <button
                                class="del-cat-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/80 text-white/70 hover:text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}" title="Hapus semua item kategori ini">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="subrow-placeholder-${cat.id} bg-table-row border-b border-table-border">
                    <td colspan="12" class="px-5 py-2.5 text-center text-table-subtle text-xs italic">
                        Belum ada item — klik Tambah untuk menambahkan.
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
        updateTotals(0);
        bindAddSubItem();
        bindEditableCategoryToggle();
        injectPendingItems();
    }

    function updateTotals(total) {
        const ppn   = total * 0.11;
        const grand = total + ppn;
        if (totalJumlah) totalJumlah.textContent = fmt(total);
        if (totalPpn)    totalPpn.textContent    = fmt(ppn);
        if (totalFinal)  totalFinal.textContent  = fmt(grand);
    }

    function showTable() {
        wrapper.classList.remove('hidden');
        setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
    }

    function bindCategoryToggle() {
        tbody.querySelectorAll('.rab-category[data-cat]').forEach(row => {
            row.addEventListener('click', function () {
                const catId   = row.dataset.cat;
                const subRows = tbody.querySelectorAll(`.subrow-${catId}`);
                const minus   = row.querySelector('.cat-icon-minus');
                const plus    = row.querySelector('.cat-icon-plus');
                const chevron = row.querySelector('.cat-chevron');
                const isHidden = subRows.length && subRows[0].classList.contains('hidden');

                subRows.forEach(r => r.classList.toggle('hidden', !isHidden));

                if (minus)   minus.classList.toggle('hidden', !isHidden);
                if (plus)    plus.classList.toggle('hidden',   isHidden);

                if (chevron) chevron.classList.toggle('rotate-180', !isHidden);

                state.collapsed[catId] = !isHidden;
            });

            row.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); row.click(); }
            });
        });
    }

    function bindReadonlyDropdowns() {
        tbody.querySelectorAll('.readonly-item-detail').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const url = btn.dataset.url || '/menu-rap/rincian-ahs';
                window.location.href = url;
            });
        });

        tbody.querySelectorAll('.readonly-item-delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const row = btn.closest('tr');
                if (row) row.remove();
                
                let total = 0;
                tbody.querySelectorAll('[class*="rab-harga-cell-"]').forEach(function (cell) {
                    const val = cell.textContent.replace(/[^\d,]/g, '').replace(',', '.');
                    total += parseFloat(val) || 0;
                });
                updateTotals(total);
            });
        });
    }

    function bindAddSubItem() {
        tbody.querySelectorAll('.add-subitem-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const catId   = btn.dataset.cat;
                const catName = btn.dataset.catname || '';
                
                try {
                    sessionStorage.setItem('rab_tambah_ahs_cat',     catId);
                    sessionStorage.setItem('rab_tambah_ahs_catname', catName);
                    sessionStorage.setItem('rab_return_url', window.location.href);
                } catch (_) {}
                const url = (window.RAB_INIT && window.RAB_INIT.tambahAhsUrl)
                    ? window.RAB_INIT.tambahAhsUrl
                    : '/menu-rap/tambah-ahs';
                window.location.href = url;
            });
        });

        tbody.querySelectorAll('.del-cat-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const catId     = btn.dataset.cat;
                const catHeader = btn.closest('tr');

                tbody.querySelectorAll(`.subrow-item-${catId}`).forEach(r => r.remove());

                if (!tbody.querySelector(`.subrow-placeholder-${catId}`)) {
                    const placeholder = document.createElement('tr');
                    placeholder.className = `subrow-placeholder-${catId} bg-table-row border-b border-table-border`;
                    placeholder.innerHTML = `<td colspan="12" class="px-5 py-2.5 text-center text-table-subtle text-xs italic">
                        Belum ada item — klik Tambah untuk menambahkan.
                    </td>`;
                    catHeader.after(placeholder);
                }
            });
        });
    }

    function injectPendingItems() {
        let groups = [];
        try {
            const raw = sessionStorage.getItem('rab_pending_items');
            if (raw) groups = JSON.parse(raw);
        } catch (_) { return; }
        if (!groups || groups.length === 0) return;

        let grandHarga = 0;

        groups.forEach(function (group) {
            const catId = group.catId;
            const items = group.items || [];
            if (items.length === 0) return;

            const placeholder = tbody.querySelector('.subrow-placeholder-' + catId);
            if (placeholder) placeholder.remove();

            const catHeader = tbody.querySelector('.rab-category [data-cat="' + catId + '"]');
            const anchorRow = catHeader ? catHeader.closest('tr') : null;

            tbody.querySelectorAll('.subrow-item-' + catId).forEach(function (r) { r.remove(); });

            let rowNum      = 0;
            let lastInserted = anchorRow; 

            items.forEach(function (item) {
                rowNum++;
                const hargaBahan = parseFloat(item.hargaBahan) || 0;
                const hargaAlat  = parseFloat(item.hargaAlat)  || 0;
                const hargaUpah  = parseFloat(item.hargaUpah)  || 0;
                const hargaKsl   = parseFloat(item.hargaKeseluruhan) || (hargaBahan + hargaAlat + hargaUpah);
                grandHarga      += hargaKsl;
                const itemRow    = document.createElement('tr');
                itemRow.className = 'subrow-item-' + catId + ' subrow-' + catId + ' bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150';
                itemRow.innerHTML = `
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${rowNum}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium min-w-[250px] lg:min-w-[350px] whitespace-normal leading-relaxed">${escHtml(item.nama)}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums">${escHtml(String(item.volume ?? 1))}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${escHtml(item.satuan)}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaBahan)}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaAlat)}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaUpah)}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaBahan * (item.volume || 1))}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaAlat * (item.volume || 1))}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaUpah * (item.volume || 1))}</td>
                    <td class="rab-harga-cell-${escHtml(catId)}-pending-${escHtml(String(item.id))} px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong whitespace-nowrap">${fmt(hargaKsl)}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                        <div class="hs-dropdown relative inline-flex">
                            <button type="button"
                                class="hs-dropdown-toggle inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle hover:text-table-body transition-colors focus:outline-none"
                                title="Opsi">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                            <div class="hs-dropdown-menu hidden z-50 mt-1 w-44 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 end-0" role="menu">
                                <button type="button"
                                    class="pending-item-edit flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Input Rincian AHS
                                </button>
                                <div class="border-t border-table-border my-1"></div>
                                <button type="button"
                                    class="del-pending-item flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </td>`;

                if (lastInserted && lastInserted.parentNode) {
                    lastInserted.parentNode.insertBefore(itemRow, lastInserted.nextSibling);
                } else {
                    tbody.appendChild(itemRow);
                }
                lastInserted = itemRow;

                itemRow.querySelector('.del-pending-item').addEventListener('click', function () {
                    itemRow.remove();
                    recomputePendingTotals();
                });

                itemRow.querySelector('.pending-item-edit').addEventListener('click', function () {
                    try {
                        sessionStorage.setItem('ahs_item_label', item.nama || '');
                        sessionStorage.setItem('rab_return_url', window.location.href);
                    } catch (_) {}
                    const url = (window.RAB_INIT && window.RAB_INIT.rincianAhsUrl)
                        ? window.RAB_INIT.rincianAhsUrl
                        : '/menu-rap/rincian-ahs';
                    window.location.href = url;
                });


            });
        });

        recomputePendingTotals();

        try { sessionStorage.removeItem('rab_pending_items'); } catch (_) {}

        try { window.HSStaticMethods?.autoInit(['dropdown']); } catch (_) {}

        tbody.querySelectorAll('.rab-category').forEach(function(catHeaderRow) {
            const catBtn = catHeaderRow.querySelector('.edit-cat-toggle-btn');
            if (catBtn) {
                const checkCatId = catBtn.dataset.cat;
                if (tbody.querySelectorAll('.subrow-item-' + checkCatId).length === 0) {
                    catHeaderRow.classList.add('hidden');
                    const placeholder = tbody.querySelector('.subrow-placeholder-' + checkCatId);
                    if (placeholder) placeholder.classList.add('hidden');
                } else {
                    catHeaderRow.classList.remove('hidden');
                    const placeholder = tbody.querySelector('.subrow-placeholder-' + checkCatId);
                    if (placeholder) placeholder.classList.add('hidden'); 
                }
            }
        });
    }

    function recomputePendingTotals() {
        let total = 0;
        tbody.querySelectorAll('[class*="rab-harga-cell-"]').forEach(function (cell) {
            const val = cell.textContent.replace(/[^0-9,]/g, '').replace(',', '.');
            total += parseFloat(val) || 0;
        });
        updateTotals(total);
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function bindEditableCategoryToggle() {
        tbody.querySelectorAll('.edit-cat-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const catId = btn.dataset.cat;
                const plus  = btn.querySelector('.edit-cat-icon-plus');
                const minus = btn.querySelector('.edit-cat-icon-minus');

                const targets = tbody.querySelectorAll(
                    `.subrow-placeholder-${catId}, .subrow-item-${catId}`
                );

                const isOpen = targets.length && !targets[0].classList.contains('hidden');

                targets.forEach(r => r.classList.toggle('hidden', isOpen));

                if (plus)  plus.classList.toggle('hidden',  !isOpen);
                if (minus) minus.classList.toggle('hidden',  isOpen);
            });
        });
    }

    cards.forEach(card => {
        card.addEventListener('click', async function () {
            const id = card.dataset.id;
            state.mode      = 'readonly';
            state.currentId = id;
            state.collapsed = {};

            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            card.classList.add('ring-2', 'ring-primary');

            showTable('readonly');
            renderLoading();

            const data = await fetchRabData(id);
            renderReadonly(data);
        });
    });

    if (addRabBtn) {
        addRabBtn.addEventListener('click', function () {
            state.mode      = 'editable';
            state.currentId = null;
            state.collapsed = {};

            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));

            showTable('editable');
            renderEditable(defaultCategories);
        });
    }

    document.addEventListener('DOMContentLoaded', async function () {
        const init = window.RAB_INIT;
        if (!init || !init.mode) return;

        if (init.mode === 'readonly' && init.id) {
            state.mode      = 'readonly';
            state.currentId = init.id;
            showTable('readonly');
            renderLoading();
            const data = await fetchRabData(init.id);
            renderReadonly(data);
        } else if (init.mode === 'new') {
            state.mode      = 'editable';
            state.currentId = null;
            showTable('editable');
            renderEditable(defaultCategories);
        }
    });

    window.addEventListener('rabDataImported', function (e) {
        const importedItems = e.detail;
        if (!importedItems || importedItems.length === 0) return;

        if (state.mode === 'readonly') {
            alert("RAB dalam mode Read-Only. Tidak bisa mengimpor data ke sini.");
            return;
        }

        const newItems = importedItems.map(item => ({
            id: item.id,
            nama: item.uraian,
            volume: item.volume,
            satuan: item.satuan,
            hargaBahan: item.harga_bahan,
            hargaAlat: item.harga_alat,
            hargaUpah: item.harga_upah,
            hargaKeseluruhan: (item.volume || 1) * (item.harga_bahan + item.harga_alat + item.harga_upah),
            kategori: item.kategori || 'persiapan'
        }));

        const groupedItems = {};
        newItems.forEach(item => {
            const catId = item.kategori;
            if (!groupedItems[catId]) groupedItems[catId] = [];
            groupedItems[catId].push(item);
        });

        try {
            let existing = sessionStorage.getItem('rab_pending_items');
            let parsed = existing ? JSON.parse(existing) : [];
            
            Object.keys(groupedItems).forEach(catId => {
                let foundCat = parsed.find(g => g.catId === catId);
                if (foundCat) {
                    foundCat.items.push(...groupedItems[catId]);
                } else {
                    parsed.push({ catId: catId, items: groupedItems[catId] });
                }
            });

            sessionStorage.setItem('rab_pending_items', JSON.stringify(parsed));
        } catch (_) {}

        injectPendingItems();
        
        alert(`Berhasil menambahkan ${importedItems.length} item pekerjaan dari Excel.`);
    });

    if (boqDownloadTplBtn) {
        boqDownloadTplBtn.addEventListener('click', function () {
            const headers = ['No', 'Uraian Pekerjaan', 'Volume', 'Satuan'];
            const examples = [
                ['1', 'Contoh: Pembuatan gudang semen', '1', 'm²'],
                ['2', 'Contoh: Pengecoran pondasi', '5', 'm³'],
            ];
            const csvRows = [headers, ...examples]
                .map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(','))
                .join('\r\n');

            const blob = new Blob([csvRows], { type: 'text/csv;charset=utf-8;' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = 'template_boq.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    }

})();