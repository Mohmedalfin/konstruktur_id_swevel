/**
 * components/render.js
 * All rendering functions: loading spinner, readonly table, editable empty state,
 * totals, table visibility, editable mode toggle, and bind helpers for readonly mode.
 */

import { state, tbody, wrapper, searchInput, tambahKategoriBtn, totalJumlah, totalPpn, totalFinal } from '../core/state.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { fetchRabData } from '../core/data.js';

export function renderLoading() {
    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Memuat data…
            </td>
        </tr>`;
    updateTotals(0);
}

export function renderReadonly(data) {
    const categories = data.categories || [];
    const grandTotal = categories.reduce(
        (sum, cat) => sum + cat.items.reduce((s, i) => s + Number(i.hargaKeseluruhan), 0), 0
    );

    if (categories.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-10 text-table-subtle text-xs">Tidak ada data pekerjaan.</td></tr>`;
        updateTotals(0);
        return;
    }

    let html = '';

    categories.forEach(cat => {
        const catTotal = cat.items.reduce((s, i) => s + Number(i.hargaKeseluruhan), 0);
        const isOpen = !state.collapsed[cat.id];
        const subClass = isOpen ? '' : 'hidden';
        const chevronRot = isOpen ? '' : 'rotate-180';

        html += `
            <tr class="rab-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors duration-200"
                data-cat="${cat.id}" role="button" tabindex="0">
                <td class="w-12 md:w-14 px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <div class="relative flex items-center justify-center w-5 h-5 mx-auto">
                        <svg class="cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? '' : 'hidden'}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg class="cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? 'hidden' : ''}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </td>
                <td colspan="9" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                    <span class="flex items-center gap-2">
                        <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                        ${cat.name}
                    </span>
                </td>
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-right text-[10px] md:text-xs tabular-nums opacity-80">${fmt(catTotal)}</td>
                <td class="w-20 md:w-24 px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <svg class="cat-chevron w-3.5 h-3.5 md:w-4 md:h-4 mx-auto opacity-60 transition-transform duration-300 ${chevronRot}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </td>
            </tr>`;

        if (cat.items.length === 0) {
            html += `
                <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border">
                    <td colspan="12" class="px-5 py-3 text-center text-table-subtle text-xs italic">
                        Belum ada item pekerjaan.
                    </td>
                </tr>`;
        } else {
            cat.items.forEach(item => {
                html += `
                    <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150">
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.no}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium min-w-[250px] lg:min-w-[350px] whitespace-normal leading-relaxed">${escHtml(item.uraian)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums">${item.volume}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.satuan}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(item.hargaBahan)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(item.hargaAlat)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(item.hargaUpah)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(item.hargaBahan * (item.volume || 1))}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(item.hargaAlat * (item.volume || 1))}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(item.hargaUpah * (item.volume || 1))}</td>
                        <td class="rab-harga-cell-${cat.id}-${item.no} px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong whitespace-nowrap">${fmt(item.hargaKeseluruhan)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                            <div class="hs-dropdown relative inline-flex">
                                <button type="button"
                                    class="hs-dropdown-toggle inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle hover:text-table-body transition-colors focus:outline-none"
                                    title="Opsi">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                                <div class="hs-dropdown-menu hidden z-50 mt-1 w-44 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 end-0" role="menu">
                                    <button type="button"
                                        class="readonly-item-detail flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition-colors"
                                        data-url="${(window.RAB_INIT && window.RAB_INIT.rincianAhsUrl) || '/menu-rap/rincian-ahs'}">
                                        <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Input Rincian AHS
                                    </button>
                                    <div class="border-t border-table-border my-1"></div>
                                    <button type="button"
                                        class="readonly-item-delete flex w-full items-center gap-2.5 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition-colors"
                                        data-id-rap-detail="${item.id_rap_detail}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>`;
            });
        }
    });

    tbody.innerHTML = html;
    updateTotals(grandTotal);
    bindCategoryToggle();
    bindReadonlyDropdowns();
    try { window.HSStaticMethods?.autoInit(['dropdown']); } catch (_) { }
}

export function renderEditable() {
    tbody.innerHTML = `<tr id="rab-tbody-empty">
        <td colspan="12" class="text-center py-14 text-table-subtle text-xs">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Belum ada kategori pekerjaan. Klik <strong>+ Kategori Pekerjaan</strong> untuk memulai.
        </td>
    </tr>`;
    updateTotals(0);
}

export function updateTotals(total) {
    const ppn = total * 0.11;
    const grand = total + ppn;
    if (totalJumlah) totalJumlah.textContent = fmt(total);
    if (totalPpn) totalPpn.textContent = fmt(ppn);
    if (totalFinal) totalFinal.textContent = fmt(grand);
}

export function showTable() {
    wrapper.classList.remove('hidden');
    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    }
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
}

export function setEditableMode(on) {
    if (tambahKategoriBtn) {
        tambahKategoriBtn.classList.remove('hidden');
    }
}

export function bindCategoryToggle() {
    tbody.querySelectorAll('.rab-category[data-cat]').forEach(row => {
        row.addEventListener('click', function () {
            const catId = row.dataset.cat;
            const subRows = tbody.querySelectorAll(`.subrow-${catId}`);
            const minus = row.querySelector('.cat-icon-minus');
            const plus = row.querySelector('.cat-icon-plus');
            const chevron = row.querySelector('.cat-chevron');
            const isHidden = subRows.length && subRows[0].classList.contains('hidden');

            subRows.forEach(r => r.classList.toggle('hidden', !isHidden));
            if (minus) minus.classList.toggle('hidden', !isHidden);
            if (plus) plus.classList.toggle('hidden', isHidden);
            if (chevron) chevron.classList.toggle('rotate-180', !isHidden);
            state.collapsed[catId] = !isHidden;
        });

        row.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                row.click();
            }
        });
    });
}

export function bindReadonlyDropdowns() {
    tbody.querySelectorAll('.readonly-item-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            window.location.href = btn.dataset.url || '/menu-rap/rincian-ahs';
        });
    });

    tbody.querySelectorAll('.readonly-item-delete').forEach(btn => {
        btn.addEventListener('click', async function () {
            const idRapDetail = btn.dataset.idRapDetail || btn.getAttribute('data-id-rap-detail');
            if (!idRapDetail) return;

            const ok = confirm('Yakin ingin menghapus pekerjaan ini?');
            if (!ok) return;

            try {
                const res = await fetch(`/api/rap/pekerjaan/${idRapDetail}`, {
                    method: 'DELETE',
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menghapus');
                }

                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                renderLoading();
                renderReadonly(await fetchRabData(idProject));
            } catch (err) {
                alert(err.message || 'Terjadi kesalahan saat menghapus');
            }
        });
    });
}