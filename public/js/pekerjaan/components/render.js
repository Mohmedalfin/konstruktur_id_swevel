/**
 * pekerjaan/components/render.js
 * Renders loading state, table rows, pagination buttons, and binds checkbox events.
 */

import { state, tbody, countEl, paginationEl, paginationInfo,
         submitBtn, selectedCount, PAGE_SIZE } from '../core/state.js';
import { toast } from '../../shared/ui/toast.js';
import { confirmAction } from '../../shared/ui/confirm.js';

/**
 * Map keyword → Tailwind badge classes.
 * Matches partial text from 'sumber' (raw keterangan from DB).
 */
const BADGE_MAP = [
    { keyword: 'SNI',       cls: 'bg-emerald-100 text-emerald-700' },
    { keyword: 'PUPR',      cls: 'bg-violet-100 text-violet-700'   },
    { keyword: 'Empiris',   cls: 'bg-amber-100 text-amber-700'     },
    { keyword: 'Estimator', cls: 'bg-rose-100 text-rose-700'       },
];

function getSumberBadge(sumber) {
    const text = (sumber || '').toUpperCase();
    const match = BADGE_MAP.find(b => text.includes(b.keyword.toUpperCase()));
    return match ? match.cls : 'bg-blue-100 text-blue-700'; // default = Proyek Terkini
}

export function renderLoading() {
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Memuat data…
            </td>
        </tr>`;
}

export function renderRows(result) {
    const { total, page, data } = result;
    const start = (page - 1) * PAGE_SIZE + 1;
    const end   = Math.min(page * PAGE_SIZE, total);

    if (countEl) {
        countEl.textContent = total > 0
            ? `Menampilkan ${start} sampai ${end} dari ${total.toLocaleString('id-ID')} data`
            : 'Tidak ada data yang cocok';
    }
    if (paginationInfo) {
        paginationInfo.textContent = total > 0
            ? `Halaman ${page} dari ${Math.ceil(total / PAGE_SIZE)}`
            : '';
    }

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-table-subtle text-xs italic">Tidak ada data pekerjaan yang cocok.</td></tr>`;
        renderPagination(0, 1);
        return;
    }

    tbody.innerHTML = data.map(function (item, idx) {
        const rowNum     = start + idx;
        const isChecked  = !!state.selected[item.id];
        const rowBg      = rowNum % 2 === 0 ? 'bg-table-row' : 'bg-white';
        const badgeCls   = getSumberBadge(item.sumber);

        const actionButtons = item.sumber === 'Proyek Terkini' ? `
            <div class="absolute left-[calc(50%+12px)] top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 action-buttons-container bg-white/50 px-1 rounded-lg backdrop-blur-sm" onclick="event.preventDefault(); event.stopPropagation();">
                <button type="button" class="btn-edit-pekerjaan p-1 text-blue-500 hover:bg-blue-50 rounded transition-colors focus:outline-none" data-id="${item.id}" data-nama="${item.nama}" data-satuan="${item.satuan}" title="Edit Pekerjaan">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button type="button" class="btn-save-pekerjaan hidden p-1 text-emerald-500 hover:bg-emerald-50 rounded transition-colors focus:outline-none" data-id="${item.id}" title="Simpan">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button type="button" class="btn-cancel-pekerjaan hidden p-1 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors focus:outline-none" data-id="${item.id}" title="Batal">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <button type="button" class="btn-delete-pekerjaan p-1 text-red-500 hover:bg-red-50 rounded transition-colors focus:outline-none" data-id="${item.id}" data-nama="${item.nama}" title="Hapus Pekerjaan">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        ` : '';

        return `
            <tr class="tambah-ahs-row border-b border-table-border/60 hover:bg-primary/5 transition-colors duration-100 ${isChecked ? 'bg-primary/5' : rowBg} group"
                data-id="${item.id}">
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle font-medium tabular-nums">${rowNum}</td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-table-strong truncate max-w-xs">
                    <div class="flex items-center">
                        <span class="nama-text truncate w-full" title="${item.nama}">${item.nama}</span>
                        <input type="text" class="nama-input hidden flex-1 text-xs bg-white border border-slate-300 px-2 py-1 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="${item.nama}">
                    </div>
                </td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle whitespace-nowrap">
                    <span class="satuan-text">${item.satuan}</span>
                    <input type="text" class="satuan-input hidden w-16 text-center text-xs bg-white border border-slate-300 px-1 py-1 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="${item.satuan}">
                </td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] md:text-[10px] font-semibold whitespace-nowrap ${badgeCls}">${item.sumber}</span>
                </td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <div class="relative inline-flex items-center justify-center">
                        ${actionButtons}
                        <input type="checkbox"
                            class="tambah-ahs-checkbox w-4 h-4 rounded border-table-border text-primary accent-primary cursor-pointer"
                            data-id="${item.id}"
                            data-nama="${item.nama}"
                            data-satuan="${item.satuan}"
                            data-harga="${item.harga}"
                            data-sumber="${item.sumber}"
                            ${isChecked ? 'checked' : ''}/>
                    </div>
                </td>
            </tr>`;
    }).join('');

    renderPagination(total, page);
    bindCheckboxes();
    bindPekerjaanActions();
}

export function renderPagination(total, current) {
    if (!paginationEl) return;
    const totalPages = Math.ceil(total / PAGE_SIZE);
    if (totalPages <= 1) { paginationEl.innerHTML = ''; return; }

    const btnBase     = 'inline-flex items-center justify-center w-7 h-7 text-xs rounded-md border transition-all duration-150 focus:outline-none';
    const btnActive   = `${btnBase} bg-primary text-white border-primary font-bold`;
    const btnIdle     = `${btnBase} bg-white border-table-border text-table-body hover:bg-primary/10 hover:border-primary/30`;
    const btnDisabled = `${btnBase} bg-white border-table-border text-table-muted opacity-50 cursor-not-allowed pointer-events-none`;

    const svgPrev = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>`;
    const svgNext = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>`;

    let html = `<button class="${current === 1 ? btnDisabled : btnIdle}" data-page="${current - 1}" ${current === 1 ? 'disabled' : ''}>${svgPrev}</button>`;

    const delta   = 2;
    const pagNums = [];
    for (let p = Math.max(1, current - delta); p <= Math.min(totalPages, current + delta); p++) pagNums.push(p);
    if (pagNums[0] > 1) {
        html += `<button class="${btnIdle}" data-page="1">1</button>`;
        if (pagNums[0] > 2) html += `<span class="px-1 text-table-subtle text-xs">…</span>`;
    }
    pagNums.forEach(p => { html += `<button class="${p === current ? btnActive : btnIdle}" data-page="${p}">${p}</button>`; });
    if (pagNums[pagNums.length - 1] < totalPages) {
        if (pagNums[pagNums.length - 1] < totalPages - 1) html += `<span class="px-1 text-table-subtle text-xs">…</span>`;
        html += `<button class="${btnIdle}" data-page="${totalPages}">${totalPages}</button>`;
    }
    html += `<button class="${current === totalPages ? btnDisabled : btnIdle}" data-page="${current + 1}" ${current === totalPages ? 'disabled' : ''}>${svgNext}</button>`;

    paginationEl.innerHTML = html;
    paginationEl.querySelectorAll('button[data-page]').forEach(btn => {
        btn.addEventListener('click', function () {
            state.page = parseInt(btn.dataset.page, 10);
            // Dispatch event so index.js can call load() — avoids circular imports
            window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
        });
    });
}

export function bindCheckboxes() {
    tbody.querySelectorAll('.tambah-ahs-row').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.tagName === 'INPUT') return;
            const cb = row.querySelector('.tambah-ahs-checkbox');
            if (cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
        });
    });

    tbody.querySelectorAll('.tambah-ahs-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const id  = cb.dataset.id;
            const row = cb.closest('tr');
            if (cb.checked) {
                state.selected[id] = { id, nama: cb.dataset.nama, satuan: cb.dataset.satuan, harga: parseFloat(cb.dataset.harga), sumber: cb.dataset.sumber };
                row.classList.add('bg-primary/5');
                row.classList.remove('bg-white', 'bg-table-row');
            } else {
                delete state.selected[id];
                const rowIdx = Array.from(tbody.querySelectorAll('tr')).indexOf(row);
                row.classList.remove('bg-primary/5');
                row.classList.add(rowIdx % 2 === 0 ? 'bg-table-row' : 'bg-white');
            }
            updateSubmitBar();
        });
    });
}

export function updateSubmitBar() {
    const count = Object.keys(state.selected).length;
    if (submitBtn)     submitBtn.disabled    = count === 0;
    if (selectedCount) selectedCount.textContent = count > 0 ? `${count} item dipilih` : 'Belum ada item dipilih';
}

export function bindPekerjaanActions() {
    // Delete Pekerjaan
    tbody.querySelectorAll('.btn-delete-pekerjaan').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.dataset.id;
            const nama = this.dataset.nama;

            const ok = await confirmAction(
                'Hapus Pekerjaan?',
                `Yakin ingin menghapus pekerjaan custom <strong>"${nama}"</strong>?`,
                'Ya, Hapus'
            );
            if (!ok) return;

            try {
                const res = await fetch(`/api/pekerjaan/custom/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal menghapus pekerjaan');

                toast.show(`Pekerjaan "${nama}" berhasil dihapus`, 'success');
                // Reload list
                window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
            } catch (err) {
                console.error(err);
                alert(err.message || 'Terjadi kesalahan saat menghapus pekerjaan');
            }
        });
    });

    // Edit Pekerjaan Setup
    tbody.querySelectorAll('.btn-edit-pekerjaan').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const tr = this.closest('tr');
            const namaText = tr.querySelector('.nama-text');
            const namaInput = tr.querySelector('.nama-input');
            const satuanText = tr.querySelector('.satuan-text');
            const satuanInput = tr.querySelector('.satuan-input');

            const editBtn = this;
            const saveBtn = tr.querySelector('.btn-save-pekerjaan');
            const deleteBtn = tr.querySelector('.btn-delete-pekerjaan');
            const cancelBtn = tr.querySelector('.btn-cancel-pekerjaan');
            const actionContainer = tr.querySelector('.action-buttons-container');

            // Set inputs to original data
            namaInput.value = editBtn.dataset.nama;
            satuanInput.value = editBtn.dataset.satuan;

            // Toggle UI
            namaText.classList.add('hidden');
            namaText.classList.remove('w-full');
            namaInput.classList.remove('hidden');

            satuanText.classList.add('hidden');
            satuanInput.classList.remove('hidden');

            editBtn.classList.add('hidden');
            deleteBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');

            actionContainer.classList.remove('opacity-0', 'group-hover:opacity-100');
            namaInput.focus();

            const resetEditState = () => {
                namaText.classList.remove('hidden');
                namaText.classList.add('w-full');
                namaInput.classList.add('hidden');

                satuanText.classList.remove('hidden');
                satuanInput.classList.add('hidden');

                editBtn.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                saveBtn.classList.add('hidden');
                cancelBtn.classList.add('hidden');

                actionContainer.classList.add('opacity-0', 'group-hover:opacity-100');
            };

            const keyHandler = (ev) => {
                if (ev.key === 'Escape') {
                    ev.preventDefault();
                    resetEditState();
                } else if (ev.key === 'Enter') {
                    ev.preventDefault();
                    saveBtn.click();
                }
            };

            namaInput.onkeydown = keyHandler;
            satuanInput.onkeydown = keyHandler;

            cancelBtn.onclick = (ev) => {
                ev.preventDefault();
                ev.stopPropagation();
                resetEditState();
            };

            saveBtn._resetEditState = resetEditState;
        });
    });

    // Execute Save Pekerjaan 
    tbody.querySelectorAll('.btn-save-pekerjaan').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const tr = this.closest('tr');
            const id = this.dataset.id;
            const editBtn = tr.querySelector('.btn-edit-pekerjaan');
            
            const oldNama = editBtn.dataset.nama;
            const oldSatuan = editBtn.dataset.satuan;
            
            const newNama = tr.querySelector('.nama-input').value.trim();
            const newSatuan = tr.querySelector('.satuan-input').value.trim() || 'm2';

            if (!newNama) {
                alert("Nama pekerjaan wajib diisi");
                return;
            }

            if (newNama === oldNama && newSatuan === oldSatuan) {
                if(this._resetEditState) this._resetEditState();
                return;
            }

            try {
                this.disabled = true;
                const origHTML = this.innerHTML;
                this.innerHTML = `<svg class="animate-spin w-3.5 h-3.5 opacity-75" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>`;

                const res = await fetch(`/api/pekerjaan/custom/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ nama: newNama, satuan: newSatuan })
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal mengubah pekerjaan');

                toast.show(`Pekerjaan "${oldNama}" berhasil diubah`, 'success');
                window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
                
            } catch (err) {
                console.error(err);
                this.disabled = false;
                alert(err.message || 'Terjadi kesalahan saat mengubah pekerjaan');
                if(this._resetEditState) this._resetEditState();
            }
        });
    });
}
