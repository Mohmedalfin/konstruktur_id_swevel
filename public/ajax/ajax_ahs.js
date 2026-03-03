/**
 * ajax_ahs.js
 * To connect to a real CI4 endpoint, replace fetchAhsData() with:
 *   const res  = await fetch(`/api/ahs/${id}`);
 *   return res.json();
 */
(function () {
    'use strict';

    /* ============================================================
       DOM REFERENCES
    ============================================================ */
    const tbody      = document.getElementById('ahs-tbody');
    const totalRab   = document.getElementById('ahs-total-rab');
    const totalRap   = document.getElementById('ahs-total-rap');
    const titleEl    = document.querySelector('#ahs-table')
                           ? document.querySelector('[id="ahs-table"]')
                               ?.closest('div')
                               ?.previousElementSibling
                               ?.querySelector('h2')
                           : null;

    if (!tbody) return;

    /* ============================================================
       DUMMY DATA  (replace fetchAhsData with a real fetch() call)
    ============================================================ */
    const dummyDatabase = {
        // keyed by item/pekerjaan ID (passed via window.AHS_INIT.id)
        1: {
            itemName: 'Pembuatan gudang semen dan peralatan',
            jasaPct:  10,
            categories: [
                {
                    id:    'bahan',
                    label: 'A',
                    name:  'Bahan',
                    items: [
                        { no: 1, uraian: 'Kayu Kaso 5/7 Kelas II',      koefisien: 0.31,   satuan: 'm³', hargaDasar: 1514255.71, hargaSatuan: 1570000.00, merk: 'Kamper',  spesifikasi: 'Standar' },
                        { no: 2, uraian: 'Paku usuk 5 cm',               koefisien: 0.50,   satuan: 'kg', hargaDasar:   18000.00, hargaSatuan:   18000.00, merk: '-',       spesifikasi: '-'       },
                        { no: 3, uraian: 'Seng gelombang BJLS 0.20 mm',  koefisien: 1.10,   satuan: 'm²', hargaDasar:   89000.00, hargaSatuan:   89000.00, merk: '-',       spesifikasi: '-'       }
                    ]
                },
                {
                    id:    'upah',
                    label: 'B',
                    name:  'Upah',
                    items: [
                        { no: 1, uraian: 'Pekerja',     koefisien: 0.10,  satuan: 'Oh', hargaDasar: 85000.00,  hargaSatuan: 85000.00,  merk: '-', spesifikasi: '-' },
                        { no: 2, uraian: 'Tukang kayu', koefisien: 0.05,  satuan: 'Oh', hargaDasar: 100000.00, hargaSatuan: 100000.00, merk: '-', spesifikasi: '-' },
                        { no: 3, uraian: 'Kepala tukang', koefisien: 0.005, satuan: 'Oh', hargaDasar: 115000.00, hargaSatuan: 115000.00, merk: '-', spesifikasi: '-' }
                    ]
                },
                {
                    id:    'alat',
                    label: 'C',
                    name:  'Alat',
                    items: [
                        { no: 1, uraian: 'Palu', koefisien: 0.01, satuan: 'bh', hargaDasar: 35000.00, hargaSatuan: 35000.00, merk: '-', spesifikasi: '-' }
                    ]
                }
            ]
        }
    };

    /**
     * Simulate AJAX fetch — swap with a real endpoint when ready:
     *   const res = await fetch(`/api/ahs/${id}`);
     *   return res.json();
     */
    function fetchAhsData(id) {
        return new Promise(function (resolve) {
            setTimeout(function () {
                resolve(dummyDatabase[id] || { itemName: '', jasaPct: 10, categories: [] });
            }, 350);
        });
    }

    /* ============================================================
       FORMAT HELPERS
    ============================================================ */
    const fmt = function (n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2 });
    };

    /* ============================================================
       RENDER — LOADING SPINNER
    ============================================================ */
    function renderLoading() {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                    <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Memuat data…
                </td>
            </tr>`;
        updateTotals(0, 0);
    }

    /* ============================================================
       RENDER — FULL TABLE BODY
    ============================================================ */
    function renderAhs(data) {
        const categories = data.categories || [];
        const jasaPct    = Number(data.jasaPct || 10) / 100;

        if (categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-table-subtle text-xs">Tidak ada data AHS.</td></tr>`;
            updateTotals(0, 0);
            return;
        }

        // Update page title if element found
        if (titleEl && data.itemName) {
            titleEl.textContent = 'Rincian AHS — ' + data.itemName;
        }

        let grandRab = 0;
        let html = '';

        categories.forEach(function (cat) {
            const catRab = cat.items.reduce(function (s, i) {
                return s + Number(i.koefisien) * Number(i.hargaDasar);
            }, 0);
            const catJasa    = catRab * jasaPct;
            const catTotalRab = catRab + catJasa;
            grandRab += catTotalRab;

            // ── Category header row ──
            html += `
                <tr class="ahs-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors duration-200"
                    data-cat="${cat.id}" role="button" tabindex="0">
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <div class="relative flex items-center justify-center w-5 h-5 mx-auto">
                            <svg class="cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg class="cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </td>
                    <td colspan="6" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                            ${cat.label}. ${cat.name}
                        </span>
                    </td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <svg class="cat-chevron w-3.5 h-3.5 md:w-4 md:h-4 mx-auto opacity-60 transition-transform duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </td>
                </tr>`;

            if (cat.items.length === 0) {
                html += `
                    <tr class="subrow-${cat.id} bg-table-row border-b border-table-border">
                        <td colspan="8" class="px-5 py-3 text-center text-table-subtle text-xs italic">Belum ada item.</td>
                    </tr>`;
            } else {
                cat.items.forEach(function (item) {
                    const hargaSatuan = Number(item.koefisien) * Number(item.hargaDasar);

                    // ── Item name row ──
                    html += `
                        <tr class="subrow-${cat.id} bg-table-row border-b border-table-border/30">
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle font-medium">${item.no}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 font-semibold text-table-strong" title="${item.uraian}">${item.uraian}</td>
                            <td colspan="6" class="px-3 md:px-5 py-2 md:py-2.5"></td>
                        </tr>`;

                    // ── RAB (read-only) row ──
                    html += `
                        <tr class="subrow-${cat.id} bg-table-row border-b border-table-border/40 hover:bg-white transition-colors duration-150">
                            <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 font-semibold text-table-muted text-[10px] md:text-xs">RAB</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5 text-center tabular-nums">${Number(item.koefisien).toFixed(4)}</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5 text-center text-table-subtle">${item.satuan}</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5 text-right tabular-nums text-table-medium">${fmt(item.hargaDasar)}</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong">${fmt(hargaSatuan)}</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5 text-center text-table-subtle">${item.merk || '—'}</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5 text-center text-table-subtle">${item.spesifikasi || '—'}</td>
                        </tr>`;

                    // ── RAP Rencana (editable inputs) row ──
                    html += `
                        <tr class="subrow-${cat.id} bg-white border-b border-table-border hover:bg-white transition-colors duration-150">
                            <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 font-semibold text-primary text-[10px] md:text-xs">RAP Rencana</td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5">
                                <input type="text" placeholder="${Number(item.koefisien).toFixed(4)}"
                                    class="w-full px-2 py-1.5 text-[10px] md:text-xs border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary text-table-medium bg-white"
                                    data-field="koefisien" data-cat="${cat.id}" data-item="${item.no}" />
                            </td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5">
                                <input type="text" placeholder="${item.satuan}"
                                    class="w-full px-2 py-1.5 text-[10px] md:text-xs border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary text-table-medium bg-white"
                                    data-field="satuan" data-cat="${cat.id}" data-item="${item.no}" />
                            </td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5">
                                <div class="relative">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-table-subtle text-[10px]">Rp</span>
                                    <input type="text" placeholder="0"
                                        class="w-full pl-7 pr-2 py-1.5 text-[10px] md:text-xs border border-table-border rounded text-right focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary text-table-medium bg-white"
                                        data-field="hargaDasar" data-cat="${cat.id}" data-item="${item.no}" />
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5">
                                <div class="relative">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-table-subtle text-[10px]">Rp</span>
                                    <input type="text" placeholder="0"
                                        class="w-full pl-7 pr-2 py-1.5 text-[10px] md:text-xs border border-table-border rounded text-right focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary text-table-medium bg-white"
                                        data-field="hargaSatuan" data-cat="${cat.id}" data-item="${item.no}" />
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5">
                                <input type="text" placeholder="—"
                                    class="w-full px-2 py-1.5 text-[10px] md:text-xs border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary text-table-medium bg-white"
                                    data-field="merk" data-cat="${cat.id}" data-item="${item.no}" />
                            </td>
                            <td class="px-4 md:px-6 py-2 md:py-2.5">
                                <input type="text" placeholder="—"
                                    class="w-full px-2 py-1.5 text-[10px] md:text-xs border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary text-table-medium bg-white"
                                    data-field="spesifikasi" data-cat="${cat.id}" data-item="${item.no}" />
                            </td>
                        </tr>`;
                });
            }

            // ── Per-category sub-total rows ──
            // Layout: col1 empty | col2 label (=Uraian col) | colspan=3 empty | col6 value (=Harga Satuan) | colspan=2 empty
            html += `
                <tr class="subrow-${cat.id} bg-table-row border-b border-table-border/50">
                    <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 text-[10px] md:text-xs font-bold uppercase tracking-wide text-table-muted whitespace-nowrap">Jumlah Harga RAB</td>
                    <td colspan="3" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                    <td class="px-4 md:px-6 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-table-strong whitespace-nowrap">${fmt(catRab)}</td>
                    <td colspan="2" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                </tr>
                <tr class="subrow-${cat.id} border-b border-table-border/30" style="background:color-mix(in srgb,var(--color-table-category,#475569) 8%,white)">
                    <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 text-[10px] md:text-xs font-semibold uppercase tracking-wide text-table-muted whitespace-nowrap">Jasa ${data.jasaPct}%</td>
                    <td colspan="3" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                    <td class="px-4 md:px-6 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-semibold tabular-nums text-table-muted whitespace-nowrap">${fmt(catJasa)}</td>
                    <td colspan="2" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                </tr>
                <tr class="subrow-${cat.id} bg-table-row border-b border-table-border">
                    <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 text-[10px] md:text-xs font-bold uppercase tracking-wide text-table-strong whitespace-nowrap">Total Harga RAB</td>
                    <td colspan="3" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                    <td class="px-4 md:px-6 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-table-strong whitespace-nowrap">${fmt(catTotalRab)}</td>
                    <td colspan="2" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                </tr>
                <tr class="subrow-${cat.id} bg-white border-b border-table-border/50">
                    <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 text-[10px] md:text-xs font-bold uppercase tracking-wide text-primary whitespace-nowrap">Jumlah Harga RAP Rencana</td>
                    <td colspan="3" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                    <td class="ahs-cat-rap-jumlah-${cat.id} px-4 md:px-6 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-table-subtle whitespace-nowrap">Rp 0</td>
                    <td colspan="2" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                </tr>
                <tr class="subrow-${cat.id} border-b border-table-border/30" style="background:color-mix(in srgb,var(--color-primary,#0ea5e9) 6%,white)">
                    <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 text-[10px] md:text-xs font-semibold uppercase tracking-wide text-primary/70 whitespace-nowrap">Jasa ${data.jasaPct}%</td>
                    <td colspan="3" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                    <td class="ahs-cat-rap-jasa-${cat.id} px-4 md:px-6 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-semibold tabular-nums text-table-subtle whitespace-nowrap">Rp 0</td>
                    <td colspan="2" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                </tr>
                <tr class="subrow-${cat.id} bg-white border-b-2 border-table-border">
                    <td class="px-3 md:px-5 py-2 md:py-2.5"></td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 pl-4 text-[10px] md:text-xs font-bold uppercase tracking-wide text-primary whitespace-nowrap">Total Harga RAP Rencana</td>
                    <td colspan="3" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                    <td class="ahs-cat-rap-total-${cat.id} px-4 md:px-6 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-table-subtle whitespace-nowrap">Rp 0</td>
                    <td colspan="2" class="px-4 md:px-6 py-2 md:py-2.5"></td>
                </tr>`;
        });

        tbody.innerHTML = html;
        updateTotals(grandRab, 0);
        bindCategoryToggle();
    }

    /* ============================================================
       FOOTER TOTALS
    ============================================================ */
    function updateTotals(rab, rap) {
        if (totalRab) totalRab.textContent = fmt(rab);
        if (totalRap) totalRap.textContent = fmt(rap);
    }

    /* ============================================================
       ACCORDION TOGGLE
    ============================================================ */
    function bindCategoryToggle() {
        tbody.querySelectorAll('.ahs-category[data-cat]').forEach(function (row) {
            row.addEventListener('click', function () {
                const catId   = row.dataset.cat;
                const subRows = tbody.querySelectorAll('.subrow-' + catId);
                const minus   = row.querySelector('.cat-icon-minus');
                const plus    = row.querySelector('.cat-icon-plus');
                const chevron = row.querySelector('.cat-chevron');
                const isHidden = subRows.length && subRows[0].classList.contains('hidden');

                subRows.forEach(function (r) { r.classList.toggle('hidden', !isHidden); });

                if (minus)   minus.classList.toggle('hidden',  !isHidden);
                if (plus)    plus.classList.toggle('hidden',    isHidden);
                if (chevron) chevron.classList.toggle('rotate-180', !isHidden);
            });

            row.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); row.click(); }
            });
        });
    }

    /* ============================================================
       SEARCH FILTER
    ============================================================ */
    const searchInput = document.getElementById('ahs-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = searchInput.value.toLowerCase().trim();
            tbody.querySelectorAll('tr').forEach(function (row) {
                if (row.classList.contains('ahs-category')) return; // always show category rows
                const text = row.textContent.toLowerCase();
                row.classList.toggle('hidden', q !== '' && !text.includes(q));
            });
        });
    }

    /* ============================================================
       AUTO-INIT from window.AHS_INIT (set by rincian-ahs.php)
       window.AHS_INIT = { id: <number> }
       Replace fetchAhsData with a real endpoint when the API is ready.
    ============================================================ */
    async function init() {
        const cfg = window.AHS_INIT || {};
        const id  = cfg.id || 1;  // fall back to id=1 for dummy data

        renderLoading();

        const data = await fetchAhsData(id);
        renderAhs(data);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
