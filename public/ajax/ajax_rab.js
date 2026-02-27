/**
 * Data structure:
 *   { categories: [ { id, name, items: [ { no, uraian, volume, satuan, hargaDasar, harga } ] } ] }
 *
 * Modes:
 *  'readonly' — card clicked → category headers + sub-rows, accordion collapsible, Detail button
 *  'editable' — Add RAB clicked → category headers only, "+ Tambah Item" per category
 */
(function () {

    'use strict';

    /* ============================================================
       STATE
    ============================================================ */
    const state = {
        mode: null,
        currentId: null,
        collapsed: {}   // tracks which category IDs are collapsed
    };

    /* ============================================================
       DOM REFERENCES
    ============================================================ */
    const wrapper     = document.getElementById('rab-table-wrapper');
    const tbody       = document.getElementById('rab-tbody');
    const addRowBtn   = document.getElementById('rab-add-row-btn');
    const totalJumlah = document.getElementById('rab-total-jumlah');
    const totalPpn    = document.getElementById('rab-total-ppn');
    const totalFinal  = document.getElementById('rab-total-final');
    const addRabBtn   = document.getElementById('addRabBtn');
    const cards       = document.querySelectorAll('.rab-card');

    if (!wrapper || !tbody) return; // guard: table-rab.php skeleton not present

    /* ============================================================
       DUMMY DATA  (replace with real CI4 AJAX endpoints later)
       Structure: { categories: [ { id, name, items: [...] } ] }
    ============================================================ */
    const dummyDatabase = {
        1: {
            categories: [
                {
                    id: 'persiapan',
                    name: 'Pekerjaan Persiapan',
                    items: [
                        { no: 1, uraian: 'Pembuatan gudang semen dan peralatan', volume: 1,    satuan: 'm²', hargaDasar: 32621.60,  harga: 32621.60  },
                        { no: 2, uraian: 'Buangan tanah galian',                 volume: 12.5, satuan: 'm³', hargaDasar: 45000.00,  harga: 562500.00 }
                    ]
                },
                {
                    id: 'struktur',
                    name: 'Pekerjaan Struktur',
                    items: [
                        { no: 1, uraian: 'Pengecoran pondasi beton',    volume: 5,   satuan: 'm³', hargaDasar: 950000.00,  harga: 4750000.00  },
                        { no: 2, uraian: 'Pemasangan besi tulangan D16', volume: 200, satuan: 'kg', hargaDasar: 14500.00,   harga: 2900000.00  },
                        { no: 3, uraian: 'Bekisting kolom',             volume: 30,  satuan: 'm²', hargaDasar: 125000.00,  harga: 3750000.00  }
                    ]
                },
                {
                    id: 'arsitektur',
                    name: 'Pekerjaan Arsitektur',
                    items: [
                        { no: 1, uraian: 'Pasangan dinding bata merah 1:4', volume: 80,  satuan: 'm²', hargaDasar: 185000.00, harga: 14800000.00 },
                        { no: 2, uraian: 'Plesteran & acian dinding',       volume: 160, satuan: 'm²', hargaDasar: 72000.00,  harga: 11520000.00 }
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
                        { no: 1, uraian: 'Pembongkaran atap lama', volume: 1, satuan: 'ls', hargaDasar: 2500000.00, harga: 2500000.00 }
                    ]
                },
                {
                    id: 'struktur',
                    name: 'Pekerjaan Struktur',
                    items: [
                        { no: 1, uraian: 'Perkuatan balok eksisting', volume: 8, satuan: 'm',  hargaDasar: 450000.00, harga: 3600000.00 },
                        { no: 2, uraian: 'Cor plat lantai t=12cm',    volume: 6, satuan: 'm²', hargaDasar: 780000.00, harga: 4680000.00 }
                    ]
                }
            ]
        }
    };

    // Default category list used in editable (Add RAB) mode — no sub-items
    const defaultCategories = [
        { id: 'persiapan',  name: 'Pekerjaan Persiapan'  },
        { id: 'struktur',   name: 'Pekerjaan Struktur'    },
        { id: 'arsitektur', name: 'Pekerjaan Arsitektur'  },
        { id: 'mep',        name: 'Pekerjaan MEP'         },
        { id: 'finishing',  name: 'Pekerjaan Finishing'   }
    ];

    /**
     * Simulate AJAX fetch — replace with:
     *   const res = await fetch(`/api/rab/${id}`);
     *   return await res.json();
     */
    function fetchRabData(id) {
        return new Promise(resolve => {
            setTimeout(() => resolve(dummyDatabase[id] || { categories: [] }), 350);
        });
    }

    /* ============================================================
       FORMAT HELPERS
    ============================================================ */
    const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2 });
    const pct = (a, total) => total ? ((a / total) * 100).toFixed(2) + '%' : '0.00%';

    /* ============================================================
       RENDER — LOADING
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
        updateTotals(0);
    }

    /* ============================================================
       RENDER — READONLY (category headers + collapsible sub-rows)
    ============================================================ */
    function renderReadonly(data) {
        const categories = data.categories || [];
        const grandTotal = categories.reduce(
            (sum, cat) => sum + cat.items.reduce((s, i) => s + Number(i.harga), 0), 0
        );

        if (categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-table-subtle text-xs">Tidak ada data pekerjaan.</td></tr>`;
            updateTotals(0);
            return;
        }

        let html = '';

        categories.forEach(cat => {
            const catTotal   = cat.items.reduce((s, i) => s + Number(i.harga), 0);
            const isOpen     = !state.collapsed[cat.id];
            const subClass   = isOpen ? '' : 'hidden';
            const chevronRot = isOpen ? '' : 'rotate-180';

            // ── Category header row ──
            html += `
                <tr class="rab-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors duration-200"
                    data-cat="${cat.id}" role="button" tabindex="0">
                    <!-- Col 1: Minus (open) / Plus (closed) icon — fixed-width, never shifts -->
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
                    <!-- Col 2-5: Category name -->
                    <td colspan="4" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                            ${cat.name}
                        </span>
                    </td>
                    <!-- Col 6: Harga sub-total -->
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-right text-[10px] md:text-xs tabular-nums opacity-80">${fmt(catTotal)}</td>
                    <!-- Col 7: % of grand total -->
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-[10px] md:text-xs tabular-nums opacity-70">${pct(catTotal, grandTotal)}</td>
                    <!-- Col 8: Chevron (right end) -->
                    <td class="w-20 md:w-24 px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <svg class="cat-chevron w-3.5 h-3.5 md:w-4 md:h-4 mx-auto opacity-60 transition-transform duration-300 ${chevronRot}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </td>
                </tr>`;

            // ── Sub-rows ──
            if (cat.items.length === 0) {
                html += `
                    <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border">
                        <td colspan="8" class="px-5 py-3 text-center text-table-subtle text-xs italic">
                            Belum ada item pekerjaan.
                        </td>
                    </tr>`;
            } else {
                cat.items.forEach(item => {
                    html += `
                        <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150">
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.no}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium max-w-0 truncate" title="${item.uraian}">${item.uraian}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums">${item.volume}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.satuan}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums">${fmt(item.hargaDasar)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong">${fmt(item.harga)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-muted">${pct(item.harga, grandTotal)}</td>
                            <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                                <button class="bg-primary hover:bg-primary-hover active:scale-95 text-white px-2.5 md:px-3.5 py-1 rounded-md text-[10px] md:text-xs font-medium transition-all duration-150 focus:outline-none">
                                    Detail
                                </button>
                            </td>
                        </tr>`;
                });
            }
        });

        tbody.innerHTML = html;
        updateTotals(grandTotal);
        bindCategoryToggle();
    }

    /* ============================================================
       RENDER — EDITABLE (category headers only + Add Item per cat)
    ============================================================ */
    function renderEditable(categories) {
        if (categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-table-subtle text-xs">Tidak ada kategori.</td></tr>`;
            updateTotals(0);
            return;
        }

        let html = '';

        categories.forEach(cat => {
            // ── Category header row (editable) ──
            html += `
                <tr class="rab-category bg-table-category text-white">
                    <!-- Col 1: Plus (open) / Minus (close) toggle — left column -->
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
                    <td colspan="6" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                            ${cat.name}
                        </span>
                    </td>
                    <!-- Col 8: Tambah + Hapus buttons (right) -->
                    <td class="px-2 md:px-3 py-2.5 md:py-3 text-center">
                        <div class="inline-flex items-center gap-1">
                            <button
                                class="add-subitem-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}" data-catname="${cat.name}" title="Tambah item">
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
                    <td colspan="8" class="px-5 py-2.5 text-center text-table-subtle text-xs italic">
                        Belum ada item — klik Tambah untuk menambahkan.
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
        updateTotals(0);
        bindAddSubItem();
        bindEditableCategoryToggle();
    }

    /* ============================================================
       FOOTER TOTALS
    ============================================================ */
    function updateTotals(total) {
        const ppn   = total * 0.11;
        const grand = total + ppn;
        if (totalJumlah) totalJumlah.textContent = fmt(total);
        if (totalPpn)    totalPpn.textContent    = fmt(ppn);
        if (totalFinal)  totalFinal.textContent  = fmt(grand);
    }

    /* ============================================================
       SHOW TABLE
    ============================================================ */
    function showTable() {
        wrapper.classList.remove('hidden');
        setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
    }

    /* ============================================================
       ACCORDION TOGGLE (readonly)
    ============================================================ */
    function bindCategoryToggle() {
        tbody.querySelectorAll('.rab-category[data-cat]').forEach(row => {
            row.addEventListener('click', function () {
                const catId   = row.dataset.cat;
                const subRows = tbody.querySelectorAll(`.subrow-${catId}`);
                const minus   = row.querySelector('.cat-icon-minus');
                const plus    = row.querySelector('.cat-icon-plus');
                const chevron = row.querySelector('.cat-chevron');
                const isHidden = subRows.length && subRows[0].classList.contains('hidden');

                // Show/hide sub-rows
                subRows.forEach(r => r.classList.toggle('hidden', !isHidden));

                // Swap plus ↔ minus
                if (minus)   minus.classList.toggle('hidden', !isHidden);
                if (plus)    plus.classList.toggle('hidden',   isHidden);

                // Rotate chevron: points up when open, down when closed
                if (chevron) chevron.classList.toggle('rotate-180', !isHidden);

                state.collapsed[catId] = !isHidden;
            });

            row.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); row.click(); }
            });
        });
    }

    /* ============================================================
       ADD SUBITEM BUTTON (editable)
       Stub — replace with modal / inline input form logic
    ============================================================ */
    function bindAddSubItem() {
        tbody.querySelectorAll('.add-subitem-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const catId      = btn.dataset.cat;
                const catName    = btn.dataset.catname;
                const placeholder = tbody.querySelector(`.subrow-placeholder-${catId}`);

                // Count existing items for this category
                const existing = tbody.querySelectorAll(`.subrow-item-${catId}`).length;
                const rowNo    = existing + 1;

                const newRow = document.createElement('tr');
                newRow.className = `subrow-item-${catId} bg-table-row border-b border-table-border`;
                newRow.innerHTML = `
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${rowNo}</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 max-w-0">
                        <input type="text" placeholder="Uraian pekerjaan…"
                            class="w-full px-2 py-1 text-xs border border-table-border rounded focus:outline-none focus:ring-1 focus:ring-primary/40 text-table-medium"/>
                    </td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                        <input type="number" value="1" min="0"
                            class="w-16 px-2 py-1 text-xs border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40"/>
                    </td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                        <input type="text" value="m²"
                            class="w-14 px-2 py-1 text-xs border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40"/>
                    </td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right">
                        <input type="number" value="0" min="0"
                            class="w-28 px-2 py-1 text-xs border border-table-border rounded text-right focus:outline-none focus:ring-1 focus:ring-primary/40"/>
                    </td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong">Rp 0</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-muted">—</td>
                    <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                        <button class="remove-row-btn text-red-400 hover:text-red-600 transition-colors p-1 rounded" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </td>`;

                // Remove placeholder row if first item
                if (existing === 0 && placeholder) placeholder.remove();

                // Insert before the category's Tambah button row
                const catHeaderRow = btn.closest('tr');
                catHeaderRow.after(newRow);

                // Bind remove button
                newRow.querySelector('.remove-row-btn').addEventListener('click', () => newRow.remove());

                // Focus uraian input
                newRow.querySelector('input[type="text"]').focus();
            });
        });

        // ── Trash (delete all items in category) ──
        tbody.querySelectorAll('.del-cat-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const catId     = btn.dataset.cat;
                const catHeader = btn.closest('tr');

                // Remove all added sub-item rows for this category
                tbody.querySelectorAll(`.subrow-item-${catId}`).forEach(r => r.remove());

                // Re-insert placeholder if it was removed
                if (!tbody.querySelector(`.subrow-placeholder-${catId}`)) {
                    const placeholder = document.createElement('tr');
                    placeholder.className = `subrow-placeholder-${catId} bg-table-row border-b border-table-border`;
                    placeholder.innerHTML = `<td colspan="8" class="px-5 py-2.5 text-center text-table-subtle text-xs italic">
                        Belum ada item — klik Tambah untuk menambahkan.
                    </td>`;
                    catHeader.after(placeholder);
                }
            });
        });
    }

    /* ============================================================
       EDITABLE CATEGORY TOGGLE (plus/minus in left column)
    ============================================================ */
    function bindEditableCategoryToggle() {
        tbody.querySelectorAll('.edit-cat-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const catId = btn.dataset.cat;
                const plus  = btn.querySelector('.edit-cat-icon-plus');
                const minus = btn.querySelector('.edit-cat-icon-minus');

                // Gather placeholder + any added item rows for this category
                const targets = tbody.querySelectorAll(
                    `.subrow-placeholder-${catId}, .subrow-item-${catId}`
                );

                // Currently open (rows visible) → close; currently closed → open
                const isOpen = targets.length && !targets[0].classList.contains('hidden');

                targets.forEach(r => r.classList.toggle('hidden', isOpen));

                // Plus shown when closed, minus shown when open
                if (plus)  plus.classList.toggle('hidden',  !isOpen);
                if (minus) minus.classList.toggle('hidden',  isOpen);
            });
        });
    }

    /* ============================================================
       CARD CLICK → readonly
    ============================================================ */
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

    /* ============================================================
       ADD RAB BUTTON → editable (category headers only)
    ============================================================ */
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

    /* ============================================================
       AUTO-INIT from window.RAB_INIT (set by menu-rap.php)
       Triggered when arriving from a dashboard card or Add RAB link.
    ============================================================ */
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

})();