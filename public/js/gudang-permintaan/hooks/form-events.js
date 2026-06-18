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
    reverseButtons: true,
    scrollbarPadding: false,
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

            // Get stok_aktual from option dataset
            const selectedOption = select.options[select.selectedIndex];
            const stokAktual = selectedOption ? selectedOption.dataset.stok : 0;

            block.items.push({
                id_rap_detail_item: item.id_rap_detail_item,
                nama_barang: item.nama,
                jumlah: 1,
                satuan: item.satuan,
                kategori: item.kategori,
                sisa_volume: item.sisa_volume || 0,
                spesifikasi: item.spesifikasi || '-',
                merk: item.merk || '-',
                keterangan: '',
                stok_aktual: stokAktual
            });

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
            const sisaVolume = parseFloat(block.items[idx].sisa_volume) || 0;
            const container = input.closest('div');
            if (container) {
                const errorSpan = container.querySelector('.error-qty');
                if (val > Math.max(0, sisaVolume)) {
                    input.classList.add('border-red-500', 'focus:border-red-500', 'bg-red-50');
                    input.classList.remove('border-slate-300', 'focus:border-blue-500', 'bg-slate-50');
                    if (errorSpan) {
                        const excess = val - Math.max(0, sisaVolume);
                        errorSpan.textContent = `Over-limit: ${excess}`;
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
            }
            
            // Check global over-limit status
            let isAnyOverLimit = false;
            state.projectRows.forEach(r => {
                r.items.forEach(i => {
                    const sv = parseFloat(i.sisa_volume) || 0;
                    if (parseFloat(i.jumlah) > Math.max(0, sv)) isAnyOverLimit = true;
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
                    if (item.jumlah > Math.max(0, sisaVolume)) {
                        isAnyOverLimit = true;
                    }

                    itemsPayload.push({
                        id_project: row.selectedProjectId,
                        id_rap_detail_item: item.id_rap_detail_item,
                        nama_barang: item.nama_barang,
                        jumlah: item.jumlah,
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
