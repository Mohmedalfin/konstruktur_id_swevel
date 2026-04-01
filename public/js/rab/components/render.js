/**
 * components/render.js
 * All rendering functions: loading spinner, readonly table, editable empty state,
 * totals, table visibility, editable mode toggle, and bind helpers for readonly mode.
 */

import {
    state,
    tbody,
    wrapper,
    searchInput,
    tambahKategoriBtn,
    totalJumlah,
    totalPpn,
    totalFinal
} from '../core/state.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { fetchRabData } from '../core/data.js';
import { confirmAction } from '../../shared/ui/confirm.js';

export function renderLoading() {
    tbody.innerHTML = `
        <tr>
            <td colspan="12" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Memuat data...
            </td>
        </tr>
    `;
    updateTotals(0);
}

export function renderReadonly(data) {
    const categories = data?.categories || [];
    const isEditable = (data?.sumber_data || 'manual') === 'manual';

    const grandTotal = categories.reduce((sum, cat) => {
        const items = cat.items || [];
        return sum + items.reduce((s, i) => s + Number(i.hargaKeseluruhan || 0), 0);
    }, 0);

    if (categories.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-10 text-table-subtle text-xs">
                    Tidak ada data pekerjaan.
                </td>
            </tr>
        `;
        updateTotals(0);
        return;
    }

    let html = '';

    categories.forEach(cat => {
        const items = cat.items || [];
        const catTotal = items.reduce((s, i) => s + Number(i.hargaKeseluruhan || 0), 0);
        const isOpen = !state.collapsed[cat.id];
        const subClass = isOpen ? '' : 'hidden';

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
                        ${escHtml(cat.name || 'Tanpa Kategori')}
                    </span>
                </td>

                <td class="px-3 md:px-5 py-2.5 md:py-3 text-right text-[10px] md:text-xs tabular-nums opacity-80">
                    ${fmt(catTotal)}
                </td>

                <td class="px-2 md:px-3 py-2.5 md:py-3 text-center">
                    ${isEditable ? `
                        <div class="inline-flex items-center gap-1">
                            <button
                                type="button"
                                class="add-subitem-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}"
                                data-catname="${escHtml(cat.name || '')}"
                                title="Tambah Pekerjaan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="delete-category-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-red-500/30 text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}"
                                data-catname="${escHtml(cat.name || '')}"
                                title="Hapus Kategori">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    ` : ''}
                </td>
            </tr>
        `;

        if (items.length === 0) {
            html += `
                <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border">
                    <td colspan="12" class="px-5 py-3 text-center text-table-subtle text-xs italic">
                        Belum ada item pekerjaan.
                    </td>
                </tr>
            `;
        } else {
            items.forEach(item => {
                const volume = Number(item.volume || 0);
                const hargaBahan = Number(item.hargaBahan || 0);
                const hargaAlat = Number(item.hargaAlat || 0);
                const hargaUpah = Number(item.hargaUpah || 0);
                const hargaKeseluruhan = Number(item.hargaKeseluruhan || 0);

                html += `
                    <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150">
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${item.no ?? '-'}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium min-w-[250px] lg:min-w-[350px] whitespace-normal leading-relaxed">
                            ${escHtml(item.uraian || '-')}
                        </td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums">${volume}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${escHtml(item.satuan || '')}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaBahan)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaAlat)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaUpah)}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaBahan * (volume || 1))}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaAlat * (volume || 1))}</td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaUpah * (volume || 1))}</td>
                        <td class="rab-harga-cell-${cat.id}-${item.no} px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong whitespace-nowrap">
                            ${fmt(hargaKeseluruhan)}
                        </td>
                        <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                            <div class="inline-flex items-center gap-2">
                                ${isEditable ? `
                                    <button
                                        type="button"
                                        class="readonly-item-detail inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle hover:text-table-body transition-colors focus:outline-none"
                                        data-url="${(window.RAB_INIT && window.RAB_INIT.rincianAhsUrl) || '/menu-rap/rincian-ahs'}"
                                        data-id-rap-detail="${item.id_rap_detail || ''}"
                                        title="Input Rincian AHS">
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        class="readonly-item-delete inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white hover:bg-red-50 border border-table-border text-red-500 transition-colors focus:outline-none"
                                        data-id-rap-detail="${item.id_rap_detail || ''}"
                                        title="Hapus pekerjaan">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
    });

    tbody.innerHTML = html;
    updateTotals(grandTotal);
    bindCategoryToggle();
    bindReadonlyDropdowns();
    bindCategoryActionButtons();
    bindDeleteCategoryButtons();

    try {
        window.HSStaticMethods?.autoInit(['dropdown']);
    } catch (_) { }
}

export function renderEditable() {
    tbody.innerHTML = `
        <tr id="rab-tbody-empty">
            <td colspan="12" class="text-center py-14 text-table-subtle text-xs">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Belum ada kategori pekerjaan. Klik <strong>+ Kategori Pekerjaan</strong> untuk memulai.
            </td>
        </tr>
    `;
    updateTotals(0);
}

export function bindCategoryActionButtons() {
    tbody.querySelectorAll('.add-subitem-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            const idKategori = Number(btn.dataset.cat || 0);
            const namaKategori = btn.dataset.catname || 'kategori';
            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
            const slug = window.RAB_INIT?.slug || new URLSearchParams(window.location.search).get('slug');

            if (!idKategori) {
                alert('Kategori tidak ditemukan.');
                return;
            }

            if (!slug && !idProject) {
                alert('Project tidak ditemukan.');
                return;
            }

            const params = new URLSearchParams();
            if (slug) params.set('slug', slug);
            if (idProject) params.set('id', idProject);
            params.set('kategori_id', idKategori);
            params.set('kategori_nama', namaKategori);

            window.location.href = `/menu-rap/tambah-pekerjaan?${params.toString()}`;
        });
    });
}

export function bindDeleteCategoryButtons() {
    tbody.querySelectorAll('.delete-category-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.stopPropagation();

            const catId = btn.dataset.cat || '';
            const catName = btn.dataset.catname || 'kategori';

            if (!catId) return;

            const ok = await confirmAction(
                'Hapus Kategori?',
                `Yakin ingin menghapus kategori <strong>"${catName}"</strong>? Semua pekerjaan di kategori ini juga akan ikut terhapus.`,
                'Ya, Hapus'
            );
            if (!ok) return;

            try {
                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (!idProject) {
                    throw new Error('ID project tidak ditemukan');
                }

                const res = await fetch(`/api/rap/kategori/${encodeURIComponent(catId)}?id_project=${encodeURIComponent(idProject)}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();

                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menghapus kategori');
                }

                renderLoading();
                const data = await fetchRabData(idProject);
                
                state.activeCategories = (data.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));

                renderReadonly(data);
                
                // Show toast specifically for UI completeness
                if (window.Toast) {
                    window.Toast.show(`Kategori "${catName}" berhasi dihapus dari project`, 'success');
                } else if (typeof toast !== 'undefined' && toast.show) {
                    toast.show(`Kategori "${catName}" berhasi dihapus dari project`, 'success');
                }
            } catch (err) {
                console.error('Gagal hapus kategori:', err);
                if (window.Toast) {
                    window.Toast.show(err.message || 'Terjadi kesalahan saat menghapus kategori', 'error');
                } else {
                    alert(err.message || 'Terjadi kesalahan saat menghapus kategori');
                }
            }
        });
    });
}

export function updateTotals(total) {
    const safeTotal = Number(total || 0);
    const ppn = safeTotal * 0.11;
    const grand = safeTotal + ppn;

    if (totalJumlah) totalJumlah.textContent = fmt(safeTotal);
    if (totalPpn) totalPpn.textContent = fmt(ppn);
    if (totalFinal) totalFinal.textContent = fmt(grand);
}

export function showTable() {
    wrapper.classList.remove('hidden');

    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    }

    setTimeout(() => {
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 80);
}

export function setEditableMode(on) {
    if (!tambahKategoriBtn) return;

    if (on) {
        tambahKategoriBtn.classList.remove('hidden');
    } else {
        tambahKategoriBtn.classList.add('hidden');
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
            const isHidden = subRows.length > 0 && subRows[0].classList.contains('hidden');

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
            const baseUrl = btn.dataset.url || '/menu-rap/rincian-ahs';
            const idRapDetail = btn.dataset.idRapDetail || '';

            if (!idRapDetail) {
                window.location.href = baseUrl;
                return;
            }

            const separator = baseUrl.includes('?') ? '&' : '?';
            window.location.href = `${baseUrl}${separator}id_rap_detail=${encodeURIComponent(idRapDetail)}`;
        });
    });

    tbody.querySelectorAll('.readonly-item-delete').forEach(btn => {
        btn.addEventListener('click', async function () {
            const idRapDetail = btn.dataset.idRapDetail || btn.getAttribute('data-id-rap-detail');
            if (!idRapDetail) return;

            const ok = await confirmAction(
                'Hapus Pekerjaan?',
                'Yakin ingin menghapus pekerjaan ini dari RAB?',
                'Ya, Hapus'
            );
            if (!ok) return;

            try {
                const res = await fetch(`/api/rap/pekerjaan/${idRapDetail}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();

                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menghapus');
                }

                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (!idProject) {
                    throw new Error('ID project tidak ditemukan');
                }

                renderLoading();
                const data = await fetchRabData(idProject);

                state.activeCategories = (data.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));

                renderReadonly(data);

                if (window.Toast) {
                    window.Toast.show('Pekerjaan berhasil dihapus dari RAB', 'success');
                }
            } catch (err) {
                console.error('Gagal hapus pekerjaan:', err);
                if (window.Toast) {
                    window.Toast.show(err.message || 'Terjadi kesalahan saat menghapus', 'error');
                } else {
                    alert(err.message || 'Terjadi kesalahan saat menghapus');
                }
            }
        });
    });
}