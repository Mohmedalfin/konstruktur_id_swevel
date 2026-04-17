/**
 * ahs/hooks/save.js
 * Save handler — collects row data and sends to CI4 endpoint.
 */

import { state, tbody, simpanBtn } from '../core/state.js';
import { saveRincianAHS } from '../core/data.js';
import { toast } from '../../shared/ui/toast.js';

export function bindSave() {
    if (!simpanBtn) return;
    simpanBtn.addEventListener('click', async function () {
        const urlParams = new URLSearchParams(window.location.search);
        const idDetail  = urlParams.get('id_rap_detail');
        
        if (!idDetail) {
            toast.show('ID Detail tidak ditemukan di URL', 'error');
            return;
        }

        const items = [];
        tbody.querySelectorAll('.ahs-row').forEach(tr => {
            const rowData = {
                tipe:        tr.dataset.tipe,
                uraian:      (tr.querySelector('.ahs-uraian')?.value      || '').trim(),
                merk:        (tr.querySelector('.ahs-merk')?.value        || '').trim(),
                spesifikasi: (tr.querySelector('.ahs-spesifikasi')?.value || '').trim(),
                koefisien:   parseFloat(tr.querySelector('.ahs-koef')?.value) || 0,
                satuan:      (tr.querySelector('.ahs-satuan')?.value      || '').trim(),
                hargaSatuan: parseFloat(tr.querySelector('.ahs-harga-dasar')?.value)  || 
                             parseFloat(tr.querySelector('.ahs-harga-satuan')?.value) || 0,
                sumber:      (tr.querySelector('.ahs-sumber')?.value      || '').trim(),
            };
            
            if (rowData.uraian) {
                items.push(rowData);
            }
        });

        if (items.length === 0) {
            toast.show('Tambahkan minimal satu item sebelum menyimpan', 'warning');
            return;
        }

        // ── Block UI ─────────────────────────────────────────────────────
        const originalText = simpanBtn.textContent;
        simpanBtn.disabled  = true;
        simpanBtn.innerHTML = '<span class="animate-spin mr-2">↻</span> Menyimpan...';

        try {
            const res = await saveRincianAHS({ id_rap_detail: idDetail, items });
            if (res.status === 'success') {
                toast.show('Rincian AHS berhasil disimpan ke database', 'success', 4000);
            } else {
                throw new Error(res.message || 'Gagal menyimpan');
            }
        } catch (err) {
            toast.show('Gagal menyimpan: ' + err.message, 'error', 5000);
        } finally {
            simpanBtn.disabled  = false;
            simpanBtn.textContent = originalText;
        }
    });
}
