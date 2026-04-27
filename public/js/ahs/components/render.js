/**
 * ahs/components/render.js
 * Renders editable AHS table rows with autocomplete, recalc, and delete.
 */

import { state, tbody, totalKeselEl } from '../core/state.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { toast } from '../../shared/ui/toast.js';
import { confirmAction } from '../../shared/ui/confirm.js';
export const tipeConfig = {
    bahan: { label: 'BAHAN', prefix: 'A', badge: 'bg-primary/10 text-primary ring-1 ring-primary/20' },
    upah: { label: 'UPAH', prefix: 'B', badge: 'bg-violet-100 text-violet-700 ring-1 ring-violet-300' },
    alat: { label: 'ALAT', prefix: 'C', badge: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300' },
};

export function renderRow(rowData, isNew = false) {
    const cfg = tipeConfig[rowData.tipe] || tipeConfig.bahan;
    const hargaSatuan = (parseFloat(rowData.koefisien) || 0) * (parseFloat(rowData.hargaSatuan) || 0);
    const tr = document.createElement('tr');
    tr.dataset.id = rowData.id;
    tr.dataset.tipe = rowData.tipe;
    tr.className = `ahs-row border-b border-slate-200 hover:bg-blue-50/30 transition-colors duration-100 group ${isNew ? 'bg-blue-50/50 ring-1 ring-blue-200' : ''}`;

    tr.innerHTML = `
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center text-table-subtle">
            <span class="ahs-rownum text-xs font-medium">-</span>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 relative">
            <input type="text" value="${escHtml(rowData.uraian)}"
                placeholder="Nama bahan / alat / pekerja"
                class="ahs-uraian w-full bg-transparent border-b border-transparent hover:border-slate-200 focus:border-primary text-[11px] md:text-[13px] text-table-medium placeholder-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}" autocomplete="off" ${isNew ? '' : 'readonly'}/>
            <ul class="ahs-autocomplete hidden absolute left-0 right-0 top-full mt-1 bg-white border border-table-border rounded-lg shadow-xl z-30 max-h-48 overflow-y-auto text-[12px]"></ul>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <input type="number" min="0" step="any" value="${rowData.koefisien}"
                class="ahs-koef w-20 px-2 py-1 text-[11px] md:text-[13px] border border-slate-200 rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary tabular-nums bg-white disabled:bg-slate-50"
                data-id="${rowData.id}" ${isNew ? '' : 'readonly'}/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <input type="text" value="${escHtml(rowData.satuan)}" placeholder="m³"
                class="ahs-satuan w-16 px-2 py-1 text-[11px] md:text-[13px] border border-slate-200 rounded text-center focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary bg-white disabled:bg-slate-50"
                data-id="${rowData.id}" ${isNew ? '' : 'readonly'}/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-right">
            <input type="number" min="0" step="any" value="${rowData.hargaSatuan}"
                class="ahs-harga-dasar w-32 px-2 py-1 text-[11px] md:text-[13px] border border-slate-200 rounded text-right focus:outline-none focus:ring-1 focus:ring-primary/40 focus:border-primary tabular-nums bg-white disabled:bg-slate-50"
                data-id="${rowData.id}" ${isNew ? '' : 'readonly'}/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong text-[11px] md:text-[13px] bg-slate-50/50">
            <span class="ahs-jumlah-cell">${fmt(hargaSatuan)}</span>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5 text-center">
            <div class="inline-flex items-center gap-1 shadow-sm rounded-lg border border-slate-200 p-0.5 bg-white ${isNew ? '' : 'opacity-40 group-hover:opacity-100'} transition-opacity">
                <button type="button" class="ahs-edit-btn inline-flex items-center justify-center w-6 h-6 rounded-md ${isNew ? 'text-blue-700' : 'text-blue-600'} hover:bg-blue-50 transition-colors" title="${isNew ? 'Selesai' : 'Edit'}">
                    ${isNew 
                        ? `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
                        : `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>`
                    }
                </button>
                <button type="button" class="ahs-del-btn inline-flex items-center justify-center w-6 h-6 rounded-md text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5">
            <input type="text" value="${escHtml(rowData.merk || '')}" placeholder="Merk"
                class="ahs-merk w-full bg-transparent border-b border-transparent hover:border-slate-200 focus:border-primary text-[11px] md:text-[13px] text-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}" ${isNew ? '' : 'readonly'}/>
        </td>
        <td class="px-3 md:px-4 py-2 md:py-2.5">
            <input type="text" value="${escHtml(rowData.spesifikasi || '')}" placeholder="Spesifikasi"
                class="ahs-spesifikasi w-full bg-transparent border-b border-transparent hover:border-slate-200 focus:border-primary text-[11px] md:text-[13px] text-table-subtle focus:outline-none transition-colors py-0.5"
                data-id="${rowData.id}" ${isNew ? '' : 'readonly'}/>
        </td>
    `;

    const header = _ensureHeader(rowData.tipe);
    const footer = _ensureFooter(rowData.tipe);

    // Insert before footer
    footer.before(tr);

    _bindRowInputs(tr);
    renumberRows();
    if (isNew) setTimeout(() => tr.querySelector('.ahs-uraian')?.focus(), 50);
}

function _ensureHeader(tipe) {
    let header = tbody.querySelector(`.ahs-category-header[data-tipe="${tipe}"]`);
    if (!header) {
        header = document.createElement('tr');
        header.className = 'ahs-category-header bg-slate-800 border-y border-slate-700';
        header.dataset.tipe = tipe;
        const cfg = tipeConfig[tipe] || { label: tipe.toUpperCase(), prefix: '?' };
        header.innerHTML = `
            <td class="px-3 md:px-4 py-2 text-center text-[11px] font-bold text-slate-300">${cfg.prefix}</td>
            <td colspan="5" class="px-3 md:px-4 py-2 text-[11px] font-bold uppercase tracking-widest text-white">
                ${cfg.label}
            </td>
            <td class="text-center py-2">
                <button type="button" class="btn-tambah-dari-modal inline-flex items-center justify-center w-6 h-6 rounded bg-white text-brand-dark hover:bg-brand-dark/5 shadow-sm border border-brand-dark/20 focus:outline-none transition-transform active:scale-95" data-tipe="${tipe}" title="Tambah ${cfg.label}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </button>
            </td>
            <td colspan="2" class="bg-slate-800 border-l border-slate-700"></td>
        `;

        const btnAdd = header.querySelector('.btn-tambah-dari-modal');
        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                if(window.ahsOpenModalWithFilter) {
                    window.ahsOpenModalWithFilter(tipe);
                } else if(window.ahsOpenModal) {
                    window.ahsOpenModal();
                }
            });
        }


        // Maintain order: Bahan -> Upah -> Alat
        const types = ['bahan', 'upah', 'alat'];
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
        if (!inserted) tbody.appendChild(header);

        // Ensure footer exists too
        _ensureFooter(tipe);
    }
    return header;
}

window.ahsQuickAdd = (tipe) => addRow(tipe);

function _ensureFooter(tipe) {
    let footer = tbody.querySelector(`.ahs-category-footer[data-tipe="${tipe}"]`);
    if (!footer) {
        footer = document.createElement('tr');
        footer.className = 'ahs-category-footer hidden'; // dummy for identification
        footer.dataset.tipe = tipe;

        const header = _ensureHeader(tipe);
        const types = ['bahan', 'upah', 'alat'];
        const myIdx = types.indexOf(tipe);
        let inserted = false;
        for (let i = myIdx + 1; i < types.length; i++) {
            const nextHeader = tbody.querySelector(`.ahs-category-header[data-tipe="${types[i]}"]`);
            if (nextHeader) {
                nextHeader.before(footer);
                inserted = true;
                break;
            }
        }
        if (!inserted) tbody.appendChild(footer);

        const tr1 = document.createElement('tr');
        tr1.className = `ahs-group-f1-${tipe} bg-slate-50/80 border-t border-slate-200`;
        tr1.innerHTML = `<td colspan="5" class="px-4 py-2 text-right text-[10px] font-bold text-slate-500 uppercase">JUMLAH HARGA</td>
                         <td class="px-4 py-2 text-right text-[11px] font-bold tabular-nums text-slate-700 ahs-group-total">Rp 0</td><td colspan="3" class="bg-slate-50/50"></td>`;

        const tr2 = document.createElement('tr');
        tr2.className = `ahs-group-f2-${tipe} bg-slate-50/80`;
        tr2.innerHTML = `<td colspan="5" class="px-4 py-2 text-right text-[10px] font-medium text-slate-400">JASA 10.00 %</td>
                         <td class="px-4 py-2 text-right text-[11px] font-medium tabular-nums text-slate-500 ahs-group-jasa">Rp 0</td><td colspan="3" class="bg-slate-50/50"></td>`;

        const tr3 = document.createElement('tr');
        tr3.className = `ahs-group-f3-${tipe} bg-blue-50/80 border-b border-slate-300`;
        tr3.innerHTML = `<td colspan="5" class="px-4 py-2 text-right text-[10px] font-extrabold text-blue-800 uppercase tracking-wider">TOTAL HARGA</td>
                         <td class="px-4 py-2 text-right text-[12px] font-black tabular-nums text-blue-900 ahs-group-final">Rp 0</td><td colspan="3" class="bg-slate-50/50"></td>`;

        footer.after(tr3);
        footer.after(tr2);
        footer.after(tr1);
    }
    return footer;
}

function _bindRowInputs(tr) {
    const koefInput = tr.querySelector('.ahs-koef');
    const hargaInput = tr.querySelector('.ahs-harga-dasar');
    const jumlahCell = tr.querySelector('.ahs-jumlah-cell');
    const uraianInput = tr.querySelector('.ahs-uraian');
    const acList = tr.querySelector('.ahs-autocomplete');
    const tipe = tr.dataset.tipe;

    function recalcRow() {
        const koef = parseFloat(koefInput?.value) || 0;
        const harga = parseFloat(hargaInput?.value) || 0;
        if (jumlahCell) jumlahCell.textContent = fmt(koef * harga);
        recalcTotals();
    }
    koefInput?.addEventListener('input', recalcRow);
    hargaInput?.addEventListener('input', recalcRow);

    tr.querySelector('.ahs-edit-btn')?.addEventListener('click', function () {
        const inputs = tr.querySelectorAll('input');
        const isEditing = !inputs[0].hasAttribute('readonly');
        const btn = this;

        if (isEditing) {
            // Selesai -> Lock
            inputs.forEach(input => input.setAttribute('readonly', true));
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>`;
            btn.classList.replace('text-blue-700', 'text-blue-600');
            btn.title = "Edit";
            tr.classList.remove('bg-blue-50/50', 'ring-1', 'ring-blue-200');
            toast.show('Perubahan disimpan di tampilan', 'success', 1500);
        } else {
            // Edit -> Unlock
            inputs.forEach(input => input.removeAttribute('readonly'));
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
            btn.classList.replace('text-blue-600', 'text-blue-700');
            btn.title = "Selesai";
            tr.classList.add('bg-blue-50/50', 'ring-1', 'ring-blue-200');
            inputs[0].focus();
        }
    });

    tr.querySelector('.ahs-del-btn')?.addEventListener('click', async function () {
        const rowId = tr.dataset.id;
        const tipe = tr.dataset.tipe;
        const isConfirmed = await confirmAction('Hapus Item AHS?', 'Item ini akan dihapus dari rincian AHS.', 'Ya, Hapus!');
        if (!isConfirmed) return;

        const isPersistent = !isNaN(rowId) && parseInt(rowId) < 1000000000;
        if (isPersistent) {
            try {
                const res = await fetch(`/api/ahs/rincian/item/${rowId}`, { method: 'DELETE' });
                const json = await res.json();
                if (json.status !== 'success') throw new Error(json.message);
            } catch (err) { toast.show('Gagal: ' + err.message, 'error'); return; }
        }

        tr.remove();
        const remainingRows = tbody.querySelectorAll(`.ahs-row[data-tipe="${tipe}"]`);
        if (remainingRows.length === 0) {
            tbody.querySelector(`.ahs-category-header[data-tipe="${tipe}"]`)?.remove();
            tbody.querySelector(`.ahs-category-footer[data-tipe="${tipe}"]`)?.remove();
            tbody.querySelector(`.ahs-group-f1-${tipe}`)?.remove();
            tbody.querySelector(`.ahs-group-f2-${tipe}`)?.remove();
            tbody.querySelector(`.ahs-group-f3-${tipe}`)?.remove();
        }

        renumberRows();
        recalcTotals();
        toast.show('Item berhasil dihapus', 'success', 2000);
    });

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
                <span class="flex-1 text-table-medium truncate text-xs">${escHtml(m.uraian)}</span>
                <span class="text-table-subtle shrink-0 text-[10px]">${escHtml(m.satuan)} · ${fmt(m.hargaSatuan)}</span>
            </li>`).join('');

        acList.querySelectorAll('li').forEach(li => {
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                uraianInput.value = li.dataset.uraian;
                const satuanEl = tr.querySelector('.ahs-satuan');
                if (satuanEl) satuanEl.value = li.dataset.satuan;
                if (hargaInput) hargaInput.value = li.dataset.harga;
                recalcRow();
                _hideAc(acList);
            });
        });
        acList.classList.remove('hidden');
        state.autocompleteActive = acList;
    });
    uraianInput?.addEventListener('blur', () => setTimeout(() => _hideAc(acList), 150));
    uraianInput?.addEventListener('keydown', e => { if (e.key === 'Escape') _hideAc(acList); });
}

function _hideAc(list) {
    if (list) list.classList.add('hidden');
    state.autocompleteActive = null;
}

export function recalcTotals() {
    const t = { bahan: 0, upah: 0, alat: 0 };
    tbody.querySelectorAll('.ahs-row').forEach(tr => {
        const tipe = tr.dataset.tipe;
        const koef = parseFloat(tr.querySelector('.ahs-koef')?.value) || 0;
        const harga = parseFloat(tr.querySelector('.ahs-harga-dasar')?.value) || 0;
        if (t[tipe] !== undefined) t[tipe] += koef * harga;
    });

    let overallTotal = 0;
    ['bahan', 'upah', 'alat'].forEach(tipe => {
        const groupTotal = t[tipe];
        const jasa = groupTotal * 0.10;
        const totalWithJasa = groupTotal + jasa;

        const f1 = tbody.querySelector(`.ahs-group-f1-${tipe} .ahs-group-total`);
        const f2 = tbody.querySelector(`.ahs-group-f2-${tipe} .ahs-group-jasa`);
        const f3 = tbody.querySelector(`.ahs-group-f3-${tipe} .ahs-group-final`);

        if (f1) f1.textContent = fmt(groupTotal);
        if (f2) f2.textContent = fmt(jasa);
        if (f3) f3.textContent = fmt(totalWithJasa);

        overallTotal += totalWithJasa;
    });

    if (totalKeselEl) {
        totalKeselEl.textContent = fmt(overallTotal);
    }
}

export function renumberRows() {
    const types = ['bahan', 'upah', 'alat'];
    let globalIdx = 0;

    types.forEach(tipe => {
        let n = 0;
        const rows = tbody.querySelectorAll(`.ahs-row[data-tipe="${tipe}"]`);
        rows.forEach(tr => {
            globalIdx++;
            n++;
            const rownumEl = tr.querySelector('.ahs-rownum');
            if (rownumEl) rownumEl.textContent = n;

            // Zebra striping implementation (Blue/White theme)
            tr.classList.remove('bg-white', 'bg-blue-50/40');
            if (globalIdx % 2 === 0) {
                tr.classList.add('bg-blue-50/40');
            } else {
                tr.classList.add('bg-white');
            }
        });
    });
    state.rowCounter = globalIdx;
}

export function addRow(tipe) {
    document.getElementById('ahs-empty-row')?.remove();
    renderRow({ id: Date.now(), tipe, uraian: '', merk: '', spesifikasi: '', koefisien: 1, satuan: '', hargaSatuan: 0, sumber: '' }, true);
    recalcTotals();
    const tipeLabel = tipeConfig[tipe]?.label || tipe;
    toast.show(`Baris ${tipeLabel} baru ditambahkan`, 'success', 2000);
}

