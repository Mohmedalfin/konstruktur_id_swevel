/**
 * ajax_ahs.js — Rincian AHS (Input oleh Kontraktor)
 * Fitur:
 *  1. Input manual (Tambah Bahan / Alat / Upah)
 *  2. Autocomplete inline saat mengetik di kolom Uraian
 *  3. Modal "Pilih dari Daftar AHS" — search, filter tipe, multiselect
 */

(function () {

    'use strict';

    /* ============================================================
       DOM REFERENCES
    ============================================================ */
    const tbody          = document.getElementById('ahs-tbody');
    const itemLabel      = document.getElementById('ahs-item-label');
    const addBahanBtn    = document.getElementById('ahs-add-bahan-btn');
    const addAlatBtn     = document.getElementById('ahs-add-alat-btn');
    const addUpahBtn     = document.getElementById('ahs-add-upah-btn');
    const simpanBtn      = document.getElementById('ahs-simpan-btn');
    const totalBahanEl   = document.getElementById('ahs-total-bahan');
    const totalAlatEl    = document.getElementById('ahs-total-alat');
    const totalUpahEl    = document.getElementById('ahs-total-upah');
    const totalKeselEl   = document.getElementById('ahs-total-keseluruhan');

    // Modal elements
    const modalOverlay   = document.getElementById('ahs-modal-overlay');
    const modalClose     = document.getElementById('ahs-modal-close');
    const modalCancel    = document.getElementById('ahs-modal-cancel');
    const modalConfirm   = document.getElementById('ahs-modal-confirm');
    const modalSearch    = document.getElementById('ahs-modal-search');
    const modalTbody     = document.getElementById('ahs-modal-tbody');
    const modalCheckAll  = document.getElementById('ahs-modal-check-all');
    const modalCountEl   = document.getElementById('ahs-modal-selected-count');
    const fromDbBtn      = document.getElementById('ahs-from-db-btn');
    const filterBtns     = document.querySelectorAll('.ahs-modal-filter-btn');

    if (!tbody) return;

    /* ============================================================
       STATE
    ============================================================ */
    let rowCounter      = 0;
    let activeFilter    = 'all';
    let modalSelected   = new Set(); // ids dari DB AHS
    let autocompleteActive = null;   // input yang sedang punya autocomplete

    /* ============================================================
       DUMMY DATA — rows yang sudah ada di tabel AHS (editable)
    ============================================================ */
    const dummyRows = [
        { id: 1, tipe: 'bahan', uraian: 'Semen Portland',         koefisien: 7.275, satuan: 'sak',  hargaSatuan: 62000  },
        { id: 2, tipe: 'bahan', uraian: 'Pasir beton',            koefisien: 0.520, satuan: 'm³',   hargaSatuan: 185000 },
        { id: 3, tipe: 'bahan', uraian: 'Kerikil / split 2–3 cm', koefisien: 0.780, satuan: 'm³',   hargaSatuan: 210000 },
        { id: 4, tipe: 'alat',  uraian: 'Molen / concrete mixer', koefisien: 0.250, satuan: 'jam',  hargaSatuan: 180000 },
        { id: 5, tipe: 'alat',  uraian: 'Vibrator beton',         koefisien: 0.250, satuan: 'jam',  hargaSatuan: 95000  },
        { id: 6, tipe: 'upah',  uraian: 'Mandor',                 koefisien: 0.083, satuan: 'OH',   hargaSatuan: 120000 },
        { id: 7, tipe: 'upah',  uraian: 'Tukang batu',            koefisien: 0.275, satuan: 'OH',   hargaSatuan: 110000 },
        { id: 8, tipe: 'upah',  uraian: 'Pekerja',                koefisien: 0.825, satuan: 'OH',   hargaSatuan: 90000  },
    ];

    /* ============================================================
       DB AHS — sumber dari database (dummy), gabungan bahan/alat/upah
    ============================================================ */
    const ahsDatabase = [
        // Bahan
        { id: 'db-1',  tipe: 'bahan', uraian: 'Semen Portland (50 kg)',   satuan: 'sak',  hargaSatuan: 62000  },
        { id: 'db-2',  tipe: 'bahan', uraian: 'Semen Putih',              satuan: 'kg',   hargaSatuan: 4500   },
        { id: 'db-3',  tipe: 'bahan', uraian: 'Pasir beton',              satuan: 'm³',   hargaSatuan: 185000 },
        { id: 'db-4',  tipe: 'bahan', uraian: 'Pasir halus',              satuan: 'm³',   hargaSatuan: 165000 },
        { id: 'db-5',  tipe: 'bahan', uraian: 'Kerikil / split 2–3 cm',  satuan: 'm³',   hargaSatuan: 210000 },
        { id: 'db-6',  tipe: 'bahan', uraian: 'Bata merah 5x11x22 cm',   satuan: 'bh',   hargaSatuan: 900    },
        { id: 'db-7',  tipe: 'bahan', uraian: 'Besi beton polos D10',     satuan: 'kg',   hargaSatuan: 14500  },
        { id: 'db-8',  tipe: 'bahan', uraian: 'Besi beton ulir D16',      satuan: 'kg',   hargaSatuan: 15200  },
        { id: 'db-9',  tipe: 'bahan', uraian: 'Kawat beton',              satuan: 'kg',   hargaSatuan: 22000  },
        { id: 'db-10', tipe: 'bahan', uraian: 'Papan bekisting',          satuan: 'm²',   hargaSatuan: 95000  },
        { id: 'db-11', tipe: 'bahan', uraian: 'Multiplek 9mm',            satuan: 'lbr',  hargaSatuan: 185000 },
        { id: 'db-12', tipe: 'bahan', uraian: 'Cat tembok (5 kg)',        satuan: 'klg',  hargaSatuan: 115000 },
        // Alat
        { id: 'db-13', tipe: 'alat',  uraian: 'Molen / concrete mixer',  satuan: 'jam',  hargaSatuan: 180000 },
        { id: 'db-14', tipe: 'alat',  uraian: 'Vibrator beton',          satuan: 'jam',  hargaSatuan: 95000  },
        { id: 'db-15', tipe: 'alat',  uraian: 'Pompa air',               satuan: 'jam',  hargaSatuan: 75000  },
        { id: 'db-16', tipe: 'alat',  uraian: 'Stamper tanah',           satuan: 'jam',  hargaSatuan: 110000 },
        { id: 'db-17', tipe: 'alat',  uraian: 'Excavator',               satuan: 'jam',  hargaSatuan: 650000 },
        { id: 'db-18', tipe: 'alat',  uraian: 'Dump truck 8 ton',        satuan: 'rit',  hargaSatuan: 450000 },
        { id: 'db-19', tipe: 'alat',  uraian: 'Scaffolding (sewa)',       satuan: 'set',  hargaSatuan: 85000  },
        // Upah
        { id: 'db-20', tipe: 'upah',  uraian: 'Mandor',                  satuan: 'OH',   hargaSatuan: 120000 },
        { id: 'db-21', tipe: 'upah',  uraian: 'Kepala tukang batu',      satuan: 'OH',   hargaSatuan: 115000 },
        { id: 'db-22', tipe: 'upah',  uraian: 'Tukang batu',             satuan: 'OH',   hargaSatuan: 110000 },
        { id: 'db-23', tipe: 'upah',  uraian: 'Tukang besi',             satuan: 'OH',   hargaSatuan: 110000 },
        { id: 'db-24', tipe: 'upah',  uraian: 'Tukang kayu',             satuan: 'OH',   hargaSatuan: 108000 },
        { id: 'db-25', tipe: 'upah',  uraian: 'Tukang cat',              satuan: 'OH',   hargaSatuan: 100000 },
        { id: 'db-26', tipe: 'upah',  uraian: 'Pekerja',                 satuan: 'OH',   hargaSatuan: 90000  },
        { id: 'db-27', tipe: 'upah',  uraian: 'Pekerja terampil',        satuan: 'OH',   hargaSatuan: 95000  },
    ];

    /* ============================================================
       FORMAT HELPERS
    ============================================================ */
    const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2 });

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /* ============================================================
       TIPE CONFIG
    ============================================================ */
    const tipeConfig = {
        bahan: { label: 'Bahan', badge: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300' },
        alat:  { label: 'Alat',  badge: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300'          },
        upah:  { label: 'Upah',  badge: 'bg-violet-100 text-violet-700 ring-1 ring-violet-300'    },
    };

    /* ============================================================
       RENDER TABLE ROW (main table)
    ============================================================ */
    function renderRow(rowData, isNew = false) {
        rowCounter++;
        const cfg    = tipeConfig[rowData.tipe] || tipeConfig.bahan;
        const jumlah = (parseFloat(rowData.koefisien) || 0) * (parseFloat(rowData.hargaSatuan) || 0);
        const tr     = document.createElement('tr');
        tr.dataset.id   = rowData.id;
        tr.dataset.tipe = rowData.tipe;
        tr.className    = 'ahs-row border-b border-table-border hover:bg-slate-50 transition-colors duration-100';

        tr.innerHTML = `
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-center text-table-subtle">
                <span class="ahs-rownum">${rowCounter}</span>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] md:text-[11px] font-semibold ${cfg.badge}">
                    ${cfg.label}
                </span>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 relative">
                <input type="text" value="${escHtml(rowData.uraian)}"
                    placeholder="Nama bahan / alat / pekerja"
                    class="ahs-uraian w-full bg-transparent border-b border-transparent hover:border-table-border focus:border-primary text-[11px] md:text-[13px] text-table-medium placeholder-table-subtle focus:outline-none transition-colors py-0.5"
                    data-id="${rowData.id}" autocomplete="off"/>
                <ul class="ahs-autocomplete hidden absolute left-0 right-0 top-full mt-1 bg-white border border-table-border rounded-lg shadow-xl z-30 max-h-48 overflow-y-auto text-[12px]"></ul>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
                <input type="number" min="0" step="any" value="${rowData.koefisien}"
                    class="ahs-koef w-20 px-2 py-1 text-[11px] md:text-[13px] border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary tabular-nums bg-white"
                    data-id="${rowData.id}"/>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
                <input type="text" value="${escHtml(rowData.satuan)}" placeholder="m³"
                    class="ahs-satuan w-16 px-2 py-1 text-[11px] md:text-[13px] border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary bg-white"
                    data-id="${rowData.id}"/>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-right">
                <input type="number" min="0" step="any" value="${rowData.hargaSatuan}"
                    class="ahs-harga-satuan w-32 px-2 py-1 text-[11px] md:text-[13px] border border-table-border rounded text-right focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary tabular-nums bg-white"
                    data-id="${rowData.id}"/>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong text-[11px] md:text-[13px]">
                <span class="ahs-jumlah-cell">${fmt(jumlah)}</span>
            </td>
            <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
                <button type="button" class="ahs-del-btn inline-flex items-center justify-center w-6 h-6 rounded-md text-table-subtle hover:text-red-500 hover:bg-red-50 transition-colors focus:outline-none" title="Hapus">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>`;

        tbody.appendChild(tr);
        bindRowInputs(tr);
        if (isNew) setTimeout(() => tr.querySelector('.ahs-uraian')?.focus(), 50);
    }

    /* ============================================================
       BIND ROW INPUTS
    ============================================================ */
    function bindRowInputs(tr) {
        const koefInput  = tr.querySelector('.ahs-koef');
        const hargaInput = tr.querySelector('.ahs-harga-satuan');
        const jumlahCell = tr.querySelector('.ahs-jumlah-cell');
        const uraianInput = tr.querySelector('.ahs-uraian');
        const acList      = tr.querySelector('.ahs-autocomplete');
        const tipe        = tr.dataset.tipe;

        function recalcRow() {
            const koef = parseFloat(koefInput?.value) || 0;
            const harga = parseFloat(hargaInput?.value) || 0;
            if (jumlahCell) jumlahCell.textContent = fmt(koef * harga);
            recalcTotals();
        }
        koefInput?.addEventListener('input', recalcRow);
        hargaInput?.addEventListener('input', recalcRow);

        // Delete
        tr.querySelector('.ahs-del-btn')?.addEventListener('click', function () {
            tr.remove();
            renumberRows();
            recalcTotals();
        });

        // ── Autocomplete ──────────────────────────────────────────
        uraianInput?.addEventListener('input', function () {
            const q = uraianInput.value.trim().toLowerCase();
            if (!q) { hideAutocomplete(acList); return; }

            const matches = ahsDatabase.filter(item =>
                item.tipe === tipe && item.uraian.toLowerCase().includes(q)
            ).slice(0, 8);

            if (matches.length === 0) { hideAutocomplete(acList); return; }

            acList.innerHTML = matches.map(m => `
                <li class="flex items-center justify-between px-3 py-2 hover:bg-primary/5 cursor-pointer transition-colors gap-2"
                    data-uraian="${escHtml(m.uraian)}"
                    data-satuan="${escHtml(m.satuan)}"
                    data-harga="${m.hargaSatuan}">
                    <span class="flex-1 text-table-medium truncate">${escHtml(m.uraian)}</span>
                    <span class="text-table-subtle shrink-0">${escHtml(m.satuan)} · ${fmt(m.hargaSatuan)}</span>
                </li>`).join('');

            acList.querySelectorAll('li').forEach(li => {
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault(); // jangan trigger blur dulu
                    uraianInput.value = li.dataset.uraian;
                    if (tr.querySelector('.ahs-satuan'))
                        tr.querySelector('.ahs-satuan').value = li.dataset.satuan;
                    if (hargaInput) hargaInput.value = li.dataset.harga;
                    recalcRow();
                    hideAutocomplete(acList);
                });
            });

            acList.classList.remove('hidden');
            autocompleteActive = acList;
        });

        uraianInput?.addEventListener('blur', function () {
            setTimeout(() => hideAutocomplete(acList), 150);
        });
        uraianInput?.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideAutocomplete(acList);
        });
    }

    function hideAutocomplete(list) {
        if (list) list.classList.add('hidden');
        autocompleteActive = null;
    }

    /* ============================================================
       RECALC TOTALS
    ============================================================ */
    function recalcTotals() {
        const t = { bahan: 0, alat: 0, upah: 0 };
        tbody.querySelectorAll('.ahs-row').forEach(function (tr) {
            const tipe  = tr.dataset.tipe;
            const koef  = parseFloat(tr.querySelector('.ahs-koef')?.value) || 0;
            const harga = parseFloat(tr.querySelector('.ahs-harga-satuan')?.value) || 0;
            if (t[tipe] !== undefined) t[tipe] += koef * harga;
        });
        if (totalBahanEl) totalBahanEl.textContent = fmt(t.bahan);
        if (totalAlatEl)  totalAlatEl.textContent  = fmt(t.alat);
        if (totalUpahEl)  totalUpahEl.textContent  = fmt(t.upah);
        if (totalKeselEl) totalKeselEl.textContent = fmt(t.bahan + t.alat + t.upah);
    }

    function renumberRows() {
        let n = 0;
        tbody.querySelectorAll('.ahs-row .ahs-rownum').forEach(el => el.textContent = ++n);
        rowCounter = n;
    }

    function addRow(tipe) {
        document.getElementById('ahs-empty-row')?.remove();
        renderRow({ id: Date.now(), tipe, uraian: '', koefisien: 1, satuan: '', hargaSatuan: 0 }, true);
        recalcTotals();
    }

    /* ============================================================
       MODAL — Pilih dari Daftar AHS
    ============================================================ */
    function openModal() {
        if (!modalOverlay) return;
        modalSelected.clear();
        updateModalCount();
        renderModalRows(ahsDatabase);
        modalOverlay.classList.remove('hidden');
        modalOverlay.classList.add('flex');
        setTimeout(() => modalSearch?.focus(), 100);
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.add('hidden');
        modalOverlay.classList.remove('flex');
        if (modalSearch) modalSearch.value = '';
        if (modalCheckAll) modalCheckAll.checked = false;
        activeFilter = 'all';
        syncFilterButtons();
    }

    function renderModalRows(items) {
        if (!modalTbody) return;
        if (items.length === 0) {
            modalTbody.innerHTML = `
                <tr><td colspan="5" class="text-center py-8 text-table-subtle text-xs italic">
                    Tidak ada item ditemukan.
                </td></tr>`;
            return;
        }
        modalTbody.innerHTML = items.map(item => {
            const cfg     = tipeConfig[item.tipe] || tipeConfig.bahan;
            const checked = modalSelected.has(item.id);
            return `
            <tr class="modal-item-row border-b border-table-border hover:bg-slate-50 transition-colors cursor-pointer ${checked ? 'bg-primary/5' : ''}"
                data-id="${item.id}">
                <td class="px-4 py-2.5 text-center">
                    <input type="checkbox" class="modal-item-cb w-3.5 h-3.5 rounded accent-primary cursor-pointer"
                        data-id="${item.id}" ${checked ? 'checked' : ''}/>
                </td>
                <td class="px-4 py-2.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold ${cfg.badge}">
                        ${cfg.label}
                    </span>
                </td>
                <td class="px-4 py-2.5 text-[12px] text-table-medium">${escHtml(item.uraian)}</td>
                <td class="px-4 py-2.5 text-center text-[12px] text-table-subtle">${escHtml(item.satuan)}</td>
                <td class="px-4 py-2.5 text-right text-[12px] tabular-nums text-table-strong">${fmt(item.hargaSatuan)}</td>
            </tr>`;
        }).join('');

        // Bind row click + checkbox
        modalTbody.querySelectorAll('.modal-item-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.type === 'checkbox') return;
                const cb = row.querySelector('.modal-item-cb');
                if (cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
            });
        });
        modalTbody.querySelectorAll('.modal-item-cb').forEach(cb => {
            cb.addEventListener('change', function () {
                const id = cb.dataset.id;
                if (cb.checked) {
                    modalSelected.add(id);
                    cb.closest('tr')?.classList.add('bg-primary/5');
                } else {
                    modalSelected.delete(id);
                    cb.closest('tr')?.classList.remove('bg-primary/5');
                }
                updateModalCount();
            });
        });
    }

    function updateModalCount() {
        const n = modalSelected.size;
        if (modalCountEl) modalCountEl.textContent = n > 0 ? `${n} item dipilih` : 'Belum ada item dipilih';
        if (modalConfirm) modalConfirm.disabled = n === 0;
    }

    function filterAndSearch() {
        const q = (modalSearch?.value || '').trim().toLowerCase();
        const filtered = ahsDatabase.filter(item => {
            const matchTipe  = activeFilter === 'all' || item.tipe === activeFilter;
            const matchQuery = !q || item.uraian.toLowerCase().includes(q);
            return matchTipe && matchQuery;
        });
        renderModalRows(filtered);
    }

    function syncFilterButtons() {
        filterBtns.forEach(btn => {
            const isActive = btn.dataset.filter === activeFilter;
            btn.className = 'ahs-modal-filter-btn px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 focus:outline-none ' +
                (isActive
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white border border-table-border text-table-body hover:bg-slate-50');
        });
    }

    function confirmModalSelection() {
        document.getElementById('ahs-empty-row')?.remove();
        const selectedItems = ahsDatabase.filter(item => modalSelected.has(item.id));
        selectedItems.forEach(item => {
            renderRow({
                id: Date.now() + Math.random(),
                tipe: item.tipe,
                uraian: item.uraian,
                koefisien: 1,
                satuan: item.satuan,
                hargaSatuan: item.hargaSatuan,
            });
        });
        recalcTotals();
        closeModal();
    }

    /* ============================================================
       INIT
    ============================================================ */
    document.addEventListener('DOMContentLoaded', function () {

        // Label item BOQ dari sessionStorage
        try {
            const namaItem = sessionStorage.getItem('ahs_item_label') || '—';
            if (itemLabel) itemLabel.textContent = namaItem.toUpperCase();
        } catch (_) {}

        // Render dummy rows
        dummyRows.length === 0
            ? (tbody.innerHTML = `<tr id="ahs-empty-row"><td colspan="8" class="text-center py-10 text-table-subtle text-xs italic">Belum ada rincian AHS. Tambahkan item untuk memulai.</td></tr>`)
            : dummyRows.forEach(r => renderRow(r));
        recalcTotals();

        // ── Toolbar event handlers ──
        addBahanBtn?.addEventListener('click', () => addRow('bahan'));
        addAlatBtn?.addEventListener('click',  () => addRow('alat'));
        addUpahBtn?.addEventListener('click',  () => addRow('upah'));
        fromDbBtn?.addEventListener('click',   () => openModal());

        // Modal open/close
        modalClose?.addEventListener('click',   closeModal);
        modalCancel?.addEventListener('click',  closeModal);
        modalOverlay?.addEventListener('click', function (e) {
            if (e.target === modalOverlay) closeModal();
        });

        // Modal confirm
        modalConfirm?.addEventListener('click', confirmModalSelection);

        // Modal search
        modalSearch?.addEventListener('input', filterAndSearch);

        // Modal filter buttons
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                activeFilter = btn.dataset.filter;
                syncFilterButtons();
                filterAndSearch();
            });
        });
        syncFilterButtons();

        // Check all
        modalCheckAll?.addEventListener('change', function () {
            const visible = modalTbody?.querySelectorAll('.modal-item-cb') || [];
            visible.forEach(cb => {
                cb.checked = modalCheckAll.checked;
                const id = cb.dataset.id;
                if (modalCheckAll.checked) {
                    modalSelected.add(id);
                    cb.closest('tr')?.classList.add('bg-primary/5');
                } else {
                    modalSelected.delete(id);
                    cb.closest('tr')?.classList.remove('bg-primary/5');
                }
            });
            updateModalCount();
        });

        // Simpan
        simpanBtn?.addEventListener('click', function () {
            const payload = [];
            tbody.querySelectorAll('.ahs-row').forEach(tr => {
                payload.push({
                    tipe:        tr.dataset.tipe,
                    uraian:      tr.querySelector('.ahs-uraian')?.value || '',
                    koefisien:   parseFloat(tr.querySelector('.ahs-koef')?.value) || 0,
                    satuan:      tr.querySelector('.ahs-satuan')?.value || '',
                    hargaSatuan: parseFloat(tr.querySelector('.ahs-harga-satuan')?.value) || 0,
                });
            });
            console.info('[AHS Simpan]', payload);
            alert('Data AHS berhasil dikumpulkan (' + payload.length + ' baris).\nEndpoint CI4 segera diimplementasi.');
        });

        // Tutup autocomplete jika klik di luar
        document.addEventListener('click', function (e) {
            if (autocompleteActive && !e.target.closest('.ahs-uraian') && !e.target.closest('.ahs-autocomplete')) {
                hideAutocomplete(autocompleteActive);
            }
        });
    });

})();
