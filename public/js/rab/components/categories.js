import {
    kategoriModalOverlay,
    kategoriModalList,
    kategoriModalConfirm,
    state
} from '../core/state.js';

import { fetchKategoriMaster } from '../core/data.js';
import { confirmAction } from '../../shared/ui/confirm.js';

export async function openKategoriModal() {
    if (!kategoriModalOverlay || !kategoriModalList) return;

    kategoriModalOverlay.classList.remove('hidden');
    kategoriModalOverlay.classList.add('flex');

    kategoriModalList.innerHTML = `
        <li class="text-xs text-slate-500 px-2 py-3">Memuat kategori...</li>
    `;

    try {
        const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;

        if (!idProject) {
            throw new Error('ID project tidak ditemukan');
        }

        const categories = await fetchKategoriMaster(idProject);

        if (!categories.length) {
            kategoriModalList.innerHTML = `
                <li class="text-xs text-slate-500 px-2 py-3">Belum ada kategori tersedia.</li>
            `;
            updateModalInfo();
            return;
        }

        const activeIds = (state.activeCategories || []).map(c => String(c.id));

        kategoriModalList.innerHTML = categories.map(cat => {
            const isActive = activeIds.includes(String(cat.id));
            const disabledAttr = isActive ? 'disabled checked' : '';
            const bgClass = isActive ? 'bg-slate-50 opacity-60 cursor-not-allowed border-slate-200' : 'border-slate-200 hover:border-primary hover:bg-primary/5 cursor-pointer';
            const textClass = isActive ? 'text-slate-400' : 'text-slate-700';

            const actionButtons = cat.jenis === 'custom' ? `
                <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1.5 action-buttons-container" onclick="event.preventDefault(); event.stopPropagation();">
                    <button type="button" class="btn-edit-master p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors focus:outline-none" data-id="${cat.id}" data-nama="${cat.nama}" title="Edit Kategori">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button type="button" class="btn-save-master hidden p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors focus:outline-none" data-id="${cat.id}" title="Simpan Perubahan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button type="button" class="btn-cancel-master hidden p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors focus:outline-none" data-id="${cat.id}" title="Batal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <button type="button" class="btn-delete-master p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors focus:outline-none" data-id="${cat.id}" data-nama="${cat.nama}" title="Hapus Kategori">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            ` : '';

            const rightContent = isActive ? `<span class="ml-auto text-[10px] bg-emerald-50 text-emerald-600 border border-emerald-200 px-2 py-0.5 rounded-full font-medium">Terpilih</span>` : actionButtons;

            return `
            <li class="group category-item">
                <label class="flex items-center gap-3 p-3 rounded-xl border ${bgClass} transition-all">
                    <input
                        type="checkbox"
                        class="kategori-checkbox w-4 h-4 accent-primary rounded disabled:opacity-50"
                        value="${cat.id}"
                        data-id="${cat.id}"
                        data-nama="${cat.nama}"
                        ${disabledAttr}
                    >
                    <span class="text-sm ${textClass} category-name-text">${cat.nama}</span>
                    <input type="text" class="category-name-input hidden flex-1 text-sm bg-white border border-slate-300 px-2 py-1 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="${cat.nama}">
                    ${rightContent}
                </label>
            </li>
        `}).join('');

        updateModalInfo();
        setTimeout(() => bindMasterCategoryActions(), 0);
    } catch (err) {
        console.error(err);
        kategoriModalList.innerHTML = `
            <li class="text-xs text-red-500 px-2 py-3">Gagal mengambil kategori master.</li>
        `;
    }
}

export function closeKategoriModal() {
    if (!kategoriModalOverlay) return;
    kategoriModalOverlay.classList.add('hidden');
    kategoriModalOverlay.classList.remove('flex');
}

export function updateModalInfo() {
    if (!kategoriModalList || !kategoriModalConfirm) return;

    const checked = kategoriModalList.querySelectorAll('.kategori-checkbox:checked').length;
    kategoriModalConfirm.disabled = checked === 0;
}

export function bindMasterCategoryActions() {
    if (!kategoriModalList) return;

    // Delete Master
    kategoriModalList.querySelectorAll('.btn-delete-master').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.dataset.id;
            const nama = this.dataset.nama;

            const ok = await confirmAction(
                'Hapus Kategori?',
                `Yakin ingin menghapus kategori custom <strong>"${nama}"</strong>?`,
                'Ya, Hapus'
            );
            if (!ok) return;

            try {
                const res = await fetch(`/api/rap/kategori-master/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal menghapus kategori');

                if (window.Toast) window.Toast.show(`Kategori "${nama}" berhasil dihapus`, 'success');
                openKategoriModal(); // Reload the list
            } catch (err) {
                console.error(err);
                alert(err.message || 'Terjadi kesalahan saat menghapus kategori');
            }
        });
    });

    // Edit Master Inline Setup
    kategoriModalList.querySelectorAll('.btn-edit-master').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const li = this.closest('li.category-item');
            const textEl = li.querySelector('.category-name-text');
            const inputEl = li.querySelector('.category-name-input');
            const editBtn = this;
            const saveBtn = li.querySelector('.btn-save-master');
            const deleteBtn = li.querySelector('.btn-delete-master');
            const cancelBtn = li.querySelector('.btn-cancel-master');
            const actionContainer = li.querySelector('.action-buttons-container');

            // Set input value to current name and display elements
            inputEl.value = editBtn.dataset.nama;
            textEl.classList.add('hidden');
            inputEl.classList.remove('hidden');
            editBtn.classList.add('hidden');
            deleteBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');
            
            // Keep container visible during edit
            actionContainer.classList.remove('opacity-0', 'group-hover:opacity-100');
            
            inputEl.focus();

            // Handle Esc to cancel and Enter to save
            const keyHandler = (ev) => {
                if (ev.key === 'Escape') {
                    ev.preventDefault();
                    resetEditState();
                } else if (ev.key === 'Enter') {
                    ev.preventDefault();
                    saveBtn.click();
                }
            };
            
            const resetEditState = () => {
                textEl.classList.remove('hidden');
                inputEl.classList.add('hidden');
                editBtn.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                saveBtn.classList.add('hidden');
                cancelBtn.classList.add('hidden');
                actionContainer.classList.add('opacity-0', 'group-hover:opacity-100');
                inputEl.removeEventListener('keydown', keyHandler);
            };

            inputEl.addEventListener('keydown', keyHandler);
            cancelBtn.onclick = (ev) => {
                ev.preventDefault();
                ev.stopPropagation();
                resetEditState();
            };
            
            // Attached resetEditState to btn for reuse on cancel save
            saveBtn._resetEditState = resetEditState;
        });
    });

    // Save Master Inline Execution
    kategoriModalList.querySelectorAll('.btn-save-master').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const li = this.closest('li.category-item');
            const id = this.dataset.id;
            const inputEl = li.querySelector('.category-name-input');
            const editBtn = li.querySelector('.btn-edit-master');
            
            const oldNama = editBtn.dataset.nama;
            const newNama = inputEl.value;

            // Simple validation: nothing changed or empty
            if (!newNama || newNama.trim() === '' || newNama === oldNama) {
                if(this._resetEditState) this._resetEditState();
                return;
            }

            try {
                this.disabled = true;
                const origHTML = this.innerHTML;
                this.innerHTML = `<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>`;

                const res = await fetch(`/api/rap/kategori-master/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ nama: newNama.trim() })
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal mengubah kategori');

                if (window.Toast) window.Toast.show(`Kategori "${oldNama}" berhasil diubah menjadi "${newNama.trim()}"`, 'success');
                openKategoriModal(); // Reload the list fully
            } catch (err) {
                console.error(err);
                this.disabled = false;
                alert(err.message || 'Terjadi kesalahan saat mengubah kategori');
                if(this._resetEditState) this._resetEditState();
            }
        });
    });
}