/**
 * ahs/components/render.js
 * Renders editable AHS table rows with autocomplete, recalc, and delete.
 */

import { state, tbody, totalBahanEl, totalAlatEl, totalUpahEl, totalKeselEl } from '../core/state.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { toast }        from '../../shared/ui/toast.js';

export const tipeConfig = {
    bahan: { label: 'Bahan', badge: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300' },
    alat:  { label: 'Alat',  badge: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300'          },
    upah:  { label: 'Upah',  badge: 'bg-violet-100 text-violet-700 ring-1 ring-violet-300'    },
};

export function renderRow(rowData, isNew = false) {
    const cfg    = tipeConfig[rowData.tipe] || tipeConfig.bahan;
    const jumlah = (parseFloat(rowData.koefisien) || 0) * (parseFloat(rowData.hargaSatuan) || 0);
    const tr     = document.createElement('tr');
    tr.dataset.id   = rowData.id;
    tr.dataset.tipe = rowData.tipe;
    tr.className    = 'ahs-row border-b border-table-border hover:bg-slate-50 transition-colors duration-100';

    tr.innerHTML = `
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center text-table-subtle">
            <span class="ahs-rownum">-</span>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] md:text-[11px] font-semibold ${cfg.badge}">
                ${cfg.label}
            </span>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 relative">
            <input type="text" value="${escHtml(rowData.uraian)}"
                placeholder="Nama bahan / alat / pekerja"
                class="ahs-uraian w-full bg-transparent border-b border-transparent hover:border-table-border focus:border-primary text-[11px] md:text-[13px] text-table-medium placeholder-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}" autocomplete="off"/>
            <ul class="ahs-autocomplete hidden absolute left-0 right-0 top-full mt-1 bg-white border border-table-border rounded-lg shadow-xl z-30 max-h-48 overflow-y-auto text-[12px]"></ul>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5">
            <input type="text" value="${escHtml(rowData.merk || '')}" placeholder="Merk"
                class="ahs-merk w-full bg-transparent border-b border-transparent hover:border-table-border focus:border-primary text-[11px] md:text-[13px] text-table-medium placeholder-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}"/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5">
            <input type="text" value="${escHtml(rowData.spesifikasi || '')}" placeholder="Spesifikasi"
                class="ahs-spesifikasi w-full bg-transparent border-b border-transparent hover:border-table-border focus:border-primary text-[11px] md:text-[13px] text-table-medium placeholder-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}"/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <input type="number" min="0" step="any" value="${rowData.koefisien}"
                class="ahs-koef w-20 px-2 py-1 text-[11px] md:text-[13px] border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary tabular-nums bg-white"
                data-id="${rowData.id}"/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <input type="text" value="${escHtml(rowData.satuan)}" placeholder="m³"
                class="ahs-satuan w-16 px-2 py-1 text-[11px] md:text-[13px] border border-table-border rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary bg-white"
                data-id="${rowData.id}"/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-right">
            <input type="number" min="0" step="any" value="${rowData.hargaSatuan}"
                class="ahs-harga-satuan w-32 px-2 py-1 text-[11px] md:text-[13px] border border-table-border rounded text-right focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary tabular-nums bg-white"
                data-id="${rowData.id}"/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5">
            <input type="text" value="${escHtml(rowData.sumber || '')}" placeholder="Sumber"
                class="ahs-sumber w-full bg-transparent border-b border-transparent hover:border-table-border focus:border-primary text-[11px] md:text-[13px] text-table-medium placeholder-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}"/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong text-[11px] md:text-[13px]">
            <span class="ahs-jumlah-cell">${fmt(jumlah)}</span>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <button type="button" class="ahs-del-btn inline-flex items-center justify-center w-6 h-6 rounded-md text-table-subtle hover:text-red-500 hover:bg-red-50 transition-colors focus:outline-none" title="Hapus">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>`;

    // Insertion logic for grouping
    const header = _ensureHeader(rowData.tipe);
    const lastRowOfTipe = Array.from(tbody.querySelectorAll(`.ahs-row[data-tipe="${rowData.tipe}"]`)).pop();

    if (lastRowOfTipe) {
        lastRowOfTipe.after(tr);
    } else {
        header.after(tr);
    }

    _bindRowInputs(tr);
    renumberRows(); // Count correctly including new row
    if (isNew) setTimeout(() => tr.querySelector('.ahs-uraian')?.focus(), 50);
}

function _ensureHeader(tipe) {
    let header = tbody.querySelector(`.ahs-category-header[data-tipe="${tipe}"]`);
    if (!header) {
        header = document.createElement('tr');
        header.className = 'ahs-category-header bg-slate-100/80 border-y border-table-border';
        header.dataset.tipe = tipe;
        header.innerHTML = `
            <td colspan="11" class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">
                ${tipeConfig[tipe]?.label || tipe}
            </td>`;

        // Maintain order: Bahan -> Alat -> Upah
        const types = ['bahan', 'alat', 'upah'];
        const myIdx = types.indexOf(tipe);
        let inserted = false;

        for (let i = myIdx + 1; i < types.length; i++) {
            const nextHeader = tbody.querySelector(`.ahs-category-header[data-tipe="${types[i]}"]`);
            if (nextHeader) {
                nextHeader.before(header);
                inserted = true;
                break;
            }
        }

        if (!inserted) {
            tbody.appendChild(header);
        }
    }
    return header;
}

function _bindRowInputs(tr) {
    const koefInput   = tr.querySelector('.ahs-koef');
    const hargaInput  = tr.querySelector('.ahs-harga-satuan');
    const jumlahCell  = tr.querySelector('.ahs-jumlah-cell');
    const uraianInput = tr.querySelector('.ahs-uraian');
    const acList      = tr.querySelector('.ahs-autocomplete');
    const tipe        = tr.dataset.tipe;

    function recalcRow() {
        const koef  = parseFloat(koefInput?.value) || 0;
        const harga = parseFloat(hargaInput?.value) || 0;
        if (jumlahCell) jumlahCell.textContent = fmt(koef * harga);
        recalcTotals();
    }
    koefInput?.addEventListener('input', recalcRow);
    hargaInput?.addEventListener('input', recalcRow);

    tr.querySelector('.ahs-del-btn')?.addEventListener('click', function () {
        const tipe = tr.dataset.tipe;
        tr.remove();

        // Remove header if no rows left for this type
        const remainingRows = tbody.querySelectorAll(`.ahs-row[data-tipe="${tipe}"]`);
        if (remainingRows.length === 0) {
            tbody.querySelector(`.ahs-category-header[data-tipe="${tipe}"]`)?.remove();
        }

        renumberRows();
        recalcTotals();
        toast.show('Item berhasil dihapus dari rincian', 'info', 2500);
    });

    // Autocomplete
    uraianInput?.addEventListener('input', function () {
        const q = uraianInput.value.trim().toLowerCase();
        if (!q) { _hideAc(acList); return; }
        const matches = state.ahsDatabase.filter(item =>
            item.tipe === tipe && item.uraian.toLowerCase().includes(q)
        ).slice(0, 8);
        if (matches.length === 0) { _hideAc(acList); return; }

        acList.innerHTML = matches.map(m => `
            <li class="flex items-center justify-between px-3 py-2 hover:bg-primary/5 cursor-pointer transition-colors gap-2"
                data-uraian="${escHtml(m.uraian)}" data-satuan="${escHtml(m.satuan)}" data-harga="${m.hargaSatuan}">
                <span class="flex-1 text-table-medium truncate">${escHtml(m.uraian)}</span>
                <span class="text-table-subtle shrink-0">${escHtml(m.satuan)} · ${fmt(m.hargaSatuan)}</span>
            </li>`).join('');

        acList.querySelectorAll('li').forEach(li => {
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                uraianInput.value = li.dataset.uraian;
                const satuanEl = tr.querySelector('.ahs-satuan');
                if (satuanEl)  satuanEl.value  = li.dataset.satuan;
                if (hargaInput) hargaInput.value = li.dataset.harga;
                recalcRow();
                _hideAc(acList);
            });
        });
        acList.classList.remove('hidden');
        state.autocompleteActive = acList;
    });
    uraianInput?.addEventListener('blur',    () => setTimeout(() => _hideAc(acList), 150));
    uraianInput?.addEventListener('keydown', e => { if (e.key === 'Escape') _hideAc(acList); });
}

function _hideAc(list) {
    if (list) list.classList.add('hidden');
    state.autocompleteActive = null;
}

export function recalcTotals() {
    const t = { bahan: 0, alat: 0, upah: 0 };
    tbody.querySelectorAll('.ahs-row').forEach(tr => {
        const tipe  = tr.dataset.tipe;
        const koef  = parseFloat(tr.querySelector('.ahs-koef')?.value) || 0;
        const harga = parseFloat(tr.querySelector('.ahs-harga-satuan')?.value) || 0;
        if (t[tipe] !== undefined) t[tipe] += koef * harga;
    });
    if (totalBahanEl) totalBahanEl.textContent = fmt(t.bahan);
    if (totalAlatEl)  totalAlatEl.textContent  = fmt(t.alat);
    if (totalUpahEl)  totalUpahEl.textContent  = fmt(t.upah);
    if (totalKeselEl) totalKeselEl.textContent = fmt(t.bahan + t.alat + t.upah);
}

export function renumberRows() {
    let n = 0;
    tbody.querySelectorAll('.ahs-row .ahs-rownum').forEach(el => el.textContent = ++n);
    state.rowCounter = n;
}

export function addRow(tipe) {
    document.getElementById('ahs-empty-row')?.remove();
    renderRow({ id: Date.now(), tipe, uraian: '', merk: '', spesifikasi: '', koefisien: 1, satuan: '', hargaSatuan: 0, sumber: '' }, true);
    recalcTotals();
    const tipeLabel = tipeConfig[tipe]?.label || tipe;
    toast.show(`Baris ${tipeLabel} baru ditambahkan`, 'success', 2000);
}

