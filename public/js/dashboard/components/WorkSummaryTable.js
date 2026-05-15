import { getState } from '../core/state.js';
import { formatPercent } from '../core/helpers.js';

/**
 * Merender tabel ringkasan pekerjaan berdasarkan kategori
 */
export function renderSummaryTable() {
    const { data } = getState();
    
    if (!data || !data.summary) return;

    const summary = data.summary;
    const tableBody = document.getElementById('table-summary-body');

    if (!tableBody) return;

    tableBody.innerHTML = '';

    if (summary.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-medium italic">
                    Belum ada data pekerjaan tersedia.
                </td>
            </tr>
        `;
        return;
    }

    summary.forEach(item => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50/50 transition-colors group';

        const diff = item.actual_pct - item.planned_pct;

        row.innerHTML = `
            <td class="px-4 py-3.5">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-700 group-hover:text-primary transition-colors">${item.nama_kategori}</span>
                </div>
            </td>
            <td class="px-4 py-3.5 text-center">
                <span class="text-xs font-black text-slate-600">${formatPercent(item.bobot_pct)}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
                <span class="text-xs font-bold text-slate-400">${formatPercent(item.planned_pct)}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs font-black ${diff >= 0 ? 'text-emerald-600' : 'text-slate-700'}">${formatPercent(item.actual_pct)}</span>
                    <div class="w-12 bg-slate-100 h-1 rounded-full overflow-hidden">
                        <div class="h-full ${diff >= 0 ? 'bg-emerald-500' : 'bg-slate-300'}" style="width: ${Math.min(item.actual_pct, 100)}%"></div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3.5 text-center">
                <button data-id="${item.id_kategori}" class="btn-category-detail inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/[0.08] text-emerald-600 hover:bg-emerald-500 hover:text-white text-[10px] font-black transition-all duration-300 border border-emerald-500/20 hover:border-emerald-500 shadow-sm hover:shadow-md hover:shadow-emerald-500/20 active:scale-95 group/btn">
                    <i class="fas fa-eye text-[9px] transition-transform group-hover/btn:scale-110"></i>
                    Detail
                </button>
            </td>
        `;

        tableBody.appendChild(row);
    });
}
