/**
 * pekerjaan/components/custom-row.js
 * "Tambah Sendiri" — inline row to add a custom pekerjaan.
 * Now saves directly to the local database (pekerjaan table) via API.
 */

import { state, tbody, customBtn } from '../core/state.js';
import { savePekerjaanKustom }     from '../core/data.js';
import { updateSubmitBar }         from './render.js';
import { toast }                   from '../../shared/ui/toast.js';

export function bindCustomRow() {
    if (!customBtn) return;

    customBtn.addEventListener('click', function () {
        // Only one custom row at a time
        const existing = tbody.querySelector('.tambah-ahs-custom-row');
        if (existing) {
            existing.querySelector('input[data-field="nama"]').focus();
            toast.show('Selesaikan input pekerjaan sebelumnya', 'warning');
            return;
        }

        const customRow = document.createElement('tr');
        customRow.className = 'tambah-ahs-custom-row border-b-2 border-primary/40 bg-gradient-to-r from-primary/5 to-white';
        customRow.innerHTML = `
            <td class="px-3 md:px-5 py-3 text-center">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </span>
            </td>
            <td class="px-3 md:px-5 py-3">
                <div class="flex flex-col gap-0.5">
                    <label class="text-[9px] font-semibold text-primary/70 uppercase tracking-widest">Nama Pekerjaan *</label>
                    <input type="text" data-field="nama" placeholder="Ketik nama pekerjaan…" autocomplete="off"
                        class="custom-input-nama w-full px-2.5 py-1.5 text-xs font-medium border border-table-border rounded-lg focus:outline-none focus:ring-0.5 focus:ring-primary focus:border-primary bg-white text-table-strong"/>
                </div>
            </td>
            <td class="px-3 md:px-5 py-3">
                <div class="flex flex-col gap-0.5">
                    <label class="text-[9px] font-semibold text-table-subtle uppercase tracking-widest">Satuan</label>
                    <input type="text" data-field="satuan" placeholder="m²"
                        class="w-full px-2.5 py-1.5 text-xs border border-table-border rounded-lg text-center focus:outline-none focus:ring-0.5 focus:ring-primary focus:border-primary bg-white text-table-medium"/>
                </div>
            </td>
            <td class="px-3 md:px-5 py-3">
                <div class="flex flex-col gap-0.5">
                    <label class="text-[9px] font-semibold text-table-subtle uppercase tracking-widest">Sumber</label>
                    <input type="text" data-field="sumber" placeholder="Sumber data" value="Proyek Terkini"
                        class="w-full px-2.5 py-1.5 text-xs border border-table-border rounded-lg text-center focus:outline-none focus:ring-0.5 focus:ring-primary focus:border-primary bg-white text-table-medium"/>
                </div>
            </td>
            <td class="px-3 md:px-5 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button class="custom-add-confirm inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary hover:bg-primary/85 active:scale-95 text-white text-[11px] font-semibold shadow-sm transition-all focus:outline-none" title="Tambahkan ke DB">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
                    </button>
                    <button class="custom-add-cancel inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-red-50 border border-table-border hover:border-red-300 text-table-subtle hover:text-red-500 transition-all focus:outline-none" title="Batal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </td>`;

        tbody.insertBefore(customRow, tbody.firstChild);
        customRow.querySelector('input[data-field="nama"]').focus();

        customRow.querySelector('.custom-add-cancel').addEventListener('click', () => customRow.remove());

        customRow.querySelector('.custom-add-confirm').addEventListener('click', async function () {
            const nama   = customRow.querySelector('[data-field="nama"]').value.trim();
            const satuan = customRow.querySelector('[data-field="satuan"]').value.trim() || '';

            if (!nama) {
                customRow.querySelector('[data-field="nama"]').focus();
                toast.show('Nama Pekerjaan wajib diisi', 'error');
                return;
            }

            // Ambil id_kategori_pekerjaan dari sessionStorage (disimpan saat klik + di RAB)
            let idKategori = null;
            try { idKategori = sessionStorage.getItem('rab_tambah_ahs_dbid') || null; } catch (_) {}
            if (idKategori) idKategori = parseInt(idKategori, 10) || null;

            // Disable button selama request
            const confirmBtn = customRow.querySelector('.custom-add-confirm');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Menyimpan…';

            try {
                const result = await savePekerjaanKustom({ nama_pekerjaan: nama, satuan, id_kategori_pekerjaan: idKategori });

                if (result.status === 'success') {
                    const saved = result.data;
                    // Tambahkan ke state.selected agar langsung bisa di-submit ke RAB
                    const tempId = saved.id || ('kustom_' + Date.now());
                    state.selected[tempId] = { id: tempId, nama, satuan, harga: 0, sumber: 'Proyek Terkini' };

                    customRow.remove();
                    updateSubmitBar();
                    toast.show(`Pekerjaan "${nama}" berhasil disimpan ke database`, 'success');

                    // Refresh tabel agar row kustom baru muncul
                    window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { detail: { page: 1 } }));
                } else {
                    throw new Error(result.message || 'Gagal menyimpan');
                }
            } catch (err) {
                console.error(err);
                toast.show('Gagal menyimpan: ' + err.message, 'error');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Simpan`;
            }
        });
    });
}
