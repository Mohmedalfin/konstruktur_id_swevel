/**
 * rab/index.js
 * Entry point for the RAB feature.
 * Structure:
 *   core/       ← state and data layer
 *   components/ ← DOM builders / UI components
 *   hooks/      ← stateful behaviors
 */

import {
    state, wrapper, tbody, addRabBtn, cards,
    tambahKategoriBtn, kategoriModalClose, kategoriModalCancel,
    kategoriModalList, kategoriModalOverlay, kategoriModalConfirm
} from './core/state.js';
import { fetchRabData } from './core/data.js';
import {
    renderLoading, renderReadonly, renderEditable,
    showTable, setEditableMode
} from './components/render.js';
import {
    openKategoriModal, closeKategoriModal,
    updateModalInfo, appendCategoryRow
} from './components/categories.js';
import { initImport } from './components/import.js';
import { initTemplate } from './components/template.js';
import { bindSearch } from './hooks/search.js';

/**
 * Show or hide the "+ Kategori Pekerjaan" button based on the sumber_data
 * returned from the API. Only manual projects can be edited.
 */
function applySourcePermission(data) {
    if (!tambahKategoriBtn) return;
    const isEditable = (data?.sumber_data || 'manual') === 'manual';
    tambahKategoriBtn.classList.toggle('hidden', !isEditable);
}

// Guard: do nothing if not a RAB page
if (!wrapper || !tbody) {
    // Not a RAB page — exit silently
} else {

    // ── Hooks ─────────────────────────────────────────────────────────────────
    bindSearch();

    // ── Components init ───────────────────────────────────────────────────────
    initTemplate();
    initImport();

    // ── Kategori modal events ─────────────────────────────────────────────────
    if (tambahKategoriBtn) tambahKategoriBtn.addEventListener('click', openKategoriModal);
    if (kategoriModalClose) kategoriModalClose.addEventListener('click', closeKategoriModal);
    if (kategoriModalCancel) kategoriModalCancel.addEventListener('click', closeKategoriModal);
    if (kategoriModalList) kategoriModalList.addEventListener('change', updateModalInfo);
    if (kategoriModalOverlay) {
        kategoriModalOverlay.addEventListener('click', e => {
            if (e.target === kategoriModalOverlay) closeKategoriModal();
        });
    }
    if (kategoriModalConfirm) {
        kategoriModalConfirm.addEventListener('click', async function () {
            try {
                if (!kategoriModalList) {
                    closeKategoriModal();
                    return;
                }

                const checked = Array.from(
                    kategoriModalList.querySelectorAll('.kategori-checkbox:not([disabled]):checked')
                );

                if (checked.length === 0) {
                    alert('Pilih minimal 1 kategori.');
                    return;
                }

                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (!idProject) {
                    alert('ID project tidak ditemukan.');
                    return;
                }

                const kategoriPayload = checked.map(cb => ({
                    nama: cb.dataset.nama
                }));

                // loading state tombol
                const oldText = kategoriModalConfirm.textContent;
                kategoriModalConfirm.disabled = true;
                kategoriModalConfirm.textContent = 'Menambahkan...';

                // tutup modal dulu biar terasa responsif
                closeKategoriModal();

                const res = await fetch(window.RAB_INIT.apiKategoriUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_project: Number(idProject),
                        kategori: kategoriPayload
                    }),
                });

                const json = await res.json();

                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menyimpan kategori');
                }

                renderLoading();
                const data = await fetchRabData(idProject);

                state.activeCategories = (data.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));

                applySourcePermission(data);
                renderReadonly(data);

                kategoriModalConfirm.disabled = false;
                kategoriModalConfirm.textContent = oldText;
            } catch (err) {
                console.error('Gagal tambah kategori:', err);
                kategoriModalConfirm.disabled = false;
                kategoriModalConfirm.textContent = 'Tambahkan';
                alert(err.message || 'Terjadi kesalahan saat menambahkan kategori');
            }
        });
    }

    // ── RAB card click (readonly mode) ───────────────────────────────────────
    cards.forEach(card => {
        card.addEventListener('click', async function () {
            const id = card.dataset.id;
            state.mode = 'readonly';
            state.currentId = id;
            state.collapsed = {};
            state.activeCategories = [];

            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            card.classList.add('ring-2', 'ring-primary');

            setEditableMode(false);
            showTable();
            renderLoading();
            renderReadonly(await fetchRabData(id));
        });
    });

    // ── Add RAB button (editable mode) ───────────────────────────────────────
    if (addRabBtn) {
        addRabBtn.addEventListener('click', function () {
            state.mode = 'editable';
            state.currentId = null;
            state.collapsed = {};
            state.activeCategories = [];
            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            setEditableMode(true);
            showTable();
            renderEditable();
        });
    }

    // ── DOMContentLoaded: auto-init from URL (RAB_INIT) ──────────────────────
    document.addEventListener('DOMContentLoaded', async function () {
        const init = window.RAB_INIT;
        const idProject = init?.idProject || init?.id;

        if (!init || !idProject) return;

        try {
            state.mode = 'readonly';
            state.currentId = idProject;
            state.collapsed = {};

            setEditableMode(true);
            showTable();
            renderLoading();

            const data = await fetchRabData(idProject);

            state.activeCategories = (data.categories || []).map(cat => ({
                id: String(cat.id),
                nama: cat.name
            }));

            applySourcePermission(data);
            renderReadonly(data);
        } catch (err) {
            console.error('Gagal memuat RAP:', err);
            tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-10 text-red-500 text-xs">
                    Gagal memuat data RAP.
                </td>
            </tr>
        `;
        }
    });
    // ── BOQ Import event (from components/import.js) ─────────────────────────
    window.addEventListener('rabDataImported', function (e) {
        const importedItems = e.detail;
        if (!importedItems || importedItems.length === 0) return;

        if (state.mode === 'readonly') {
            alert('RAB dalam mode Read-Only. Tidak bisa mengimpor data ke sini.');
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

        const grouped = {};
        newItems.forEach(item => {
            if (!grouped[item.kategori]) grouped[item.kategori] = [];
            grouped[item.kategori].push(item);
        });

        const nameMap = {};
        dummyKategoriMaster.forEach(k => { nameMap[k.id] = k.nama; });

        try {
            let parsed = JSON.parse(sessionStorage.getItem('rab_pending_items') || '[]');
            Object.keys(grouped).forEach(catId => {
                const catName = nameMap[catId] || catId;
                const found = parsed.find(g => g.catId === catId);
                if (found) { found.items.push(...grouped[catId]); }
                else { parsed.push({ catId, catName, items: grouped[catId] }); }
                if (!state.activeCategories.some(c => c.id === catId)) {
                    const cat = { id: catId, nama: catName };
                    state.activeCategories.push(cat);
                    appendCategoryRow(cat);
                }
            });
            sessionStorage.setItem('rab_pending_items', JSON.stringify(parsed));
        } catch (_) { }

        injectPendingItems();
    });

} // end guard
