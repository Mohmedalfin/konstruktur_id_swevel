/**
 * rab/index.js
 * Entry point for the RAB feature.
 * Structure:
 *   core/       ← state and data layer
 *   components/ ← DOM builders / UI components
 *   hooks/      ← stateful behaviors
 */

import { state, wrapper, tbody, addRabBtn, cards,
         tambahKategoriBtn, kategoriModalClose, kategoriModalCancel,
         kategoriModalList, kategoriModalOverlay, kategoriModalConfirm,
         kategoriManualInput, kategoriManualAdd } from './core/state.js?v=3';
import { fetchRabData, fetchKategoriMaster, getProjectSlug }                from './core/data.js?v=3';
import { fetchRapItems }                                                  from './core/rap-data.js?v=3';
import { confirmDelete }                                                 from '../shared/ui/confirm.js?v=3';
import { toast }                                                         from '../shared/ui/toast.js?v=3';
import { renderLoading, renderReadonly, renderEditable,
         showTable, setEditableMode, renderRapFromDB }              from './components/render.js?v=4';
import { openKategoriModal, closeKategoriModal,
         updateModalInfo, appendCategoryRow }                            from './components/categories.js?v=4';
import { initImport }                                                    from './components/import.js?v=3';
import { initTemplate }                                                  from './components/template.js?v=3';
import { injectPendingItems }                                    from './hooks/pending.js?v=4';
import { bindSearch }                                                    from './hooks/search.js?v=3';

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
    if (kategoriModalClose)  kategoriModalClose.addEventListener('click',  closeKategoriModal);
    if (kategoriModalCancel) kategoriModalCancel.addEventListener('click', closeKategoriModal);
    if (kategoriModalList)   kategoriModalList.addEventListener('change',  updateModalInfo);
    if (kategoriModalOverlay) {
        kategoriModalOverlay.addEventListener('click', e => {
            if (e.target === kategoriModalOverlay) closeKategoriModal();
        });
    }
    if (kategoriModalConfirm) {
        kategoriModalConfirm.addEventListener('click', function () {
            if (!kategoriModalList) { closeKategoriModal(); return; }
            kategoriModalList.querySelectorAll('.kategori-checkbox:not([disabled]):checked').forEach(cb => {
                const cat = { 
                    id: cb.dataset.id, 
                    nama: cb.dataset.nama,
                    db_id: cb.dataset.dbid || cb.dataset.db_id || '' 
                };
                if (!state.activeCategories.some(c => c.id === cat.id)) {
                    state.activeCategories.push(cat);
                    appendCategoryRow(cat);
                }
            });
            closeKategoriModal();
        });
    }

    // --- Custom Manual Category ---
    const addManualCategory = async () => {
        if (!kategoriManualInput || !kategoriModalList) return;
        const val = kategoriManualInput.value.trim();
        if (!val) return;

        const slug = getProjectSlug();
        if (!slug) {
            toast.show('Pilih Proyek terlebih dahulu!', 'error', 3000);
            return;
        }

        const escVal = val.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        
        try {
            // POST to backend API
            const response = await fetch('/api/kategori', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    slug: slug,
                    nama_kategori: escVal
                })
            });

            const result = await response.json();
            if (!response.ok) {
                toast.show(result.messages?.error || 'Gagal menambahkan', 'error', 3000);
                return;
            }

            const masterId = result.data.id_kategori_pekerjaan;
            const manualId = result.data.kode_kategori;

            const li = document.createElement('li');
            li.innerHTML = `
                <div class="view-mode flex items-center gap-3 p-3 rounded-xl border border-primary bg-primary/5 cursor-pointer transition-all select-none group hover:bg-primary/10 w-full">
                    <input type="checkbox"
                        class="kategori-checkbox w-4 h-4 accent-primary rounded focus:ring-primary"
                        value="${manualId}" data-id="${manualId}" data-dbid="${masterId}" data-nama="${escVal}" checked>
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
            
            const viewMode  = li.querySelector('.view-mode');
            const editMode  = li.querySelector('.edit-mode');
            const btnEdit   = li.querySelector('.btn-edit-manual');
            const btnDel    = li.querySelector('.btn-del-manual');
            const spanName  = li.querySelector('.cat-name-display');
            const checkbox  = li.querySelector('.kategori-checkbox');
            const editInput = li.querySelector('.inline-edit-input');
            const btnSave   = li.querySelector('.btn-save-edit');
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

            // Save Edit (PUT API)
            const saveEdit = async () => {
                const newVal = editInput.value.trim();
                if (newVal !== "" && newVal !== checkbox.dataset.nama) {
                    const finalVal = newVal.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    
                    try {
                        const dbId = checkbox.dataset.dbid;
                        const putRes = await fetch(`/api/kategori/${dbId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ nama_kategori: finalVal })
                        });

                        if (putRes.ok) {
                            checkbox.dataset.nama = finalVal;
                            spanName.textContent = finalVal;
                            toast.show('Nama kategori diperbarui', 'success', 2000);
                        } else {
                            toast.show('Gagal memperbarui', 'error', 2000);
                        }
                    } catch (e) {
                        toast.show('Gagal menghubungi server', 'error', 2000);
                    }
                }
                editMode.classList.add('hidden');
                editMode.classList.remove('flex');
                viewMode.classList.remove('hidden');
            };
            
            btnSave.addEventListener('click', (e) => {
                e.preventDefault(); e.stopPropagation();
                saveEdit();
            });
            editInput.addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); saveEdit(); }
                if (e.key === 'Escape') { e.preventDefault(); btnCancel.click(); }
            });

            // Delete (DELETE API)
            btnDel.addEventListener('click', async (e) => {
                e.preventDefault(); e.stopPropagation();
                const confirmed = await confirmDelete(checkbox.dataset.nama);
                if (confirmed) {
                    try {
                        const catName = checkbox.dataset.nama;
                        const dbId = checkbox.dataset.dbid;
                        const delRes = await fetch(`/api/kategori/${dbId}`, {
                            method: 'DELETE',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (delRes.ok) {
                            li.remove();
                            updateModalInfo();
                            toast.show(`Kategori "${catName}" dihapus`, 'info', 2500);
                        } else {
                            toast.show('Gagal menghapus kategori', 'error', 2500);
                        }
                    } catch (err) {
                        toast.show('Gagal menghubungi server', 'error', 2500);
                    }
                }
            });
            
            kategoriModalList.insertBefore(li, kategoriModalList.firstChild);
            kategoriManualInput.value = '';
            updateModalInfo();
            toast.show(`Kategori ditambahkan`, 'success', 2000);
        } catch (error) {
            toast.show('Gagal menghubungi server', 'error', 3000);
        }
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
            state.mode            = 'readonly';
            state.currentId       = id;
            state.collapsed       = {};
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
            state.mode            = 'editable';
            state.currentId       = null;
            state.collapsed       = {};
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
        const slug = getProjectSlug();

        if (!init || !init.mode) {
            // Tidak ada mode eksplisit — coba load dari DB jika ada slug
            if (slug) {
                setEditableMode(true);
                showTable();
                renderLoading();
                try {
                    const apiData = await fetchRapItems(slug);
                    renderRapFromDB(apiData);
                } catch (e) {
                    console.error('Gagal load RAP dari DB:', e);
                }
            }
            return;
        }

        if (init.mode === 'readonly' && init.id) {
            state.mode      = 'readonly';
            state.currentId = init.id;
            setEditableMode(false);
            showTable();
            renderLoading();
            renderReadonly(await fetchRabData(init.id));

        } else if (init.mode === 'new' || init.mode === 'editable') {
            state.mode            = 'editable';
            state.currentId       = null;
            state.activeCategories = [];
            setEditableMode(true);
            showTable();
            renderLoading();

            const hasPending = !!sessionStorage.getItem('rab_pending_items');
            if (hasPending) {
                // Tunggu sampai injeksi POST /api/rap kelar
                await injectPendingItems();
            }

            // Langsung load dari DB secara utuh, tak ada jeda aneh!
            try {
                const apiData = await fetchRapItems(slug);
                renderRapFromDB(apiData);
            } catch (e) {
                console.error('Gagal load RAP dari DB:', e);
                renderEditable();
            }
        }
    });

    // ── BOQ Import event (from components/import.js) ─────────────────────────
    window.addEventListener('rabDataImported', async function (e) {
        const importedItems = e.detail;
        if (!importedItems || importedItems.length === 0) return;

        if (state.mode === 'readonly') {
            alert('RAB dalam mode Read-Only. Tidak bisa mengimpor data ke sini.');
            return;
        }

        const newItems = importedItems.map(item => ({
            id:               item.id,
            nama:             item.uraian,
            volume:           item.volume,
            satuan:           item.satuan,
            hargaBahan:       item.harga_bahan,
            hargaAlat:        item.harga_alat,
            hargaUpah:        item.harga_upah,
            hargaKeseluruhan: (item.volume || 1) * (item.harga_bahan + item.harga_alat + item.harga_upah),
            kategori:         item.kategori || 'persiapan'
        }));

        const grouped = {};
        newItems.forEach(item => {
            if (!grouped[item.kategori]) grouped[item.kategori] = [];
            grouped[item.kategori].push(item);
        });

        // Fetch DB Categories mapping
        const remoteMaster = await fetchKategoriMaster();
        const nameMap = {};
        const dbIdMap = {};
        remoteMaster.forEach(k => { 
            nameMap[k.id] = k.nama; 
            dbIdMap[k.id] = k.db_id;
        });

        try {
            let parsed = JSON.parse(sessionStorage.getItem('rab_pending_items') || '[]');
            Object.keys(grouped).forEach(catId => {
                const catName = nameMap[catId] || catId;
                const catDbId = dbIdMap[catId] || null;
                const found   = parsed.find(g => g.catId === catId);
                if (found) { found.items.push(...grouped[catId]); }
                else       { parsed.push({ catId, catName, catDbId, items: grouped[catId] }); }
                if (!state.activeCategories.some(c => c.id === catId)) {
                    const cat = { id: catId, nama: catName, db_id: catDbId };
                    state.activeCategories.push(cat);
                    appendCategoryRow(cat);
                }
            });
            sessionStorage.setItem('rab_pending_items', JSON.stringify(parsed));
        } catch (_) {}

        injectPendingItems();
    });

} // end guard
