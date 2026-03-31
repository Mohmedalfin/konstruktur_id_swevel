/**
 * components/categories.js
 * Category modal management and dynamic category row creation.
 */

import { state, tbody, kategoriModalOverlay, kategoriModalList, kategoriModalInfo } from '../core/state.js';
import { fetchKategoriMaster }     from '../core/data.js';
import { confirmDeleteCategory, confirmDelete }   from '../../shared/ui/confirm.js';
import { toast }                   from '../../shared/ui/toast.js';

export function openKategoriModal() {
    if (!kategoriModalOverlay || !kategoriModalList) return;

    fetchKategoriMaster().then(list => {
        kategoriModalList.innerHTML = '';
        list.forEach(cat => {
            const alreadyAdded = state.activeCategories.some(c => c.id === cat.id);
            const li = document.createElement('li');
            
            if (cat.id && cat.id.startsWith('kustom_')) {
                const escVal = cat.nama.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                li.innerHTML = `
                    <div class="view-mode flex items-center gap-3 p-3 rounded-xl border border-primary bg-primary/5 cursor-pointer transition-all select-none group hover:bg-primary/10 w-full ${alreadyAdded ? 'opacity-50 cursor-not-allowed' : ''}">
                        <input type="checkbox"
                            class="kategori-checkbox w-4 h-4 accent-primary rounded focus:ring-primary"
                            value="${cat.id}" data-id="${cat.id}" data-dbid="${cat.db_id}" data-nama="${escVal}" ${alreadyAdded ? 'checked disabled' : ''}>
                        <div class="flex-1 min-w-0" onclick="${alreadyAdded ? '' : 'this.previousElementSibling.click()'}">
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

                btnEdit.addEventListener('click', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    viewMode.classList.add('hidden');
                    editMode.classList.remove('hidden');
                    editMode.classList.add('flex');
                    editInput.value = checkbox.dataset.nama;
                    editInput.focus();
                });

                btnCancel.addEventListener('click', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    editMode.classList.add('hidden');
                    editMode.classList.remove('flex');
                    viewMode.classList.remove('hidden');
                });

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

            } else {
                li.innerHTML = `
                    <label class="flex items-center gap-3 p-3 rounded-xl border ${
                        alreadyAdded
                            ? 'border-slate-200 bg-white opacity-50 cursor-not-allowed'
                            : 'border-table-border bg-white hover:border-primary hover:bg-primary/5 cursor-pointer'
                    } transition-all select-none">
                        <input type="checkbox"
                            class="kategori-checkbox w-4 h-4 accent-primary rounded focus:ring-primary"
                            value="${cat.id}" data-id="${cat.id}" data-dbid="${cat.db_id || ''}" data-nama="${cat.nama}"
                            ${alreadyAdded ? 'checked disabled' : ''}>
                        <span class="text-xs font-medium text-table-body">${cat.nama}</span>
                        ${alreadyAdded ? '<span class="ml-auto text-[10px] text-table-subtle italic">Sudah ditambahkan</span>' : ''}
                    </label>`;
            }
            kategoriModalList.appendChild(li);
        });
        updateModalInfo();
        kategoriModalOverlay.classList.remove('hidden');
        kategoriModalOverlay.classList.add('flex');
    });
}

export function closeKategoriModal() {
    if (!kategoriModalOverlay) return;
    kategoriModalOverlay.classList.add('hidden');
    kategoriModalOverlay.classList.remove('flex');
}

export function updateModalInfo() {
    const total = kategoriModalList
        ? kategoriModalList.querySelectorAll('.kategori-checkbox:not([disabled]):checked').length
        : 0;
    if (kategoriModalInfo) kategoriModalInfo.textContent = total + ' kategori dipilih';
}

export function appendCategoryRow(cat) {
    const emptyRow = tbody.querySelector('#rab-tbody-empty');
    if (emptyRow) emptyRow.remove();

    const catTr = document.createElement('tr');
    catTr.className  = 'rab-category bg-table-category text-white';
    catTr.dataset.cat = cat.id;
    catTr.innerHTML = `
        <td class="w-12 md:w-14 px-3 md:px-5 py-2.5 md:py-3 text-center">
            <button class="edit-cat-toggle-btn relative flex items-center justify-center w-5 h-5 mx-auto focus:outline-none"
                data-cat="${cat.id}" title="Buka / Tutup">
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
            <span class="flex items-center gap-2">
                <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                ${cat.nama}
            </span>
        </td>
        <td class="px-2 md:px-3 py-2.5 md:py-3 text-center">
            <div class="inline-flex items-center gap-1">
                <button class="add-subitem-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 text-white transition-colors duration-150 focus:outline-none"
                    data-cat="${cat.id}" data-catname="${cat.nama}" data-dbid="${cat.db_id || ''}" title="Tambah AHS">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <button class="del-cat-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/80 text-white/70 hover:text-white transition-colors duration-150 focus:outline-none"
                    data-cat="${cat.id}" title="Hapus semua item kategori ini">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>`;
    tbody.appendChild(catTr);

    const placeholderTr = document.createElement('tr');
    placeholderTr.className = `subrow-placeholder-${cat.id} bg-table-row border-b border-table-border`;
    placeholderTr.innerHTML = `<td colspan="12" class="px-5 py-2.5 text-center text-table-subtle text-xs italic">Belum ada item — klik Tambah untuk menambahkan.</td>`;
    tbody.appendChild(placeholderTr);

    bindAddSubItemRow(catTr);
    bindToggleRow(catTr);
}

export function bindAddSubItemRow(catTr) {
    catTr.querySelectorAll('.add-subitem-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            try {
                sessionStorage.setItem('rab_tambah_ahs_cat',     btn.dataset.cat);
                sessionStorage.setItem('rab_tambah_ahs_catname', btn.dataset.catname || '');
                sessionStorage.setItem('rab_tambah_ahs_dbid',    btn.dataset.dbid   || '');
                sessionStorage.setItem('rab_return_url', window.location.href);
            } catch (_) {}
            window.location.href = (window.RAB_INIT && window.RAB_INIT.tambahAhsUrl)
                ? window.RAB_INIT.tambahAhsUrl
                : '/menu-rap/tambah-ahs';
        });
    });

    catTr.querySelectorAll('.del-cat-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.stopPropagation();

            // Get the category name from the row for the dialog
            const catName = catTr.querySelector('td:nth-child(2) span:last-child')?.textContent.trim()
                         || btn.dataset.cat
                         || 'kategori ini';

            const confirmed = await confirmDeleteCategory(catName);
            if (!confirmed) return;

            const catId = btn.dataset.cat;

            // Remove all sub-items for this category
            tbody.querySelectorAll('.subrow-item-' + catId).forEach(r => r.remove());

            // Show placeholder if none exists
            if (!tbody.querySelector('.subrow-placeholder-' + catId)) {
                const ph = document.createElement('tr');
                ph.className = `subrow-placeholder-${catId} bg-table-row border-b border-table-border`;
                ph.innerHTML = `<td colspan="12" class="px-5 py-2.5 text-center text-table-subtle text-xs italic">Belum ada item — klik Tambah untuk menambahkan.</td>`;
                catTr.after(ph);
            }

            toast.show(`Semua item di "${catName}" berhasil dihapus`, 'info', 2500);
        });
    });
}

export function bindToggleRow(catTr) {
    catTr.querySelectorAll('.edit-cat-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const catId  = btn.dataset.cat;
            const plus   = btn.querySelector('.edit-cat-icon-plus');
            const minus  = btn.querySelector('.edit-cat-icon-minus');
            const targets = tbody.querySelectorAll(`.subrow-placeholder-${catId}, .subrow-item-${catId}`);
            const isOpen  = targets.length && !targets[0].classList.contains('hidden');
            targets.forEach(r => r.classList.toggle('hidden', isOpen));
            if (plus)  plus.classList.toggle('hidden', !isOpen);
            if (minus) minus.classList.toggle('hidden',  isOpen);
        });
    });
}
