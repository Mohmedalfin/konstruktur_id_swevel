/**
 * ahs/index.js
 * Entry point for the Rincian AHS feature.
 * Structure:
 *   core/       ← state + API fetch
 *   components/ ← table rows (render.js) + modal (modal.js)
 *   hooks/      ← save behavior
 */

import { state, tbody, itemLabel, addBahanBtn, addAlatBtn, addUpahBtn,
         fromDbBtn, modalClose, modalCancel, modalConfirm,
         modalSearch, modalCheckAll, filterBtns, modalTbody } from './core/state.js';
import { fetchAhsDatabase }                                    from './core/data.js';
import { addRow, renderRow, recalcTotals }                     from './components/render.js';
import { openModal, closeModal, renderModalRows, updateModalCount,
         syncFilterButtons, confirmModalSelection,
         bindModalInfiniteScroll }                             from './components/modal.js';
import { bindSave }                                            from './hooks/save.js';

// Guard: not an AHS page
if (!tbody) {
    // silent exit
} else {

    document.addEventListener('DOMContentLoaded', async function () {

        // ── Label item dari sessionStorage ────────────────────────────────
        try {
            const namaItem = sessionStorage.getItem('ahs_item_label') || '—';
            if (itemLabel) itemLabel.textContent = namaItem.toUpperCase();
        } catch (_) {}

        // ── Render initial rows (kosong / dummy) ──────────────────────────
        if (tbody.querySelectorAll('.ahs-row').length === 0) {
            tbody.innerHTML = `<tr id="ahs-empty-row"><td colspan="11" class="text-center py-10 text-table-subtle text-xs italic">Belum ada rincian AHS. Tambahkan item untuk memulai.</td></tr>`;
        }
        recalcTotals();

        // ── Toolbar ───────────────────────────────────────────────────────
        addBahanBtn?.addEventListener('click', () => addRow('bahan'));
        addAlatBtn?.addEventListener('click',  () => addRow('alat'));
        addUpahBtn?.addEventListener('click',  () => addRow('upah'));
        fromDbBtn?.addEventListener('click',   () => openModal());

        // ── Modal events ──────────────────────────────────────────────────
        modalClose?.addEventListener('click',   closeModal);
        modalCancel?.addEventListener('click',  closeModal);
        modalConfirm?.addEventListener('click', confirmModalSelection);
        document.getElementById('ahs-modal-overlay')?.addEventListener('click', function (e) {
            if (e.target === document.getElementById('ahs-modal-overlay')) closeModal();
        });

        // Modal search (debounced server fetch)
        let searchTimeout = null;
        modalSearch?.addEventListener('input', function () {
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                const q = (modalSearch.value || '').trim();
                state.currentPage = 1;
                state.hasMoreData = true;
                modalTbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-table-subtle text-xs italic">Mencari...</td></tr>';
                await fetchAhsDatabase(1, q, false);
                renderModalRows(state.ahsDatabase);
            }, 500);
        });

        // Modal filter buttons
        filterBtns.forEach(btn => {
            btn.addEventListener('click', async function () {
                state.activeFilter = btn.dataset.filter;
                syncFilterButtons();
                state.currentPage = 1;
                state.hasMoreData = true;
                const q = (modalSearch?.value || '').trim();
                modalTbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-table-subtle text-xs italic">Memuat data filter...</td></tr>';
                await fetchAhsDatabase(1, q, false);
                renderModalRows(state.ahsDatabase);
            });
        });

        // Check all
        modalCheckAll?.addEventListener('change', function () {
            const visible = modalTbody?.querySelectorAll('.modal-item-cb') || [];
            visible.forEach(cb => {
                cb.checked = modalCheckAll.checked;
                const uid  = cb.dataset.uid;
                if (modalCheckAll.checked) {
                    const item = state.ahsDatabase.find(x => x._uid === uid);
                    if (item) state.modalSelected.set(uid, item);
                    cb.closest('tr')?.classList.add('bg-primary/5');
                } else {
                    state.modalSelected.delete(uid);
                    cb.closest('tr')?.classList.remove('bg-primary/5');
                }
            });
            updateModalCount();
        });

        // Infinite scroll
        bindModalInfiniteScroll();
        syncFilterButtons();

        // ── Save ──────────────────────────────────────────────────────────
        bindSave();

        // ── Close autocomplete on outside click ───────────────────────────
        document.addEventListener('click', function (e) {
            if (state.autocompleteActive &&
                !e.target.closest('.ahs-uraian') &&
                !e.target.closest('.ahs-autocomplete')) {
                state.autocompleteActive.classList.add('hidden');
                state.autocompleteActive = null;
            }
        });
    });

} // end guard
