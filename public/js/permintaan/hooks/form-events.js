import { getState, updateState, resetFormState } from '../core/state.js';
import { fetchProjectRapItems, saveRequest } from '../core/data.js';
import { renderFormProjectBlocks } from '../components/render.js';

// SweetAlert2 helper
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';
const AppSwal = Swal.mixin({
    customClass: {
        popup: 'app-swal-popup',
        title: 'app-swal-title',
        htmlContainer: 'app-swal-html',
        confirmButton: 'app-swal-confirm',
        cancelButton: 'app-swal-cancel',
        icon: 'app-swal-icon',
    },
    buttonsStyling: false,
    reverseButtons: true
});

export function initForm() {
    // 1. Reset state and render initial form block
    resetFormState();
    renderForm();

    // 2. Add Project Row Block
    const btnAddProject = document.getElementById('btn-add-project');
    if (btnAddProject) {
        btnAddProject.addEventListener('click', () => {
            const state = getState();
            state.projectRows.push({
                id: Date.now() + Math.random(),
                selectedProjectId: '',
                rapItems: [],
                items: []
            });
            renderForm();
        });
    }

    // 3. Project Dropdown Changes
    document.addEventListener('change', async (e) => {
        const select = e.target.closest('.select-project');
        if (!select) return;

        const blockId = parseFloat(select.dataset.blockId);
        const projectId = select.value;
        const state = getState();
        const block = state.projectRows.find(b => b.id === blockId);

        if (block) {
            block.selectedProjectId = projectId;
            block.items = [];
            block.rapItems = [];

            if (projectId) {
                block.rapItems = await fetchProjectRapItems(projectId);
            }
            renderForm();
        }
    });

    // Custom Dropdown Handlers
    document.addEventListener('click', (e) => {
        // Close all open panels if clicked outside
        if (!e.target.closest('.custom-select-wrapper')) {
            document.querySelectorAll('.custom-select-panel:not(.hidden)').forEach(p => {
                p.classList.add('hidden');
                const icon = p.parentElement.querySelector('.custom-select-icon');
                if (icon) icon.classList.remove('rotate-180');
            });
        }

        // Toggle Panel
        const trigger = e.target.closest('.custom-select-trigger');
        if (trigger) {
            const wrapper = trigger.closest('.custom-select-wrapper');
            const panel = wrapper.querySelector('.custom-select-panel');
            const icon = trigger.querySelector('.custom-select-icon');
            const searchInput = wrapper.querySelector('.custom-select-search');
            
            const isHidden = panel.classList.contains('hidden');
            
            // Close others
            document.querySelectorAll('.custom-select-panel:not(.hidden)').forEach(p => {
                if (p !== panel) {
                    p.classList.add('hidden');
                    const otherIcon = p.parentElement.querySelector('.custom-select-icon');
                    if (otherIcon) otherIcon.classList.remove('rotate-180');
                }
            });

            if (isHidden) {
                panel.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
                if (searchInput) setTimeout(() => searchInput.focus(), 100);
            } else {
                panel.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        }

        // Select Item
        const item = e.target.closest('.custom-select-item');
        if (item) {
            const wrapper = item.closest('.custom-select-wrapper');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const display = wrapper.querySelector('.custom-select-display');
            const panel = wrapper.querySelector('.custom-select-panel');
            const icon = wrapper.querySelector('.custom-select-icon');

            const value = item.dataset.value;
            const name = item.dataset.name;

            hiddenInput.value = value;
            display.innerText = name;

            // Update active styles
            wrapper.querySelectorAll('.custom-select-item').forEach(i => {
                i.classList.remove('font-bold', 'bg-blue-50', 'text-blue-600');
                i.classList.add('text-slate-700', 'hover:bg-slate-50');
            });
            item.classList.remove('text-slate-700', 'hover:bg-slate-50');
            item.classList.add('font-bold', 'bg-blue-50', 'text-blue-600');

            panel.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');

            // Dispatch change event on the hidden input to trigger existing logic
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });

    // Custom Dropdown Search
    document.addEventListener('input', (e) => {
        const searchInput = e.target.closest('.custom-select-search');
        if (searchInput) {
            const term = searchInput.value.toLowerCase();
            const wrapper = searchInput.closest('.custom-select-wrapper');
            const items = wrapper.querySelectorAll('.custom-select-item');
            
            items.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                if (name.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    });

    // 4. Remove Project Block
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove-project-block');
        if (!btn) return;

        const blockId = parseFloat(btn.dataset.blockId);
        const state = getState();
        state.projectRows = state.projectRows.filter(b => b.id !== blockId);
        renderForm();
    });

    // 5. Add Item Button Click
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-add-rap-item');
        if (!btn) return;

        const blockId = parseFloat(btn.dataset.blockId);
        const state = getState();
        const block = state.projectRows.find(b => b.id === blockId);
        if (!block) return;

        // Find the select dropdown
        const blockElement = document.querySelector(`[data-row-id="${blockId}"]`);
        if (!blockElement) return;
        
        const select = blockElement.querySelector('.select-rap-item');
        const idRap = select.value ? select.value : null;
        if (!idRap) {
            AppSwal.fire({
                icon: 'warning',
                title: 'Pilih Item',
                text: 'Silakan pilih item dari dropdown terlebih dahulu.'
            });
            return;
        }

        const item = block.rapItems.find(i => String(i.id_rap_detail_item) === String(idRap));
        if (item) {
            // Check if item already added
            const exists = block.items.some(i => String(i.id_rap_detail_item) === String(idRap));
            if (exists) {
                AppSwal.fire({
                    icon: 'warning',
                    title: 'Item sudah ada',
                    text: `Barang "${item.nama}" sudah ditambahkan sebelumnya.`
                });
                select.value = '';
                return;
            }

            block.items.push({
                id_rap_detail_item: item.id_rap_detail_item,
                nama_barang: item.nama,
                jumlah: 0,
                satuan: item.satuan,
                kategori: item.kategori,
                sisa_volume: item.sisa_volume || 0,
                spesifikasi: item.spesifikasi || '-',
                merk: item.merk || '-',
                keterangan: '',
                satuan_kemasan: item.satuan_kemasan,
                konversi_faktor: item.konversi_faktor
            });

            // Reset the custom select UI instead of the hidden select value
            const wrapper = select.closest('.custom-select-wrapper');
            if (wrapper) {
                const display = wrapper.querySelector('.custom-select-display');
                if (display) display.innerText = '-- Pilih Item --';
                
                wrapper.querySelectorAll('.custom-select-item').forEach(i => {
                    if (i.dataset.value === '') {
                        i.classList.add('font-bold', 'bg-blue-50', 'text-blue-600');
                        i.classList.remove('text-slate-700', 'hover:bg-slate-50');
                    } else {
                        i.classList.remove('font-bold', 'bg-blue-50', 'text-blue-600');
                        i.classList.add('text-slate-700', 'hover:bg-slate-50');
                    }
                });
            }
            select.value = '';
            renderForm();
        }
    });

    // 7. Remove Item from list
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove-item');
        if (!btn) return;

        const blockId = parseFloat(btn.dataset.blockId);
        const idx = parseInt(btn.dataset.idx);

        const state = getState();
        const block = state.projectRows.find(b => b.id === blockId);

        if (block) {
            block.items.splice(idx, 1);
            renderForm();
        }
    });

    // 8. Sync Input Changes to local state
    // Quantity Input Change
    document.addEventListener('input', (e) => {
        const input = e.target.closest('.input-qty');
        if (!input) return;

        const blockId = parseFloat(input.dataset.blockId);
        const idx = parseInt(input.dataset.idx);
        const val = parseFloat(input.value) || 0;

        const state = getState();
        const block = state.projectRows.find(b => b.id === blockId);
        if (block && block.items[idx]) {
            block.items[idx].jumlah = val;
            
            // Validate locally and update the DOM immediately
            const sisaVolumeBase = parseFloat(block.items[idx].sisa_volume) || 0;
            const itemObj = block.items[idx];
            const kf = parseFloat(itemObj.konversi_faktor) || 1;
            const deductedQty = val * kf;
            
            const container = input.closest('div');
            if (container) {
                const errorSpan = container.querySelector('.error-qty');
                const isOverLimit = deductedQty > Math.max(0, sisaVolumeBase);

                if (isOverLimit) {
                    input.classList.add('border-red-500', 'focus:border-red-500', 'bg-red-50');
                    input.classList.remove('border-slate-300', 'focus:border-blue-500', 'bg-slate-50');
                    if (errorSpan) {
                        const excess = deductedQty - Math.max(0, sisaVolumeBase);
                        errorSpan.textContent = `Over: ${excess} ${itemObj.satuan}`;
                        errorSpan.classList.remove('hidden');
                        errorSpan.classList.add('text-red-500');
                    }
                } else {
                    input.classList.remove('border-red-500', 'focus:border-red-500', 'bg-red-50');
                    input.classList.add('border-slate-300', 'focus:border-blue-500', 'bg-slate-50');
                    if (errorSpan) {
                        errorSpan.textContent = '';
                        errorSpan.classList.add('hidden');
                        errorSpan.classList.remove('text-red-500');
                    }
                }

                // Update conversion info container
                const conversionContainer = container.querySelector('.conversion-info-container');
                if (conversionContainer) {
                    const hasKemasan = itemObj.satuan_kemasan && String(itemObj.satuan_kemasan).trim() !== '';
                    const isDifferentName = hasKemasan && String(itemObj.satuan_kemasan).toLowerCase() !== String(itemObj.satuan).toLowerCase();
                    const displaySatuan = (hasKemasan && (kf > 1 || isDifferentName)) ? itemObj.satuan_kemasan : itemObj.satuan;
                    
                    let conversionHtml = '';
                    if (hasKemasan && (kf > 1 || isDifferentName)) {
                        if (deductedQty > 0) {
                            conversionHtml = `<div class="mt-1 sm:mt-0 sm:ml-2 text-[9px] text-indigo-700 bg-indigo-50 px-2 py-1 rounded border border-indigo-200 whitespace-nowrap leading-tight text-left">
                                Memotong RAP: <b>${deductedQty} ${itemObj.satuan}</b>
                            </div>`;
                        } else {
                            conversionHtml = `<div class="mt-1 sm:mt-0 sm:ml-2 text-[9px] text-slate-500 bg-slate-50 px-2 py-1 rounded border border-slate-200 whitespace-nowrap leading-tight text-left">
                                <i class="fas fa-info-circle mr-0.5"></i> 1 ${displaySatuan} = ${kf} ${itemObj.satuan}
                            </div>`;
                        }
                    }
                    conversionContainer.innerHTML = conversionHtml;
                }
            }
            
            // Check global over-limit status
            let isAnyOverLimit = false;
            state.projectRows.forEach(r => {
                r.items.forEach(i => {
                    const sv = parseFloat(i.sisa_volume) || 0;
                    const itemKf = parseFloat(i.konversi_faktor) || 1;
                    const deducted = (parseFloat(i.jumlah) || 0) * itemKf;
                    if (deducted > Math.max(0, sv)) isAnyOverLimit = true;
                });
            });
            
            const overLimitContainer = document.getElementById('over-limit-container');
            const justifikasiInput = document.getElementById('justifikasi_over_limit');
            if (overLimitContainer && justifikasiInput) {
                if (isAnyOverLimit) {
                    overLimitContainer.classList.remove('hidden');
                    justifikasiInput.setAttribute('required', 'required');
                } else {
                    overLimitContainer.classList.add('hidden');
                    justifikasiInput.removeAttribute('required');
                }
            }
        }
    });

    // Satuan Input Change (Only enabled for custom items)
    document.addEventListener('input', (e) => {
        const input = e.target.closest('.input-satuan');
        if (!input) return;

        const blockId = parseFloat(input.dataset.blockId);
        const idx = parseInt(input.dataset.idx);
        const val = input.value.trim();

        const state = getState();
        const block = state.projectRows.find(b => b.id === blockId);
        if (block && block.items[idx]) {
            block.items[idx].satuan = val;
        }
    });

    // Item Note/Keterangan Change
    document.addEventListener('input', (e) => {
        const input = e.target.closest('.input-item-keterangan');
        if (!input) return;

        const blockId = parseFloat(input.dataset.blockId);
        const idx = parseInt(input.dataset.idx);
        const val = input.value;

        const state = getState();
        const block = state.projectRows.find(b => b.id === blockId);
        if (block && block.items[idx]) {
            block.items[idx].keterangan = val;
        }
    });



    // 9. Handle Form Submission
    const form = document.getElementById('permintaan-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const state = getState();
            const globalNotes = document.getElementById('catatan_umum')?.value || '';
            const justifikasiInput = document.getElementById('justifikasi_over_limit');
            const justifikasiOverLimit = justifikasiInput ? justifikasiInput.value : '';

            // Validation checks
            if (state.projectRows.length === 0) {
                AppSwal.fire({
                    icon: 'warning',
                    title: 'Formulir Kosong',
                    text: 'Silakan tambahkan setidaknya satu blok proyek.'
                });
                return;
            }

            const itemsPayload = [];
            let isValid = true;
            let validationError = '';
            let isAnyOverLimit = false;

            state.projectRows.forEach((row, index) => {
                if (!row.selectedProjectId) {
                    isValid = false;
                    validationError = `Proyek ${index + 1} belum dipilih.`;
                    return;
                }

                if (!row.items || row.items.length === 0) {
                    isValid = false;
                    validationError = `Proyek ${index + 1} harus memiliki setidaknya satu item.`;
                    return;
                }

                row.items.forEach(item => {
                    if (!item.nama_barang || !item.nama_barang.trim()) {
                        isValid = false;
                        validationError = `Nama item di Proyek ${index + 1} tidak boleh kosong.`;
                        return;
                    }
                    if (item.jumlah <= 0) {
                        isValid = false;
                        validationError = `Jumlah untuk item "${item.nama_barang}" di Proyek ${index + 1} tidak boleh kosong atau nol.`;
                        return;
                    }
                    if (!item.satuan.trim()) {
                        isValid = false;
                        validationError = `Satuan untuk item "${item.nama_barang}" di Proyek ${index + 1} tidak boleh kosong.`;
                        return;
                    }

                    const sisaVolume = parseFloat(item.sisa_volume) || 0;
                    const kf = parseFloat(item.konversi_faktor) || 1;
                    const baseQty = (parseFloat(item.jumlah) || 0) * kf;
                    
                    if (baseQty > Math.max(0, sisaVolume)) {
                        isAnyOverLimit = true;
                    }

                    itemsPayload.push({
                        id_project: row.selectedProjectId,
                        id_rap_detail_item: item.id_rap_detail_item,
                        nama_barang: item.nama_barang,
                        jumlah: baseQty,
                        satuan: item.satuan,
                        kategori: item.kategori,
                        keterangan: item.keterangan
                    });
                });
            });

            if (!isValid) {
                AppSwal.fire({
                    icon: 'warning',
                    title: 'Validasi Gagal',
                    text: validationError
                });
                return;
            }
            
            if (isAnyOverLimit && !justifikasiOverLimit.trim()) {
                AppSwal.fire({
                    icon: 'warning',
                    title: 'Justifikasi Diperlukan',
                    text: 'Terdapat item yang melebihi sisa volume RAP. Anda wajib mengisi justifikasi over-limit.'
                });
                const overLimitContainer = document.getElementById('over-limit-container');
                if (overLimitContainer) overLimitContainer.classList.remove('hidden');
                if (justifikasiInput) {
                    justifikasiInput.setAttribute('required', 'required');
                    justifikasiInput.focus();
                }
                return;
            }

            const payload = {
                keterangan: globalNotes,
                justifikasi_over_limit: justifikasiOverLimit,
                items: itemsPayload
            };

            try {
                // Show loading
                if (window.showLoader) window.showLoader();

                const res = await saveRequest(payload);
                if (res.status === 'success') {
                    if (window.hideLoader) window.hideLoader();

                    await AppSwal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Permintaan item ke gudang berhasil dikirim!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    window.location.href = '/permintaan';
                } else {
                    if (window.hideLoader) window.hideLoader();
                    throw new Error(res.message || 'Gagal menyimpan permintaan');
                }
            } catch (error) {
                if (window.hideLoader) window.hideLoader();
                AppSwal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Terjadi kesalahan internal.'
                });
            }
        });
    }
}

function renderForm() {
    const state = getState();
    const projects = (window.PERMINTAAN_INIT && window.PERMINTAAN_INIT.projects) || (window.BUAT_PERMINTAAN_INIT && window.BUAT_PERMINTAAN_INIT.projects) || [];
    renderFormProjectBlocks(state.projectRows, projects);
}
