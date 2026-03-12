/**
 * hooks/pending.js
 * Stateful logic for injecting pending AHS items (from sessionStorage) into the
 * RAB table after returning from the Tambah AHS page, and restoring category rows.
 */

import { state, tbody }    from '../core/state.js';
import { fmt, escHtml }    from '../../shared/utils.js';
import { updateTotals }    from '../components/render.js';
import { appendCategoryRow } from '../components/categories.js';
import { confirmDelete }   from '../../shared/ui/confirm.js';
import { toast }           from '../../shared/ui/toast.js';

export function restorePendingCategories() {
    let groups = [];
    try {
        const raw = sessionStorage.getItem('rab_pending_items');
        if (raw) groups = JSON.parse(raw);
    } catch (_) { return; }
    if (!groups || groups.length === 0) return;

    groups.forEach(function (group) {
        const catId   = group.catId;
        const catName = group.catName || catId;
        if (!catId) return;
        if (state.activeCategories.some(c => c.id === catId)) return;
        const cat = { id: catId, nama: catName };
        state.activeCategories.push(cat);
        appendCategoryRow(cat);
    });

    injectPendingItems();
}

export function injectPendingItems() {
    let groups = [];
    try {
        const raw = sessionStorage.getItem('rab_pending_items');
        if (raw) groups = JSON.parse(raw);
    } catch (_) { return; }
    if (!groups || groups.length === 0) return;

    groups.forEach(function (group) {
        const catId = group.catId;
        const items = group.items || [];
        if (items.length === 0) return;

        const placeholder = tbody.querySelector('.subrow-placeholder-' + catId);
        if (placeholder) placeholder.remove();

        const catHeader  = tbody.querySelector('.rab-category [data-cat="' + catId + '"]');
        const anchorRow  = catHeader ? catHeader.closest('tr') : null;

        tbody.querySelectorAll('.subrow-item-' + catId).forEach(r => r.remove());

        let rowNum       = 0;
        let lastInserted = anchorRow;

        items.forEach(function (item) {
            rowNum++;
            const hargaBahan = parseFloat(item.hargaBahan) || 0;
            const hargaAlat  = parseFloat(item.hargaAlat)  || 0;
            const hargaUpah  = parseFloat(item.hargaUpah)  || 0;
            const hargaKsl   = parseFloat(item.hargaKeseluruhan) || (hargaBahan + hargaAlat + hargaUpah);

            const itemRow = document.createElement('tr');
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

            itemRow.querySelector('.del-pending-item').addEventListener('click', async () => {
                const itemName = item.nama || 'pekerjaan ini';
                const confirmed = await confirmDelete(itemName);
                if (!confirmed) return;

                itemRow.remove();
                recomputePendingTotals();
                toast.show(`"${itemName}" berhasil dihapus dari RAB`, 'info', 2500);
            });
            itemRow.querySelector('.pending-item-edit').addEventListener('click', () => {
                try {
                    sessionStorage.setItem('ahs_item_label', item.nama || '');
                    sessionStorage.setItem('rab_return_url', window.location.href);
                } catch (_) {}
                window.location.href = (window.RAB_INIT && window.RAB_INIT.rincianAhsUrl)
                    ? window.RAB_INIT.rincianAhsUrl
                    : '/menu-rap/rincian-ahs';
            });
        });
    });

    recomputePendingTotals();
    try { sessionStorage.removeItem('rab_pending_items'); } catch (_) {}
    try { window.HSStaticMethods?.autoInit(['dropdown']); } catch (_) {}
}

export function recomputePendingTotals() {
    let total = 0;
    tbody.querySelectorAll('[class*="rab-harga-cell-"]').forEach(cell => {
        const val = cell.textContent.replace(/[^0-9,]/g, '').replace(',', '.');
        total += parseFloat(val) || 0;
    });
    updateTotals(total);
}
