/**
 * components/render.js
 * All rendering functions: loading spinner, readonly table, editable empty state,
 * totals, table visibility, editable mode toggle, and bind helpers for readonly mode.
 */

import { state, tbody, wrapper, searchInput, tambahKategoriBtn, totalJumlah, totalPpn, totalFinal } from '../core/state.js';
import { fmt, escHtml }   from '../../shared/utils.js';
import { confirmDelete }  from '../../shared/ui/confirm.js';
import { toast }          from '../../shared/ui/toast.js';
import { bindAddSubItemRow, bindToggleRow } from './categories.js?v=4';

export function renderLoading() {
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

export function renderReadonly(data) {
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
                        <svg class="cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? '' : 'hidden'}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
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
                </td>
            </tr>`;

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

export function renderEditable() {
    tbody.innerHTML = `<tr id="rab-tbody-empty">
        <td colspan="12" class="text-center py-14 text-table-subtle text-xs">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Belum ada kategori pekerjaan. Klik <strong>+ Kategori Pekerjaan</strong> untuk memulai.
        </td>
    </tr>`;
    updateTotals(0);
}

export function updateTotals(total) {
    const ppn   = total * 0.11;
    const grand = total + ppn;
    if (totalJumlah) totalJumlah.textContent = fmt(total);
    if (totalPpn)    totalPpn.textContent    = fmt(ppn);
    if (totalFinal)  totalFinal.textContent  = fmt(grand);
}

export function showTable() {
    wrapper.classList.remove('hidden');
    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    }
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
}

export function setEditableMode(on) {
    if (tambahKategoriBtn) {
        tambahKategoriBtn.classList.toggle('hidden', !on);
    }
}

export function bindCategoryToggle() {
    tbody.querySelectorAll('.rab-category[data-cat]').forEach(row => {
        row.addEventListener('click', function () {
            const catId    = row.dataset.cat;
            const subRows  = tbody.querySelectorAll(`.subrow-${catId}`);
            const minus    = row.querySelector('.cat-icon-minus');
            const plus     = row.querySelector('.cat-icon-plus');
            const chevron  = row.querySelector('.cat-chevron');
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

export function bindReadonlyDropdowns() {
    tbody.querySelectorAll('.readonly-item-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            window.location.href = btn.dataset.url || '/menu-rap/rincian-ahs';
        });
    });
    tbody.querySelectorAll('.readonly-item-delete').forEach(btn => {
        btn.addEventListener('click', async function () {
            // Get the item name from the row for a clearer dialog message
            const row      = btn.closest('tr');
            const itemName = row?.querySelector('td:nth-child(2)')?.textContent.trim() || 'item ini';

            const confirmed = await confirmDelete(itemName);
            if (!confirmed) return;

            if (row) row.remove();

            let total = 0;
            tbody.querySelectorAll('[class*="rab-harga-cell-"]').forEach(cell => {
                const val = cell.textContent.replace(/[^\d,]/g, '').replace(',', '.');
                total += parseFloat(val) || 0;
            });
            updateTotals(total);
            toast.show(`Pekerjaan berhasil dihapus dari RAB`, 'info', 2500);
        });
    });
}

// ─────────────────────────────────────────────────────────────────────────────
// renderRapFromDB — render data yang sudah tersimpan di DB (GET /api/rap)
// Dipakai saat halaman RAB dibuka / di-refresh untuk menampilkan data persisten
// ─────────────────────────────────────────────────────────────────────────────
export function renderRapFromDB(apiData) {
    const groups = apiData?.data ?? [];

    state.activeCategories = [];

    if (groups.length === 0) {
        tbody.innerHTML = `<tr id="rab-tbody-empty"><td colspan="12" class="text-center py-12 text-table-subtle text-xs italic">Belum ada pekerjaan. Klik <strong>+ Kategori Pekerjaan</strong> untuk memulai.</td></tr>`;
        updateTotals(0);
        return;
    }

    let html       = '';
    let grandTotal = 0;
    let rowNum     = 0;

    groups.forEach(group => {
        const catId   = group.id_kategori  ?? 0;
        const catName = group.nama_kategori ?? 'Tanpa Kategori';

        // Hitung total kategori dari sum sub-total keseluruhan
        const catTotal = group.items.reduce((sum, item) => {
            const hargaBahan = item.harga_bahan * item.volume;
            const hargaAlat  = item.harga_alat  * item.volume;
            const hargaUpah  = item.harga_upah  * item.volume;
            return sum + hargaBahan + hargaAlat + hargaUpah;
        }, 0);
        grandTotal += catTotal;

        // Register to activeCategories so it won't be duplicated in modal
        if (catId) {
            state.activeCategories.push({
                id: `db_${catId}`,
                nama: catName,
                db_id: catId
            });
        }

        // Header kategori
        html += `
        <tr class="rab-category bg-table-category text-white select-none" data-cat="${catId}">
            <td class="w-12 md:w-14 px-3 md:px-5 py-2.5 md:py-3 text-center">
                <button class="edit-cat-toggle-btn relative flex items-center justify-center w-5 h-5 mx-auto focus:outline-none"
                    data-cat="${catId}" title="Buka / Tutup">
                    <svg class="edit-cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 hidden"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg class="edit-cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
            </td>
            <td colspan="10" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                <div class="flex items-center justify-between w-full">
                    <span class="flex items-center gap-2">
                        <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                        ${escHtml(catName)}
                    </span>
                    <span class="ml-auto text-xs font-semibold tabular-nums opacity-80">${fmt(catTotal)}</span>
                </div>
            </td>
            <td class="px-2 md:px-3 py-2.5 md:py-3 text-center">
                <div class="inline-flex items-center gap-1">
                    <button class="add-subitem-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 text-white transition-colors duration-150 focus:outline-none"
                        data-cat="${catId}" data-catname="${escHtml(catName)}" data-dbid="${catId === 0 ? '' : catId}" title="Tambah AHS">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    <!-- Tombol Hapus Kategori -->
                    <button class="del-cat-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/80 text-white/70 hover:text-white transition-colors duration-150 focus:outline-none pointer-events-auto"
                        data-cat="${catId}" data-catname="${escHtml(catName)}" title="Hapus Kategori (dan seluruh item di dalamnya)">
                        <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>`;

        // Baris item per kategori
        group.items.forEach(item => {
            rowNum++;
            const vol        = item.volume      || 0;
            const hargaBahan = item.harga_bahan || 0;
            const hargaAlat  = item.harga_alat  || 0;
            const hargaUpah  = item.harga_upah  || 0;
            const subBahan   = hargaBahan * vol;
            const subAlat    = hargaAlat  * vol;
            const subUpah    = hargaUpah  * vol;
            const total      = subBahan + subAlat + subUpah;

            html += `
            <tr class="subrow-item-${catId} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150" data-id-rap="${item.id_rap}">
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${rowNum}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium min-w-[250px] whitespace-normal leading-relaxed">${escHtml(item.nama_pekerjaan)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                    <input type="number" min="0" step="0.01"
                        class="rab-volume-db w-20 text-center text-xs px-2 py-1 border border-table-border rounded focus:outline-none focus:border-primary"
                        value="${vol}" data-id-rap="${item.id_rap}">
                </td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${escHtml(item.satuan)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums">${fmt(hargaBahan)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums">${fmt(hargaAlat)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums">${fmt(hargaUpah)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-medium">${fmt(subBahan)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-medium">${fmt(subAlat)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-medium">${fmt(subUpah)}</td>
                <td class="rab-harga-cell-db-${item.id_rap} px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong">${fmt(total)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                    <div class="hs-dropdown relative inline-flex">
                        <button type="button"
                            class="hs-dropdown-toggle inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle transition-colors focus:outline-none"
                            title="Opsi">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <div class="hs-dropdown-menu hidden z-50 mt-1 w-44 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 end-0" role="menu">
                            <button type="button" class="rap-rincian-btn flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition-colors"
                                data-id-rap="${item.id_rap}" data-nama="${escHtml(item.nama_pekerjaan)}">
                                <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Rincian AHS
                            </button>
                            <div class="border-t border-table-border my-1"></div>
                            <button type="button" class="rap-del-btn flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition-colors"
                                data-id-rap="${item.id_rap}" data-nama="${escHtml(item.nama_pekerjaan)}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus dari RAP
                            </button>
                        </div>
                    </div>
                </td>
            </tr>`;
        });
    });

    tbody.innerHTML = html;
    updateTotals(grandTotal);

    // Bind kategori headers yang baru di render dari DB
    tbody.querySelectorAll('.rab-category').forEach(tr => {
        bindAddSubItemRow(tr);
        bindToggleRow(tr);
    });

    // ── Bind events setelah innerHTML ──────────────────────────────────────
    // Volume input → auto-save ke DB
    tbody.querySelectorAll('.rab-volume-db').forEach(input => {
        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                const idRap = input.dataset.idRap;
                const vol   = parseFloat(input.value) || 0;
                if (!idRap) return;
                try {
                    const { updateRapVolume } = await import('../core/rap-data.js');
                    await updateRapVolume(idRap, vol);
                    // Update subtotals di baris ini
                    const row        = input.closest('tr');
                    const hargaSels  = row.querySelectorAll('td');
                    // Recalculate totals
                    let grandT = 0;
                    tbody.querySelectorAll('[class*="rab-harga-cell-db-"]').forEach(c => {
                        grandT += parseFloat(c.textContent.replace(/[^0-9.]/g,'')) || 0;
                    });
                    updateTotals(grandT);
                } catch (e) { console.warn('Gagal update volume:', e); }
            }, 600);
        });
    });

    // Hapus item → DELETE API
    tbody.querySelectorAll('.rap-del-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const idRap = btn.dataset.idRap;
            const nama  = btn.dataset.nama || 'pekerjaan ini';
            const confirmed = await confirmDelete(nama);
            if (!confirmed) return;
            try {
                const { deleteRapItem } = await import('../core/rap-data.js');
                await deleteRapItem(idRap);
                btn.closest('tr').remove();
                let grandT = 0;
                tbody.querySelectorAll('[class*="rab-harga-cell-db-"]').forEach(c => {
                    grandT += parseFloat(c.textContent.replace(/[^0-9.]/g,'')) || 0;
                });
                updateTotals(grandT);
                toast.show(`"${nama}" dihapus dari RAP`, 'info', 2500);
            } catch (e) {
                toast.show('Gagal menghapus item', 'error', 2500);
            }
        });
    });

    // Hapus kategori beserta seluruh anak-anak itemnya
    tbody.querySelectorAll('.del-cat-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation(); // Mencegah toggle expand/collapse row
            
            const catId = btn.dataset.cat;
            const catName = btn.dataset.catname || 'kategori ini';
            const subRows = tbody.querySelectorAll(`.subrow-item-${catId}`);
            
            const confirmed = await confirmDelete(`Kategori "${catName}" beserta ${subRows.length} item pekerjaan di dalamnya`);
            if (!confirmed) return;

            btn.innerHTML = `<svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8H4z"></path></svg>`;
            
            try {
                const { deleteRapItem } = await import('../core/rap-data.js');
                
                // Hapus item satu per satu (sekuensial untuk menghindari server overload/rate limit)
                for (let i = 0; i < subRows.length; i++) {
                    const idRap = subRows[i].dataset.idRap;
                    if (idRap) await deleteRapItem(idRap);
                }
                
                // Hapus dari UI
                btn.closest('.rab-category').remove();
                subRows.forEach(r => r.remove());
                
                // Hapus dari state agar dropdown tambah kategori membukanya kembali
                state.activeCategories = state.activeCategories.filter(c => String(c.db_id) !== String(catId));
                
                // Cek jika tabel kosong sama sekali
                if (tbody.querySelectorAll('.rab-category').length === 0) {
                     renderRapFromDB({ data: [] });
                     toast.show(`Kategori "${catName}" dihapus`, 'info', 2500);
                     return;
                }

                // Kalkulasi ulang Total
                let grandT = 0;
                tbody.querySelectorAll('[class*="rab-harga-cell-db-"]').forEach(c => {
                    grandT += parseFloat(c.textContent.replace(/[^0-9.]/g,'')) || 0;
                });
                updateTotals(grandT);
                
                toast.show(`Kategori "${catName}" beserta isinya berhasil dihapus`, 'info', 2500);
            } catch (err) {
                console.error('Gagal hapus kategori:', err);
                toast.show('Pengahapusan kategori gagal', 'error', 3000);
                btn.innerHTML = `<svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;
            }
        });
    });

    // Rincian AHS
    tbody.querySelectorAll('.rap-rincian-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            try {
                sessionStorage.setItem('ahs_id_rap',    btn.dataset.idRap);
                sessionStorage.setItem('ahs_item_label', btn.dataset.nama);
                sessionStorage.setItem('rab_return_url', window.location.href);
            } catch (_) {}
            window.location.href = (window.RAB_INIT?.rincianAhsUrl) || '/menu-rap/rincian-ahs';
        });
    });

    try { window.HSStaticMethods?.autoInit(['dropdown']); } catch (_) {}
}

