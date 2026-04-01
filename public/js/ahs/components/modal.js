/**
 * ahs/components/modal.js
 * "Pilih dari Daftar AHS" modal — open/close, rendering, search, filter, infinite scroll, check-all.
 */

import { state, modalOverlay, modalClose, modalCancel, modalConfirm,
         modalSearch, modalTbody, modalCheckAll, modalCountEl, filterBtns } from '../core/state.js';
import { fetchAhsDatabase } from '../core/data.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { toast } from '../../shared/ui/toast.js';
import { tipeConfig, renderRow, recalcTotals } from './render.js';

export function openModal() {
    if (!modalOverlay) return;
    state.modalSelected.clear();
    updateModalCount();

    state.currentPage  = 1;
    state.hasMoreData  = true;
    state.ahsDatabase  = [];
    if (modalSearch) modalSearch.value = '';
    state.activeFilter = 'all';
    syncFilterButtons();

    modalTbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-table-subtle text-xs italic">Memuat data...</td></tr>';
    modalOverlay.classList.remove('hidden');
    modalOverlay.classList.add('flex');

    fetchAhsDatabase(1, '', false).then(() => {
        renderModalRows(state.ahsDatabase);
        setTimeout(() => modalSearch?.focus(), 100);
    });
}

export function closeModal() {
    if (!modalOverlay) return;
    modalOverlay.classList.add('hidden');
    modalOverlay.classList.remove('flex');
    if (modalSearch)   modalSearch.value   = '';
    if (modalCheckAll) modalCheckAll.checked = false;
    state.activeFilter = 'all';
    syncFilterButtons();
}

export function renderModalRows(items) {
    if (!modalTbody) return;
    if (items.length === 0) {
        modalTbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-table-subtle text-xs italic">Tidak ada item ditemukan.</td></tr>`;
        return;
    }

    modalTbody.innerHTML = items.map(item => {
        const cfg     = tipeConfig[item.tipe] || tipeConfig.bahan;
        const checked = state.modalSelected.has(item._uid);
        return `
        <tr class="modal-item-row border-b border-table-border hover:bg-slate-50 transition-colors cursor-pointer ${checked ? 'bg-primary/5' : ''}"
            data-uid="${item._uid}">
            <td class="px-4 py-2.5 text-center">
                <input type="checkbox" class="modal-item-cb w-3.5 h-3.5 rounded accent-primary cursor-pointer"
                    data-uid="${item._uid}" ${checked ? 'checked' : ''}/>
            </td>
            <td class="px-4 py-2.5 text-center text-[10px] md:text-[11px] font-semibold text-table-subtle whitespace-nowrap">${escHtml(item.id)}</td>
            <td class="px-4 py-2.5 text-[12px] text-table-medium">${escHtml(item.uraian)}</td>
            <td class="px-4 py-2.5 text-[12px] text-table-medium whitespace-nowrap">${escHtml(item.merk || '-')}</td>
            <td class="px-4 py-2.5 text-[12px] text-table-medium">${escHtml(item.spesifikasi || '-')}</td>
            <td class="px-4 py-2.5 text-center text-[12px] text-table-subtle whitespace-nowrap">${escHtml(item.satuan)}</td>
            <td class="px-4 py-2.5 text-right text-[12px] tabular-nums text-table-strong whitespace-nowrap">${fmt(item.hargaSatuan)}</td>
            <td class="px-4 py-2.5 text-[12px] text-table-medium whitespace-nowrap" style="min-width:12rem">${escHtml(item.sumber || '-')}</td>
        </tr>`;
    }).join('');

    modalTbody.querySelectorAll('.modal-item-row').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.type === 'checkbox') return;
            const cb = row.querySelector('.modal-item-cb');
            if (cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
        });
    });
    modalTbody.querySelectorAll('.modal-item-cb').forEach(cb => {
        cb.addEventListener('change', function () {
            const uid = cb.dataset.uid;
            if (cb.checked) {
                const itemData = state.ahsDatabase.find(x => x._uid === uid);
                if (itemData) state.modalSelected.set(uid, itemData);
                cb.closest('tr')?.classList.add('bg-primary/5');
            } else {
                state.modalSelected.delete(uid);
                cb.closest('tr')?.classList.remove('bg-primary/5');
            }
            updateModalCount();
        });
    });
}

export function updateModalCount() {
    const n = state.modalSelected.size;
    if (modalCountEl) modalCountEl.textContent = n > 0 ? `${n} item dipilih` : 'Belum ada item dipilih';
    if (modalConfirm) modalConfirm.disabled    = n === 0;
}

export function syncFilterButtons() {
    filterBtns.forEach(btn => {
        const isActive = btn.dataset.filter === state.activeFilter;
        btn.className = 'ahs-modal-filter-btn px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 focus:outline-none ' +
            (isActive ? 'bg-primary text-white shadow-sm' : 'bg-white border border-table-border text-table-body hover:bg-slate-50');
    });
}

export function confirmModalSelection() {
    document.getElementById('ahs-empty-row')?.remove();
    const count = state.modalSelected.size;
    Array.from(state.modalSelected.values()).forEach(item => {
        renderRow({
            id: Date.now() + Math.random(),
            tipe: item.tipe, uraian: item.uraian, merk: item.merk || '',
            spesifikasi: item.spesifikasi || '', koefisien: 1,
            satuan: item.satuan, hargaSatuan: item.hargaSatuan, sumber: item.sumber || ''
        });
    });
    recalcTotals();
    closeModal();
    if (count > 0) {
        toast.show(`Berhasil menambahkan ${count} item dari database`, 'success', 3000);
    }
}

export function bindModalInfiniteScroll() {
    const wrap = document.querySelector('#ahs-modal-table')?.parentElement;
    if (!wrap) return;
    wrap.addEventListener('scroll', async function () {
        if (wrap.scrollTop + wrap.clientHeight >= wrap.scrollHeight - 10) {
            if (!state.isFetching && state.hasMoreData) {
                const q     = (modalSearch?.value || '').trim();
                const trLoad = document.createElement('tr');
                trLoad.id   = 'ahs-load-more';
                trLoad.innerHTML = '<td colspan="8" class="text-center py-2 text-table-subtle text-xs italic">Memuat lebih banyak data...</td>';
                modalTbody.appendChild(trLoad);
                await fetchAhsDatabase(state.currentPage + 1, q, true);
                document.getElementById('ahs-load-more')?.remove();
                renderModalRows(state.ahsDatabase);
            }
        }
    });
}
