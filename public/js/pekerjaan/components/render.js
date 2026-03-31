/**
 * pekerjaan/components/render.js
 * Renders loading state, table rows, pagination, and binds checkbox/kustom action events.
 */

import { state, tbody, countEl, paginationEl, paginationInfo,
         submitBtn, selectedCount, PAGE_SIZE } from '../core/state.js';
import { updatePekerjaanKustom, deletePekerjaanKustom } from '../core/data.js';
import { toast } from '../../shared/ui/toast.js';

const BADGE_MAP = [
    { keyword: 'SNI',       cls: 'bg-emerald-100 text-emerald-700' },
    { keyword: 'PUPR',      cls: 'bg-violet-100 text-violet-700'   },
    { keyword: 'Empiris',   cls: 'bg-amber-100 text-amber-700'     },
    { keyword: 'Estimator', cls: 'bg-rose-100 text-rose-700'       },
];

function getSumberBadge(sumber) {
    const text  = (sumber || '').toUpperCase();
    const match = BADGE_MAP.find(b => text.includes(b.keyword.toUpperCase()));
    return match ? match.cls : 'bg-blue-100 text-blue-700';
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
        const rowNum    = start + idx;
        const isChecked = !!state.selected[item.id];
        const rowBg     = rowNum % 2 === 0 ? 'bg-table-row' : 'bg-white';
        const badgeCls  = getSumberBadge(item.sumber);
        const safeName  = (item.nama || '').replace(/"/g, '&quot;');

        // Kolom aksi:
        // - Baris bawaan sistem  → hanya checkbox (pilih ke RAB)
        // - Baris kustom         → checkbox (pilih ke RAB) + Edit + Delete
        const checkboxEl = `<input type="checkbox"
                    class="tambah-ahs-checkbox w-4 h-4 rounded border-table-border text-primary accent-primary cursor-pointer"
                    data-id="${item.id}"
                    data-nama="${safeName}"
                    data-satuan="${item.satuan||''}"
                    data-harga="${item.harga||0}"
                    data-sumber="${item.sumber||''}"
                    ${isChecked ? 'checked' : ''}/>`;

        const actionCol = item.is_custom
            ? `<td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                <div class="inline-flex items-center gap-1.5">
                    ${checkboxEl}
                    <button type="button" class="kustom-edit-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-600 transition-colors focus:outline-none"
                        data-dbid="${item.db_id}" data-nama="${safeName}" data-satuan="${item.satuan||''}" title="Edit">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button type="button" class="kustom-del-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 transition-colors focus:outline-none"
                        data-dbid="${item.db_id}" data-nama="${safeName}" title="Hapus">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
              </td>`
            : `<td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                ${checkboxEl}
              </td>`;

        return `
            <tr class="tambah-ahs-row border-b border-table-border/60 hover:bg-primary/5 transition-colors duration-100 ${!item.is_custom && isChecked ? 'bg-primary/5' : rowBg}"
                data-id="${item.id}" data-custom="${item.is_custom ? '1' : '0'}">
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle font-medium tabular-nums">${rowNum}</td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-table-strong truncate max-w-xs" title="${item.nama||''}">
                    ${item.nama||''}
                    ${item.is_custom ? '<span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary/10 text-primary uppercase tracking-wide">Kustom</span>' : ''}
                </td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle whitespace-nowrap">${item.satuan||'-'}</td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] md:text-[10px] font-semibold whitespace-nowrap ${badgeCls}">${item.sumber||''}</span>
                </td>
                ${actionCol}
            </tr>`;
    }).join('');

    renderPagination(total, page);
    bindCheckboxes();
    bindKustomActions();
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
            window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
        });
    });
}

export function bindCheckboxes() {
    tbody.querySelectorAll('.tambah-ahs-row[data-custom="0"]').forEach(row => {
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

/**
 * Bind inline Edit and Delete for custom (kustom) rows.
 */
export function bindKustomActions() {
    // ── Edit ────────────────────────────────────────────────────────────────
    tbody.querySelectorAll('.kustom-edit-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.stopPropagation();
            const dbId   = btn.dataset.dbid;
            const oldName = btn.dataset.nama;
            const oldSat  = btn.dataset.satuan;

            // Find the name <td> in parent row
            const row     = btn.closest('tr');
            const nameTd  = row ? row.querySelector('td:nth-child(2)') : null;
            if (!nameTd) return;

            // Replace name cell with inline editor
            nameTd.innerHTML = `
                <div class="flex items-center gap-1.5">
                    <input type="text" class="kustom-inline-name flex-1 text-xs px-2 py-1 border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-400" value="${oldName}">
                    <input type="text" class="kustom-inline-sat w-16 text-xs px-2 py-1 border border-blue-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-400" value="${oldSat}" placeholder="satuan">
                    <button type="button" class="kustom-save-btn inline-flex items-center justify-center w-6 h-6 rounded bg-emerald-500 hover:bg-emerald-600 text-white focus:outline-none" title="Simpan">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button type="button" class="kustom-cancel-btn inline-flex items-center justify-center w-6 h-6 rounded bg-white hover:bg-slate-100 border border-slate-200 text-slate-500 focus:outline-none" title="Batal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>`;

            const nameInput = nameTd.querySelector('.kustom-inline-name');
            const satInput  = nameTd.querySelector('.kustom-inline-sat');
            nameInput.focus();

            nameTd.querySelector('.kustom-cancel-btn').addEventListener('click', () => {
                window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
            });

            const doSave = async () => {
                const newName = nameInput.value.trim();
                const newSat  = satInput.value.trim();
                if (!newName) { nameInput.focus(); return; }
                try {
                    await updatePekerjaanKustom(dbId, { nama_pekerjaan: newName, satuan: newSat });
                    toast.show('Pekerjaan diperbarui', 'success', 2000);
                    window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
                } catch (err) {
                    toast.show('Gagal memperbarui pekerjaan', 'error', 2500);
                }
            };

            nameTd.querySelector('.kustom-save-btn').addEventListener('click', doSave);
            nameInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doSave(); } });
        });
    });

    // ── Delete ──────────────────────────────────────────────────────────────
    tbody.querySelectorAll('.kustom-del-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.stopPropagation();
            const dbId  = btn.dataset.dbid;
            const nama  = btn.dataset.nama;
            if (!confirm(`Hapus pekerjaan kustom "${nama}"?`)) return;
            try {
                await deletePekerjaanKustom(dbId);
                toast.show(`Pekerjaan "${nama}" dihapus`, 'info', 2500);
                window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: state.page } }));
            } catch (err) {
                toast.show('Gagal menghapus pekerjaan', 'error', 2500);
            }
        });
    });
}

export function updateSubmitBar() {
    const count = Object.keys(state.selected).length;
    if (submitBtn)     submitBtn.disabled    = count === 0;
    if (selectedCount) selectedCount.textContent = count > 0 ? `${count} item dipilih` : 'Belum ada item dipilih';
}
