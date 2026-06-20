/**
 * tambah-ahs/components/custom-row.js
 * "Tambah Sendiri" — inline editable row to add a custom (non-database) item.
 */

import { state, tbody, customBtn } from '../core/state.js';
import { updateSubmitBar } from './render.js';
import { toast } from '../../shared/ui/toast.js';

export function bindCustomRow() {
    if (!customBtn) return;

    customBtn.addEventListener('click', function () {
        // Only allow one custom row at a time
        const existing = tbody.querySelector('.tambah-ahs-custom-row');
        if (existing) {
            existing.querySelector('input[data-field="nama"]').focus();
            toast.show('Selesaikan input pekerjaan sebelumnya', 'warning');
            return;
        }

        const customRow = document.createElement('tr');
        customRow.className = 'tambah-ahs-custom-row border-b-2 border-primary/40 bg-gradient-to-r from-primary/5 to-white';
        
        // Custom ComboBox Options
        const satuanOptions = [
            {v: 'm', l: 'Meter (m)'}, {v: 'm2', l: 'Meter Persegi (m²)'}, {v: 'm3', l: 'Meter Kubik (m³)'},
            {v: 'cm', l: 'Sentimeter (cm)'}, {v: 'mm', l: 'Milimeter (mm)'}, {v: 'km', l: 'Kilometer (km)'},
            {v: 'kg', l: 'Kilogram (kg)'}, {v: 'ton', l: 'Ton'}, {v: 'gr', l: 'Gram (gr)'},
            {v: 'bh', l: 'Buah (bh)'}, {v: 'unit', l: 'Unit'}, {v: 'set', l: 'Set'},
            {v: 'ls', l: 'Lump Sum (ls)'}, {v: 'ttk', l: 'Titik (ttk)'}, {v: 'btg', l: 'Batang (btg)'},
            {v: 'lbr', l: 'Lembar (lbr)'}, {v: 'mtr', l: "Meter Lari (m')"}, {v: 'org/hr', l: 'Orang/Hari (OH)'},
            {v: 'jam', l: 'Jam'}, {v: 'hari', l: 'Hari'}, {v: 'bln', l: 'Bulan'}, {v: 'mgg', l: 'Minggu'},
            {v: 'zak', l: 'Zak'}, {v: 'gln', l: 'Galon (gln)'}, {v: 'klg', l: 'Kaleng (klg)'},
            {v: 'btl', l: 'Botol (btl)'}, {v: 'ktk', l: 'Kotak (ktk)'}, {v: 'rol', l: 'Rol'},
            {v: 'dus', l: 'Dus'}, {v: 'rit', l: 'Ritase (rit)'}, {v: 'pax', l: 'Pax'}, {v: 'liter', l: 'Liter (L)'}
        ];
        
        // ... (HTML content unchanged)
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
                <div class="flex flex-col gap-0.5 w-[5rem] relative mx-auto items-center">
                    <label class="text-[9px] font-semibold text-table-subtle uppercase tracking-widest text-center w-full">Satuan</label>
                    <input type="text" data-field="satuan" placeholder="m²" value="m2" autocomplete="off"
                        class="w-full px-2 py-1.5 text-xs border border-table-border rounded-lg text-center focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white text-table-medium truncate"/>
                    <div class="custom-satuan-dropdown absolute top-full left-0 mt-1 w-32 bg-white border border-table-border rounded-md shadow-lg z-[60] hidden max-h-40 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-table-border [&::-webkit-scrollbar-thumb]:rounded-full">
                        <ul class="py-1 text-xs text-table-medium"></ul>
                    </div>
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
                    <button class="custom-add-confirm inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary hover:bg-primary/85 active:scale-95 text-white text-[11px] font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/40" title="Tambahkan ke pilihan">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tambah
                    </button>
                    <button class="custom-add-cancel inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-red-50 border border-table-border hover:border-red-300 text-table-subtle hover:text-red-500 transition-all focus:outline-none" title="Batal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </td>`;

        tbody.insertBefore(customRow, tbody.firstChild);
        
        // Bind Custom ComboBox Events
        const satuanInput = customRow.querySelector('input[data-field="satuan"]');
        const satuanDropdown = customRow.querySelector('.custom-satuan-dropdown');
        const satuanUl = satuanDropdown.querySelector('ul');
        
        const renderSatuan = (filter = '') => {
            satuanUl.innerHTML = '';
            const filtered = satuanOptions.filter(o => o.v.toLowerCase().includes(filter.toLowerCase()) || o.l.toLowerCase().includes(filter.toLowerCase()));
            if (filtered.length === 0) {
                satuanUl.innerHTML = '<li class="px-3 py-1.5 text-table-subtle italic">Tekan enter untuk manual</li>';
            } else {
                filtered.forEach(o => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-1.5 hover:bg-primary/10 hover:text-primary cursor-pointer truncate';
                    li.textContent = o.v;
                    li.title = o.l;
                    li.addEventListener('mousedown', (e) => {
                        e.preventDefault(); // prevent blur
                        satuanInput.value = o.v;
                        satuanDropdown.classList.add('hidden');
                    });
                    satuanUl.appendChild(li);
                });
            }
        };

        satuanInput.addEventListener('focus', () => {
            renderSatuan(satuanInput.value);
            satuanDropdown.classList.remove('hidden');
        });

        satuanInput.addEventListener('input', (e) => {
            renderSatuan(e.target.value);
            satuanDropdown.classList.remove('hidden');
        });

        satuanInput.addEventListener('blur', () => {
            setTimeout(() => satuanDropdown.classList.add('hidden'), 150);
        });
        
        customRow.querySelector('input[data-field="nama"]').focus();

        customRow.querySelector('.custom-add-cancel').addEventListener('click', () => customRow.remove());

        customRow.querySelector('.custom-add-confirm').addEventListener('click', async function () {
            const btn = this;
            const nama   = customRow.querySelector('[data-field="nama"]').value.trim();
            const satuan = customRow.querySelector('[data-field="satuan"]').value.trim() || 'm²';
            const sumber = customRow.querySelector('[data-field="sumber"]').value.trim() || 'Manual';

            if (!nama) {
                customRow.querySelector('[data-field="nama"]').focus();
                toast.show('Nama Pekerjaan wajib diisi', 'error');
                return;
            }

            // AJAX Save to Database
            btn.disabled = true;
            btn.innerHTML = '<span class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>';

            try {
                const response = await fetch('/api/pekerjaan/custom', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nama,
                        satuan,
                        id_project: window.RAB_INIT?.idProject || null
                    })
                });

                const result = await response.json();

                if (!response.ok || result.status !== 'success') {
                    throw new Error(result.message || 'Gagal menyimpan ke database');
                }

                const newId = result.data.id;

                // Add to selection state
                state.selected[newId] = { 
                    id: newId, 
                    nama, 
                    satuan, 
                    harga: 0, 
                    sumber: 'Proyek Terkini' 
                };

                customRow.remove();
                updateSubmitBar();
                toast.show('Pekerjaan berhasil disimpan dan ditambahkan ke pilihan', 'success');

                // Trigger reload table so it appears in the master list
                window.dispatchEvent(new CustomEvent('tambahAhsPageChange', { 
                    detail: { page: 1 } 
                }));

            } catch (err) {
                console.error(err);
                toast.show(err.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Tambah';
            }
        });
    });
}
