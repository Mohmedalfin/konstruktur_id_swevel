/**
 * ahs/hooks/save.js
 * Save handler — collects row data and sends to CI4 endpoint.
 */

import { state, tbody, simpanBtn } from '../core/state.js';

export function bindSave() {
    if (!simpanBtn) return;
    simpanBtn.addEventListener('click', function () {
        const payload = [];
        tbody.querySelectorAll('.ahs-row').forEach(tr => {
            payload.push({
                tipe:        tr.dataset.tipe,
                uraian:      tr.querySelector('.ahs-uraian')?.value      || '',
                merk:        tr.querySelector('.ahs-merk')?.value        || '',
                spesifikasi: tr.querySelector('.ahs-spesifikasi')?.value || '',
                koefisien:   parseFloat(tr.querySelector('.ahs-koef')?.value) || 0,
                satuan:      tr.querySelector('.ahs-satuan')?.value      || '',
                hargaSatuan: parseFloat(tr.querySelector('.ahs-harga-satuan')?.value) || 0,
                sumber:      tr.querySelector('.ahs-sumber')?.value      || '',
            });
        });
        console.info('[AHS Simpan]', payload);
        alert('Data AHS berhasil dikumpulkan (' + payload.length + ' baris).\nEndpoint CI4 segera diimplementasi.');
    });
}
