/**
 * ReturModal.js – Modal untuk retur material ke gudang central
 */
export class ReturModal {
    constructor(cfg, showToast) {
        this.cfg       = cfg;
        this.showToast = showToast;
        this._state    = { id_barang: null, onSuccess: null };

        this._bindEvents();
    }

    _bindEvents() {
        const modal = document.getElementById('modal-retur');

        // Close buttons
        document.getElementById('btn-close-retur')?.addEventListener('click', () => this.close());
        document.getElementById('btn-cancel-retur')?.addEventListener('click', () => this.close());
        document.getElementById('modal-retur-backdrop')?.addEventListener('click', () => this.close());

        // Form submit
        document.getElementById('form-retur')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this._submit();
        });
    }

    /**
     * Open the modal with item data
     * @param {{ id_barang, nama, stok, satuan, onSuccess }} opts
     */
    open({ id_barang, nama, stok, satuan, onSuccess }) {
        this._state = { id_barang, onSuccess };

        // Populate UI
        document.getElementById('retur-id-barang').value = id_barang;
        document.getElementById('retur-nama-barang').textContent = nama;
        document.getElementById('retur-stok-tersedia').textContent = `${parseFloat(stok).toLocaleString('id-ID')} ${satuan}`;
        document.getElementById('retur-satuan').textContent = satuan;

        const jumlahEl = document.getElementById('retur-jumlah');
        jumlahEl.max   = stok;
        jumlahEl.value = '';

        document.getElementById('retur-keterangan').value = '';

        // Show
        document.getElementById('modal-retur').classList.remove('hidden');
        setTimeout(() => jumlahEl.focus(), 100);
    }

    close() {
        document.getElementById('modal-retur').classList.add('hidden');
        this._state = { id_barang: null, onSuccess: null };
    }

    async _submit() {
        const jumlah    = parseFloat(document.getElementById('retur-jumlah').value || 0);
        const keterangan = document.getElementById('retur-keterangan').value.trim();
        const id_barang  = parseInt(this._state.id_barang);

        if (!jumlah || jumlah <= 0) {
            this.showToast('warning', 'Jumlah retur harus lebih dari 0');
            return;
        }

        const btn = document.getElementById('btn-submit-retur');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i> Memproses…';

        try {
            const res = await fetch(`${this.cfg.apiBase}/retur`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    id_project: this.cfg.idProject,
                    id_barang,
                    jumlah,
                    keterangan: keterangan || 'Retur material sisa ke gudang',
                }),
            });
            const json = await res.json();

            if (!res.ok || json.status !== 'success') {
                throw new Error(json.message || 'Gagal memproses retur');
            }

            this.showToast('success', json.message || 'Retur berhasil diproses');
            this.close();
            this._state.onSuccess?.();

        } catch (err) {
            this.showToast('error', err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-right-left text-[10px]"></i> Proses Retur';
        }
    }
}
