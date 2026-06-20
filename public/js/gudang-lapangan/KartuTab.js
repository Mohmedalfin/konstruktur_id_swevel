/**
 * KartuTab.js – Handles the Kartu Stok tab (all mutations log)
 */
import { formatNum, formatDate, tipeBadge, sumberLabel } from './ui.js';

export class KartuTab {
    constructor(cfg, showToast) {
        this.cfg       = cfg;
        this.showToast = showToast;
        this.allData   = [];
        this.loaded    = false;

        this._bindFilters();
    }
    _bindFilters() {
        const dateStart = document.getElementById('kartu-filter-start');
        const dateEnd = document.getElementById('kartu-filter-end');
        const dateClear = document.getElementById('kartu-filter-clear-date');

        const updateDateClearBtn = () => {
            if (dateStart?.value || dateEnd?.value) {
                dateClear?.classList.remove('hidden');
                dateClear?.classList.add('flex');
            } else {
                dateClear?.classList.add('hidden');
                dateClear?.classList.remove('flex');
            }
            this._render();
        };

        document.getElementById('kartu-filter-tipe')?.addEventListener('change', () => this._render());
        document.getElementById('kartu-filter-sumber')?.addEventListener('change', () => this._render());
        
        dateStart?.addEventListener('change', updateDateClearBtn);
        dateEnd?.addEventListener('change', updateDateClearBtn);
        
        dateClear?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (dateStart) dateStart.value = '';
            if (dateEnd) dateEnd.value = '';
            updateDateClearBtn();
        });
    }

    async load() {
        this._setLoading(true);
        try {
            const res = await fetch(`${this.cfg.apiBase}/kartu?id_project=${this.cfg.idProject}&limit=200`);
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal memuat kartu stok');

            this.allData = json.data || [];
            this.loaded  = true;
            this._render();
        } catch (err) {
            this.showToast('error', err.message);
            this._setLoading(false);
        }
    }

    _getFiltered() {
        const tipe   = document.getElementById('kartu-filter-tipe')?.value || '';
        const sumber = document.getElementById('kartu-filter-sumber')?.value || '';
        const start  = document.getElementById('kartu-filter-start')?.value || '';
        const end    = document.getElementById('kartu-filter-end')?.value || '';

        return this.allData.filter(r => {
            const matchTipe   = !tipe   || r.tipe   === tipe;
            const matchSumber = !sumber || r.sumber === sumber;
            let matchDate = true;
            if (start || end) {
                const rowDate = r.created_at ? String(r.created_at).substring(0, 10) : '';
                if (start && rowDate < start) matchDate = false;
                if (end && rowDate > end) matchDate = false;
            }
            return matchTipe && matchSumber && matchDate;
        });
    }

    _render() {
        const tbody   = document.getElementById('kartu-table-body');
        const emptyEl = document.getElementById('kartu-empty-state');
        const filtered = this._getFiltered();

        if (!filtered.length) {
            tbody.innerHTML = '';
            emptyEl.classList.remove('hidden');
            return;
        }
        emptyEl.classList.add('hidden');

        tbody.innerHTML = filtered.map(r => `
        <tr class="hover:bg-slate-50/60 transition-colors">
            <td class="px-5 py-4 whitespace-nowrap">
                <span class="text-[11px] font-semibold text-slate-500">${formatDate(r.created_at)}</span>
            </td>
            <td class="px-5 py-4">
                <div class="font-bold text-[12px] text-slate-800 leading-tight mb-0.5">${r.nama_barang || '–'}</div>
                <div class="text-[10px] font-medium text-slate-400">${r.kode_barang || ''}</div>
            </td>
            <td class="px-5 py-4 text-center whitespace-nowrap">
                ${tipeBadge(r.tipe)}
            </td>
            <td class="px-5 py-4 text-center">
                <span class="font-bold text-[13px] ${r.tipe === 'masuk' ? 'text-emerald-600' : 'text-red-500'}">
                    ${r.tipe === 'masuk' ? '+' : '-'}${formatNum(r.jumlah)} <span class="text-[10px] text-slate-400 ml-0.5 font-medium">${r.satuan || ''}</span>
                </span>
            </td>
            <td class="px-5 py-4 text-center hidden sm:table-cell">
                <span class="text-[12px] font-bold text-slate-800">${formatNum(r.sisa_stok)} <span class="text-[10px] text-slate-400 ml-0.5 font-medium">${r.satuan || ''}</span></span>
            </td>
            <td class="px-5 py-4 hidden md:table-cell">
                <span class="text-[11px] text-slate-500 font-medium">${sumberLabel(r.sumber)}</span>
            </td>
            <td class="px-5 py-4 hidden lg:table-cell">
                <span class="text-[11px] font-medium text-slate-400 truncate block max-w-[200px]" title="${r.keterangan || ''}">${r.keterangan || '–'}</span>
            </td>
        </tr>`).join('');
    }

    _setLoading(on) {
        const tbody = document.getElementById('kartu-table-body');
        if (on) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-5 py-16 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                            <i class="fas fa-circle-notch fa-spin text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 font-medium">Memuat riwayat mutasi…</p>
                    </div>
                </td>
            </tr>`;
        }
    }
}
