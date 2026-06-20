/**
 * KartuBarangModal.js – Modal untuk melihat kartu stok per barang
 */
import { formatNum, formatDate, tipeBadge, sumberLabel } from './ui.js';

export class KartuBarangModal {
    constructor(cfg, showToast) {
        this.cfg       = cfg;
        this.showToast = showToast;

        this._bindEvents();
    }

    _bindEvents() {
        document.getElementById('btn-close-kartu-barang')?.addEventListener('click', () => this.close());
        document.getElementById('modal-kartu-backdrop')?.addEventListener('click', () => this.close());
    }

    /**
     * Open modal and load data for a specific item
     * @param {{ id_barang, nama }} opts
     */
    async open({ id_barang, nama }) {
        document.getElementById('modal-kartu-title').textContent = nama || 'Kartu Stok';
        document.getElementById('modal-kartu-subtitle').textContent = `Riwayat mutasi material`;
        document.getElementById('kartu-barang-body').innerHTML = `
            <tr><td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                <i class="fas fa-spinner fa-spin mr-2"></i>Memuat…
            </td></tr>`;
        document.getElementById('modal-kartu-barang').classList.remove('hidden');

        try {
            const url = `${this.cfg.apiBase}/kartu?id_project=${this.cfg.idProject}&id_barang=${id_barang}`;
            const res  = await fetch(url);
            const json = await res.json();

            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal memuat kartu');

            const rows = json.data || [];
            this._renderRows(rows, document.getElementById('kartu-barang-body'));
        } catch (err) {
            this.showToast('error', err.message);
            this.close();
        }
    }

    close() {
        document.getElementById('modal-kartu-barang').classList.add('hidden');
    }

    _renderRows(rows, tbody) {
        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-10 text-center text-slate-400 text-xs">Belum ada riwayat mutasi untuk barang ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(r => `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-slate-600">${formatDate(r.created_at)}</td>
            <td class="px-4 py-3 text-center">${tipeBadge(r.tipe)}</td>
            <td class="px-4 py-3 text-right">
                <span class="font-bold text-base ${r.tipe === 'masuk' ? 'text-emerald-600' : 'text-red-500'}">
                    ${r.tipe === 'masuk' ? '+' : '-'}${formatNum(r.jumlah)}
                </span>
            </td>
            <td class="px-4 py-3 text-right font-bold text-base text-slate-700">${formatNum(r.sisa_stok)}</td>
            <td class="px-4 py-3 hidden sm:table-cell">
                <div class="text-xs font-bold text-slate-600">${sumberLabel(r.sumber)}</div>
                ${r.keterangan ? `<div class="text-xs text-slate-500 mt-0.5">${r.keterangan}</div>` : ''}
            </td>
        </tr>`).join('');
    }
}
