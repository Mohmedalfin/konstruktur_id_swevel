/**
 * pekerjaan/components/render.js
 * Renders loading state, table rows, pagination buttons, and binds checkbox events.
 */

import { state, tbody, countEl, paginationEl, paginationInfo,
         submitBtn, selectedCount, PAGE_SIZE } from '../core/state.js';

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
        return `
            <tr class="tambah-ahs-row border-b border-table-border/60 hover:bg-primary/5 transition-colors duration-100 ${isChecked ? 'bg-primary/5' : rowBg}"
                data-id="${item.id}">
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle font-medium tabular-nums">${rowNum}</td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-table-strong truncate max-w-xs" title="${item.nama}">${item.nama}</td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle whitespace-nowrap">${item.satuan}</td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] md:text-[10px] font-semibold whitespace-nowrap ${badgeCls}">${item.sumber}</span>
                </td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <input type="checkbox"
                        class="tambah-ahs-checkbox w-4 h-4 rounded border-table-border text-primary accent-primary cursor-pointer"
                        data-id="${item.id}"
                        data-nama="${item.nama}"
                        data-satuan="${item.satuan}"
                        data-harga="${item.harga}"
                        data-sumber="${item.sumber}"
                        ${isChecked ? 'checked' : ''}/>
                </td>
            </tr>`;
    }).join('');

    renderPagination(total, page);
    bindCheckboxes();
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
