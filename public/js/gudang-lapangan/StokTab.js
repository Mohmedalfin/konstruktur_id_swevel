/**
 * StokTab.js – Handles the Stok Lapangan tab
 */
import { formatNum, stokStatusBadge } from './ui.js';

export class StokTab {
    constructor(cfg, returModal, kartuBarangModal, showToast) {
        this.cfg            = cfg;
        this.returModal     = returModal;
        this.kartuBarangModal = kartuBarangModal;
        this.showToast      = showToast;
        this.allData        = [];
        this.loaded         = false;

        this._bindFilters();
    }

    _bindFilters() {
        const searchEl   = document.getElementById('stok-search');
        const kategoriEl = document.getElementById('stok-filter-kategori');

        searchEl?.addEventListener('input',   () => this._render());
        kategoriEl?.addEventListener('change', () => this._render());
    }

    async load() {
        this._setLoading(true);
        try {
            const res = await fetch(`${this.cfg.apiBase}/stok?id_project=${this.cfg.idProject}`);
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal memuat stok');

            this.allData = json.data || [];
            this.loaded  = true;
            this._updateStats();
            this._render();
        } catch (err) {
            this.showToast('error', err.message);
            this._setLoading(false);
        }
    }

    _updateStats() {
        const total    = this.allData.length;
        const cukup    = this.allData.filter(r => parseFloat(r.stok_aktual) >= 5).length;
        const menipis  = this.allData.filter(r => parseFloat(r.stok_aktual) > 0 && parseFloat(r.stok_aktual) < 5).length;
        const habis    = this.allData.filter(r => parseFloat(r.stok_aktual) <= 0).length;

        document.getElementById('stat-total-jenis').textContent = total;
        document.getElementById('stat-stok-cukup').textContent  = cukup;
        document.getElementById('stat-stok-menipis').textContent = menipis;
        document.getElementById('stat-stok-habis').textContent  = habis;
    }

    _getFiltered() {
        const search   = (document.getElementById('stok-search')?.value || '').toLowerCase().trim();
        const kategori = document.getElementById('stok-filter-kategori')?.value || '';

        return this.allData.filter(r => {
            const matchSearch = !search
                || (r.nama_barang || '').toLowerCase().includes(search)
                || (r.kode_barang || '').toLowerCase().includes(search)
                || (r.merk || '').toLowerCase().includes(search);
            const matchKat = !kategori || (r.jenis_item || '') === kategori;
            return matchSearch && matchKat;
        });
    }

    _render() {
        const tbody = document.getElementById('stok-table-body');
        const emptyEl = document.getElementById('stok-empty-state');
        const filtered = this._getFiltered();

        if (!filtered.length) {
            tbody.innerHTML = '';
            emptyEl.classList.remove('hidden');
            return;
        }
        emptyEl.classList.add('hidden');

        tbody.innerHTML = filtered.map(r => {
            const stok = parseFloat(r.stok_aktual) || 0;
            const satuan = r.satuan || '';
            return `
            <tr class="hover:bg-slate-50/60 transition-colors group" data-id="${r.id_barang}">
                <td class="px-5 py-4">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">${r.kode_barang || '–'}</span>
                </td>
                <td class="px-5 py-4">
                    <div class="font-bold text-[12px] text-slate-800 leading-tight mb-0.5">${r.nama_barang || '–'}</div>
                    ${r.spesifikasi && r.spesifikasi !== '-' ? `<div class="text-[10px] font-medium text-slate-400 truncate max-w-[200px]">${r.spesifikasi}</div>` : ''}
                </td>
                <td class="px-5 py-4 hidden md:table-cell">
                    <span class="text-[11px] font-semibold text-slate-500">${r.jenis_item || '–'}</span>
                </td>
                <td class="px-5 py-4 hidden lg:table-cell">
                    <span class="text-[11px] font-medium text-slate-500">${r.merk && r.merk !== '-' ? r.merk : '–'}</span>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="font-bold text-[13px] ${stok <= 0 ? 'text-red-500' : stok < 5 ? 'text-amber-500' : 'text-slate-800'}">
                        ${formatNum(stok)}
                    </div>
                    <div class="text-[10px] font-medium text-slate-400 mt-0.5">${satuan}</div>
                </td>
                <td class="px-5 py-4 text-center hidden sm:table-cell">
                    ${stokStatusBadge(stok)}
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                            class="btn-lihat-kartu inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-100 transition-colors focus:outline-none text-[10px]"
                            data-id="${r.id_barang}" data-nama="${r.nama_barang}" title="Lihat Kartu Stok">
                            <i class="fas fa-scroll"></i>
                        </button>
                        <button
                            class="btn-retur inline-flex items-center justify-center w-7 h-7 rounded-lg bg-amber-50 text-amber-500 hover:bg-amber-100 transition-colors focus:outline-none text-[10px] ${stok <= 0 ? 'opacity-30 cursor-not-allowed' : ''}"
                            data-id="${r.id_barang}" data-nama="${r.nama_barang}" data-stok="${stok}" data-satuan="${satuan}"
                            title="Retur ke Central" ${stok <= 0 ? 'disabled' : ''}>
                            <i class="fas fa-right-left"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        // Bind row action buttons
        tbody.querySelectorAll('.btn-retur').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;
                this.returModal.open({
                    id_barang: btn.dataset.id,
                    nama: btn.dataset.nama,
                    stok: btn.dataset.stok,
                    satuan: btn.dataset.satuan,
                    onSuccess: () => this.load(),
                });
            });
        });

        tbody.querySelectorAll('.btn-lihat-kartu').forEach(btn => {
            btn.addEventListener('click', () => {
                this.kartuBarangModal.open({
                    id_barang: btn.dataset.id,
                    nama: btn.dataset.nama,
                });
            });
        });
    }

    _setLoading(on) {
        const tbody = document.getElementById('stok-table-body');
        if (on) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-5 py-16 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                            <i class="fas fa-circle-notch fa-spin text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 font-medium">Memuat data stok lapangan…</p>
                    </div>
                </td>
            </tr>`;
        }
    }
}
