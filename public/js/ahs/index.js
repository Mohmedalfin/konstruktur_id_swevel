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
         modalSearch, modalCheckAll, filterBtns, modalTbody,
         tableSearch, sourceLabel } from './core/state.js';
import { fetchAhsDatabase, fetchProyekItems, fetchShbjItems, fetchSurveyItems, fetchEstimatorIdItems, fetchRincianAHS } from './core/data.js';
import { addRow, renderRow, recalcTotals, initEmptyFramework }                     from './components/render.js';
import { openModal, closeModal, renderModalRows, updateModalCount,
         syncFilterButtons, confirmModalSelection,
         bindModalInfiniteScroll, bindSourceTabs }             from './components/modal.js';
import { bindSave }                                            from './hooks/save.js';

// Guard: not an AHS page
if (!tbody) {
    // silent exit
} else {

    document.addEventListener('DOMContentLoaded', async function () {
        const urlParams = new URLSearchParams(window.location.search);
        const idDetail  = urlParams.get('id_rap_detail');

        // ── Label item dari sessionStorage ────────────────────────────────
        try {
            const namaItem = sessionStorage.getItem('ahs_item_label') || '—';
            if (itemLabel) itemLabel.textContent = namaItem.toUpperCase();
            
            const sumber = sessionStorage.getItem('ahs_item_source') || 'PUPR';
            if (sourceLabel) sourceLabel.textContent = sumber.toUpperCase();
        } catch (_) {}

        // ── Ambil id_project dan id_rap_detail dari URL atau sessionStorage ───
        try {
            const urlParams  = new URLSearchParams(window.location.search);
            const idFromUrl  = urlParams.get('id_project');
            const idFromSess = sessionStorage.getItem('id_project') ||
                               sessionStorage.getItem('rap_id_project');
            state.idProject  = idFromUrl ? parseInt(idFromUrl, 10)
                                         : (idFromSess ? parseInt(idFromSess, 10) : null);
            // Store id_rap_detail for backend fallback
            const detailFromUrl = urlParams.get('id_rap_detail');
            state.idDetail = detailFromUrl ? parseInt(detailFromUrl, 10) : null;
        } catch (_) {}

        // ── Render initial rows ───────────────────────────────────────────
        if (idDetail) {
            const existing = await fetchRincianAHS(idDetail);
            if (existing && existing.length > 0) {
                tbody.innerHTML = '';
                initEmptyFramework();
                existing.forEach(item => renderRow(item));
            } else {
                tbody.innerHTML = '';
                initEmptyFramework();
            }
        } else {
            tbody.innerHTML = `<tr id="ahs-empty-row"><td colspan="11" class="text-center py-10 text-table-subtle text-xs italic">ID Detail tidak valid.</td></tr>`;
        }

        recalcTotals();

        // ── Fetch master for autocomplete ─────────────────────────────────
        fetchAhsDatabase(1);

        // ── Expose Modal ──────────────────────────────────────────────────
        window.ahsOpenModalWithFilter = openModal;
        window.ahsOpenModal = openModal;

        // Old toolbar buttons removed or hidden, no need to bind them.

        // ── Table Search (Local) ─────────────────────────────────────────
        let tableSearchTimeout = null;
        tableSearch?.addEventListener('input', function() {
            if (tableSearchTimeout) clearTimeout(tableSearchTimeout);
            tableSearchTimeout = setTimeout(() => {
                const q = (tableSearch.value || '').trim().toLowerCase();
                const rows = tbody.querySelectorAll('.ahs-row');
                rows.forEach(row => {
                    const text = row.querySelector('.ahs-uraian')?.value.toLowerCase() || '';
                    if (text.includes(q)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
                
                // Toggle headers/footers if their rows are all hidden
                ['bahan', 'upah', 'alat'].forEach(tipe => {
                    const typeRows = Array.from(tbody.querySelectorAll(`.ahs-row[data-tipe="${tipe}"]`));
                    const anyVisible = typeRows.some(r => !r.classList.contains('hidden'));
                    
                    const header = tbody.querySelector(`.ahs-category-header[data-tipe="${tipe}"]`);
                    const f1 = tbody.querySelector(`.ahs-group-f1-${tipe}`);
                    const f2 = tbody.querySelector(`.ahs-group-f2-${tipe}`);
                    const f3 = tbody.querySelector(`.ahs-group-f3-${tipe}`);
                    
                    if (!anyVisible && q !== '') {
                        header?.classList.add('hidden');
                        f1?.classList.add('hidden');
                        f2?.classList.add('hidden');
                        f3?.classList.add('hidden');
                    } else {
                        header?.classList.remove('hidden');
                        f1?.classList.remove('hidden');
                        f2?.classList.remove('hidden');
                        f3?.classList.remove('hidden');
                    }
                });
            }, 300);
        });

        // ── Modal events ──────────────────────────────────────────────────
        modalClose?.addEventListener('click',   closeModal);
        modalCancel?.addEventListener('click',  closeModal);
        modalConfirm?.addEventListener('click', confirmModalSelection);
        document.getElementById('ahs-modal-overlay')?.addEventListener('click', function (e) {
            if (e.target === document.getElementById('ahs-modal-overlay')) closeModal();
        });

        // Modal search (debounced — reuses active source)
        let searchTimeout = null;
        modalSearch?.addEventListener('input', function () {
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                const q   = (modalSearch.value || '').trim();
                const src = state.activeSource;
                state.currentPage = 1;
                state.hasMoreData = true;
                modalTbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-table-subtle text-xs italic">Mencari...</td></tr>';

                if (src === 'proyek') {
                    await fetchProyekItems(state.idProject, state.idDetail, 1, q, false);
                } else if (src === 'shbj') {
                    await fetchShbjItems(1, q, false);
                } else if (src === 'survey') {
                    await fetchSurveyItems(1, q, false);
                } else if (src === 'estimatorid') {
                    await fetchEstimatorIdItems(1, q, false);
                } else {
                    await fetchAhsDatabase(1, q, false);
                }
                renderModalRows(state.ahsDatabase);
            }, 500);
        });

        // Modal filter buttons — respects active source tab
        filterBtns.forEach(btn => {
            btn.addEventListener('click', async function () {
                state.activeFilter = btn.dataset.filter;
                syncFilterButtons();
                state.currentPage = 1;
                state.hasMoreData = true;
                const q   = (modalSearch?.value || '').trim();
                const src = state.activeSource;
                modalTbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-table-subtle text-xs italic">Memuat data filter...</td></tr>';
                if (src === 'proyek') {
                    await fetchProyekItems(state.idProject, state.idDetail, 1, q, false);
                } else if (src === 'shbj') {
                    await fetchShbjItems(1, q, false);
                } else if (src === 'survey') {
                    await fetchSurveyItems(1, q, false);
                } else if (src === 'estimatorid') {
                    await fetchEstimatorIdItems(1, q, false);
                } else {
                    await fetchAhsDatabase(1, q, false);
                }
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
        bindSourceTabs();

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

// Global helper for Back button
window.goBackToRab = function() {
    let returnUrl = '';
    try { returnUrl = sessionStorage.getItem('rab_return_url'); } catch (_) {}
    window.location.href = returnUrl ? returnUrl : '/menu-rap?mode=new';
};
