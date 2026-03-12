import { state, submitBtn } from '../core/state.js';
import { toast } from '../../shared/ui/toast.js';

export function bindSubmit() {
    if (!submitBtn) return;

    submitBtn.addEventListener('click', async function () {
        const items = Object.values(state.selected);
        if (items.length === 0) return;

        let catId = '';
        try {
            catId = sessionStorage.getItem('rab_tambah_ahs_cat') || '';
        } catch (_) { }

        const idProject = sessionStorage.getItem('current_id_project') || new URLSearchParams(window.location.search).get('id');

        if (!idProject || !catId) {
            alert('Kategori atau project tidak ditemukan.');
            return;
        }

        const payloadItems = items.map(item => ({
            nama: item.nama,
            volume: 1,
            satuan: item.satuan || '',
            harga_bahan: 0,
            harga_alat: 0,
            harga_upah: 0,
            keterangan: item.sumber || 'manual',
        }));

        submitBtn.textContent = 'Menambahkan...';
        submitBtn.disabled = true;

        try {
            const res = await fetch('/api/rap/pekerjaan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_project: Number(idProject),
                    id_kategori: Number(catId),
                    pekerjaan: payloadItems,
                }),
            });

            const json = await res.json();

            if (!res.ok || json.status !== 'success') {
                throw new Error(json.message || 'Gagal menyimpan pekerjaan');
            }

            let rabUrl = '';
            try {
                rabUrl = sessionStorage.getItem('rab_return_url') || '';
            } catch (_) { }

            window.location.href = rabUrl || `/menu-rap?id=${idProject}`;
        } catch (err) {
            alert(err.message || 'Terjadi kesalahan saat menyimpan pekerjaan');
            submitBtn.textContent = 'Tambah ke RAB';
            submitBtn.disabled = false;
        }
    });
}