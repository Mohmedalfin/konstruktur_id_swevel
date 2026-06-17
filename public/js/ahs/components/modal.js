/**
 * ahs/components/modal.js
 * "Pilih dari Daftar AHS" modal — open/close, rendering, search, filter,
 * tab-based source switching, infinite scroll, check-all.
 */

import { state, modalOverlay, modalClose, modalCancel, modalConfirm,
         modalSearch, modalTbody, modalCheckAll, modalCountEl, filterBtns } from '../core/state.js';
import { fetchAhsDatabase, fetchProyekItems, fetchShbjItems, fetchSurveyItems, fetchEstimatorIdItems } from '../core/data.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { toast } from '../../shared/ui/toast.js';
import { tipeConfig, renderRow, recalcTotals, checkAndMarkEmpiris } from './render.js';

// ── Helper: fetch the right data based on the active source tab ────────────
async function fetchBySource(page = 1, q = '', appendData = false) {
    const src = state.activeSource;

    if (src === 'proyek') {
        await fetchProyekItems(state.idProject, state.idDetail, page, q, appendData);
    } else if (src === 'shbj') {
        await fetchShbjItems(page, q, appendData);
    } else if (src === 'survey') {
        await fetchSurveyItems(page, q, appendData);
    } else if (src === 'estimatorid') {
        await fetchEstimatorIdItems(page, q, appendData);
    } else {
        // suplier, ikkbps — semua masih pakai estimator DB untuk sekarang
        await fetchAhsDatabase(page, q, appendData);
    }
}

// ── Helper: loading indicator text per tab ─────────────────────────────────
function getLoadingLabel() {
    switch (state.activeSource) {
        case 'proyek':      return 'Memuat data dari proyek terkini...';
        case 'shbj':        return 'Memuat data SHBJ (regulasi daerah)...';
        case 'survey':      return 'Memuat data hasil survey...';
        case 'estimatorid': return 'Memuat data dari Estimator.id...';
        default:            return 'Memuat data...';
    }
}

export function openModal(initialFilter = 'all') {
    if (!modalOverlay) return;
    state.modalSelected.clear();
    updateModalCount();

    state.currentPage  = 1;
    state.hasMoreData  = true;
    state.ahsDatabase  = [];
    if (modalSearch) modalSearch.value = '';
    state.activeFilter = initialFilter;
    syncFilterButtons();

    // Hide Suplier tab if filter is upah or alat
    const suplierTab = document.querySelector('.ahs-source-tab[data-source="suplier"]');
    if (suplierTab) {
        if (initialFilter === 'upah' || initialFilter === 'alat') {
            suplierTab.classList.add('hidden');
            if (state.activeSource === 'suplier') {
                state.activeSource = 'proyek';
            }
        } else {
            suplierTab.classList.remove('hidden');
        }
    }

    _syncSourceTabs(state.activeSource);   // keep last-used tab

    modalTbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-slate-400 text-xs italic">${getLoadingLabel()}</td></tr>`;

    // Slide down animation
    modalOverlay.classList.remove('hidden');
    modalOverlay.classList.add('flex');
    setTimeout(() => {
        modalOverlay.classList.remove('opacity-0');
        const content = document.getElementById('ahs-modal-content');
        if (content) {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }
    }, 10);

    fetchBySource(1, '', false).then(() => {
        renderModalRows(state.ahsDatabase);
        setTimeout(() => modalSearch?.focus(), 300);
    });
}

export function closeModal() {
    if (!modalOverlay) return;

    // Slide up animation
    modalOverlay.classList.add('opacity-0');
    const content = document.getElementById('ahs-modal-content');
    if (content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
    }

    setTimeout(() => {
        modalOverlay.classList.add('hidden');
        modalOverlay.classList.remove('flex');
        if (modalSearch)   modalSearch.value    = '';
        if (modalCheckAll) modalCheckAll.checked = false;
        state.activeFilter = 'all';
        syncFilterButtons();
    }, 300);
}

function _syncSourceTabs(activeSource) {
    document.querySelectorAll('.ahs-source-tab').forEach(btn => {
        const isActive = btn.dataset.source === activeSource;
        if (isActive) {
            btn.classList.add('border-blue-600', 'text-blue-600');
            btn.classList.remove('border-transparent', 'text-slate-500');
        } else {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-slate-500');
        }
    });
}

export function bindSourceTabs() {
    document.querySelectorAll('.ahs-source-tab').forEach(btn => {
        btn.addEventListener('click', async function () {
            const src = this.dataset.source;
            state.activeSource = src;
            _syncSourceTabs(src);

            // Reset pagination & database
            state.currentPage = 1;
            state.hasMoreData = true;
            state.ahsDatabase = [];

            const q = (modalSearch?.value || '').trim();

            // Update search placeholder based on tab
            if (modalSearch) {
                const labels = {
                    proyek      : 'Cari dari proyek terkini...',
                    shbj        : 'Cari berdasarkan regulasi daerah...',
                    suplier     : 'Cari dari daftar suplier...',
                    ikkbps      : 'Cari indeks IKK BPS...',
                    estimatorid : 'Cari dari Estimator.id...',
                    survey      : 'Cari dari hasil survey...',
                };
                modalSearch.placeholder = labels[src] || 'Ketik kata kunci...';
            }

            // Update filter label
            const filterLabel = document.getElementById('ahs-filter-label-nama');
            if (filterLabel) {
                const labelMap = {
                    proyek      : 'Nama Material',
                    shbj        : 'Nama Material (SHBJ)',
                    suplier     : 'Nama Material',
                    ikkbps      : 'Nama Material',
                    estimatorid : 'Nama Material',
                    survey      : 'Nama Material',
                };
                filterLabel.textContent = labelMap[src] || 'Nama Material';
            }

            modalTbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-slate-400 text-xs italic">${getLoadingLabel()}</td></tr>`;
            await fetchBySource(1, q, false);
            renderModalRows(state.ahsDatabase);
        });
    });
}

export function renderModalRows(items) {
    if (!modalTbody) return;
    if (items.length === 0) {
        const emptyMessages = {
            proyek      : 'Belum ada bahan/upah/alat yang terinput di proyek ini.',
            shbj        : 'Tidak ada data SHBJ ditemukan. Pastikan keterangan item berisi referensi regulasi daerah (Kepgub, Pergub, Kepbup, dll).',
            survey      : 'Tidak ada data dari hasil survey ditemukan.',
            estimatorid : 'Tidak ada data bersumber dari Estimator.id ditemukan.',
        };
        const msg = emptyMessages[state.activeSource] || 'Tidak ada item ditemukan.';
        modalTbody.innerHTML = `<tr><td colspan="9" class="text-center py-12 text-slate-400 text-xs italic px-6">${msg}</td></tr>`;
        return;
    }

    let rowNum = 1;
    modalTbody.innerHTML = items.map(item => {
        const checked  = state.modalSelected.has(item._uid);
        const bgClass  = (rowNum % 2 === 0) ? 'bg-[#f0f5ff]' : 'bg-white';
        const n        = rowNum++;
        // Badge for tipe
        const tipeBadge = {
            bahan : '<span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-green-100 text-green-700">BAHAN</span>',
            upah  : '<span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700">UPAH</span>',
            alat  : '<span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-orange-100 text-orange-700">ALAT</span>',
        };
        const badge = tipeBadge[item.tipe] || '';

        // Parse sumber format "Nama Regulasi|https://link"
        const rawSumber = item.sumber || '';
        const pipeIdx   = rawSumber.indexOf('|');
        const sumberNama = pipeIdx !== -1 ? rawSumber.substring(0, pipeIdx) : rawSumber;
        const sumberUrl  = pipeIdx !== -1 ? rawSumber.substring(pipeIdx + 1) : '';
        const sumberCell = sumberNama || sumberUrl
            ? `<div class="flex flex-col gap-0.5">
                <span class="text-[11px] font-medium text-amber-700">${escHtml(sumberNama || '—')}</span>
                ${sumberUrl
                    ? `<a href="${escHtml(sumberUrl)}" target="_blank" rel="noopener" onclick="event.stopPropagation()"
                        class="inline-flex items-center gap-1 text-[10px] text-blue-500 hover:text-blue-700 hover:underline">
                        <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat
                    </a>`
                    : ''}
              </div>`
            : `<span class="text-slate-300 text-[10px] italic">—</span>`;

        return `
        <tr class="modal-item-row border-b border-gray-100 ${bgClass} hover:bg-slate-100 transition-colors cursor-pointer ${checked ? '!bg-brand-dark/10' : ''}"
            data-uid="${item._uid}">
            <td class="px-3 py-2 text-center text-[11px] font-medium text-slate-400 tabular-nums">${n}</td>
            <td class="px-3 py-2 text-center whitespace-nowrap">
                ${badge}
            </td>
            <td class="px-3 py-2 text-[12px] text-slate-800 font-medium">
                ${escHtml(item.uraian)}
            </td>
            <td class="px-3 py-2 text-center text-[12px] text-slate-600 whitespace-nowrap">${escHtml(item.satuan)}</td>
            <td class="px-3 py-2 text-right text-[12px] tabular-nums text-slate-800 whitespace-nowrap">${fmt(item.hargaSatuan)}</td>
            <td class="px-3 py-2 text-[12px] text-slate-600 whitespace-nowrap">${escHtml(item.merk || 'Standar')}</td>
            <td class="px-3 py-2 text-[12px] text-slate-600">${escHtml(item.spesifikasi || 'Standar')}</td>
            <td class="px-3 py-2">${sumberCell}</td>
            <td class="px-3 py-2 text-center">
                <input type="checkbox" class="modal-item-cb w-3.5 h-3.5 accent-brand-dark cursor-pointer"
                    data-uid="${item._uid}" ${checked ? 'checked' : ''}/>
            </td>
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
    checkAndMarkEmpiris();
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
                trLoad.innerHTML = '<td colspan="9" class="text-center py-2 text-table-subtle text-xs italic">Memuat lebih banyak data...</td>';
                modalTbody.appendChild(trLoad);
                await fetchBySource(state.currentPage + 1, q, true);
                document.getElementById('ahs-load-more')?.remove();
                renderModalRows(state.ahsDatabase);
            }
        }
    });
}
