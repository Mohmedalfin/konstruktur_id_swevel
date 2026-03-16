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

    // --- Custom Manual Category ---
    const addManualCategory = () => {
        if (!kategoriManualInput || !kategoriModalList) return;
        const val = kategoriManualInput.value.trim();
        if (!val) return;

        const escVal = val.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const manualId = 'manual-' + Date.now();
        const li = document.createElement('li');
        li.innerHTML = `
            <div class="view-mode flex items-center gap-3 p-3 rounded-xl border border-primary bg-primary/5 cursor-pointer transition-all select-none group hover:bg-primary/10 w-full">
                <input type="checkbox"
                    class="kategori-checkbox w-4 h-4 accent-primary rounded focus:ring-primary"
                    value="${manualId}" data-id="${manualId}" data-nama="${escVal}" checked>
                <div class="flex-1 min-w-0" onclick="this.previousElementSibling.click()">
                    <span class="cat-name-display text-xs font-medium text-table-body truncate block">${escVal}</span>
                </div>
                
                <div class="shrink-0 flex items-center min-h-[24px]">
                    <span class="text-[10px] text-primary italic font-medium group-hover:hidden">Kustom</span>
                    <div class="hidden group-hover:flex items-center gap-1">
                        <button type="button" class="btn-edit-manual flex items-center justify-center w-6 h-6 rounded-md bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors shadow-sm" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button type="button" class="btn-del-manual flex items-center justify-center w-6 h-6 rounded-md bg-white border border-red-200 text-red-600 hover:bg-red-50 transition-colors shadow-sm" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="edit-mode hidden items-center gap-2 p-3 rounded-xl border border-blue-300 bg-blue-50 w-full">
                <input type="text" class="inline-edit-input flex-1 text-xs px-2 py-1.5 rounded border border-blue-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" value="${escVal}">
                <button type="button" class="btn-save-edit flex items-center justify-center w-7 h-7 rounded-md bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm" title="Simpan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button type="button" class="btn-cancel-edit flex items-center justify-center w-7 h-7 rounded-md bg-white hover:bg-slate-100 border border-slate-200 text-slate-500 shadow-sm" title="Batal">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>`;

        const viewMode = li.querySelector('.view-mode');
        const editMode = li.querySelector('.edit-mode');
        const btnEdit = li.querySelector('.btn-edit-manual');
        const btnDel = li.querySelector('.btn-del-manual');
        const spanName = li.querySelector('.cat-name-display');
        const checkbox = li.querySelector('.kategori-checkbox');
        const editInput = li.querySelector('.inline-edit-input');
        const btnSave = li.querySelector('.btn-save-edit');
        const btnCancel = li.querySelector('.btn-cancel-edit');

        // Toggle to Edit Mode
        btnEdit.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            viewMode.classList.add('hidden');
            editMode.classList.remove('hidden');
            editMode.classList.add('flex');
            editInput.value = checkbox.dataset.nama;
            editInput.focus();
        });

        // Cancel Edit
        btnCancel.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            editMode.classList.add('hidden');
            editMode.classList.remove('flex');
            viewMode.classList.remove('hidden');
        });

        // Save Edit
        const saveEdit = () => {
            const newVal = editInput.value.trim();
            if (newVal !== "") {
                const finalVal = newVal.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                checkbox.dataset.nama = finalVal;
                spanName.textContent = finalVal;
            }
            editMode.classList.add('hidden');
            editMode.classList.remove('flex');
            viewMode.classList.remove('hidden');
            toast.show('Nama kategori diperbarui', 'success', 2000);
        };
        btnSave.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            saveEdit();
        });
        editInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); saveEdit(); }
            if (e.key === 'Escape') { e.preventDefault(); btnCancel.click(); }
        });

        // Delete
        btnDel.addEventListener('click', async (e) => {
            e.preventDefault(); e.stopPropagation();
            const confirmed = await confirmDelete(checkbox.dataset.nama);
            if (confirmed) {
                const catName = checkbox.dataset.nama;
                li.remove();
                updateModalInfo();
                toast.show(`Kategori "${catName}" dihapus`, 'info', 2500);
            }
        });

        kategoriModalList.insertBefore(li, kategoriModalList.firstChild);
        kategoriManualInput.value = '';
        updateModalInfo();
        toast.show(`Kategori ditambahkan`, 'success', 2000);
    };

    if (kategoriManualAdd) {
        kategoriManualAdd.addEventListener('click', addManualCategory);
    }
    if (kategoriManualInput) {
        kategoriManualInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addManualCategory();
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
