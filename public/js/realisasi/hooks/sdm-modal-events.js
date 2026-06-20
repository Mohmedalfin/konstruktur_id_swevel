import { getState, updateState } from '../core/state.js';
import { toast }                from '../../shared/ui/toast.js';

const SEL_BATCH_TBODY   = '#batch-sdm-progress-tbody';
const SEL_BTN_SAVE      = '#btn-save-realisasi-sdm';
const SEL_FOTO_INPUT    = '#upload-foto-sdm-input';
const SEL_FOTO_PREVIEW  = '#foto-preview-sdm-container';
const SEL_FOTO_EMPTY    = '#foto-empty-state-sdm';
const CATEGORIES        = ['bahan', 'alat', 'upah'];

let uploadedFiles = [];

export function initSDMModalEvents() {
    _bindDropdownChange();
    _bindAddButton();
    _bindSaveButton();
    _bindFileUpload();
    _bindModalOpen();
}

function _populateSDMDropdown() {
    const { sdmResources } = getState();
    
    CATEGORIES.forEach(catId => {
        const container = document.getElementById(`container-select-${catId}`);
        if (!container) return;
        
        // Filter resources for this category
        const filteredResources = sdmResources.filter(r => r.kategori === catId);
        _buildCustomDropdown(container, catId, filteredResources);
    });
}

function _buildCustomDropdown(container, catId, items) {
    container.innerHTML = '';

    const hiddenSelect = document.createElement('select');
    hiddenSelect.id = `real-${catId}-select`;
    hiddenSelect.className = 'hidden';
    container.appendChild(hiddenSelect);

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.id = `custom-${catId}-select-trigger`;
    trigger.className = 'relative w-full py-3 px-4 flex items-center justify-between bg-white border border-gray-300 rounded-lg text-start text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-primary font-medium text-slate-500 transition-all';
    trigger.innerHTML = `<span id="custom-${catId}-select-label">Pilih Item...</span><i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200" id="custom-${catId}-select-chevron"></i>`;
    container.appendChild(trigger);

    const panel = document.createElement('div');
    panel.id = `custom-${catId}-select-panel`;
    panel.className = 'hidden absolute top-full left-0 right-0 mt-1 z-[70] bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden';
    panel.style.maxHeight = '310px';
    panel.innerHTML = `
        <div class="p-2 border-b border-slate-100 sticky top-0 bg-white z-10">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="text" id="custom-${catId}-select-search" placeholder="Cari..." class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-primary bg-slate-50">
            </div>
        </div>
        <div id="custom-${catId}-select-list" class="overflow-y-auto" style="max-height:240px"></div>
    `;
    container.appendChild(panel);

    let selectedItem = null;

    const renderList = (query = '') => {
        const list = panel.querySelector(`#custom-${catId}-select-list`);
        const q = query.toLowerCase().trim();

        const filtered = items.filter(item => {
            if (!q) return true;
            return item.nama_item.toLowerCase().includes(q);
        });

        list.innerHTML = '';

        if (filtered.length === 0) {
            list.innerHTML = `<div class="px-4 py-6 text-center text-sm text-slate-400 italic">Tidak ada data.</div>`;
            return;
        }

        let catColor = catId === 'bahan' ? 'orange' : (catId === 'alat' ? 'blue' : 'red');

        filtered.forEach(item => {
            const row = document.createElement('button');
            row.type = 'button';
            row.className = `w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-${catColor}-50 hover:text-${catColor}-700 transition-colors rounded-md mx-1 mt-1`;
            row.style.width = 'calc(100% - 8px)';
            
            let sisaGudangHtml = '';
            if (catId === 'bahan' || catId === 'alat') {
                sisaGudangHtml = ` <span class="mx-1">•</span> Gudang: <span class="font-bold text-emerald-600">${item.stok_lapangan || 0}</span> ${item.satuan}`;
            }

            row.innerHTML = `
                <div class="font-medium">${item.nama_item}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">RAB: <span class="font-bold ${item.qty_sisa < 0 ? 'text-red-600' : 'text-' + catColor + '-500'}">${item.qty_sisa}</span> ${item.satuan}${sisaGudangHtml}</div>
            `;

            if (selectedItem && selectedItem.id_rap_detail_item === item.id_rap_detail_item) {
                row.classList.add(`bg-${catColor}-50`, `text-${catColor}-700`, 'font-semibold');
            }

            row.addEventListener('click', () => {
                selectedItem = item;

                document.getElementById(`custom-${catId}-select-label`).textContent = item.nama_item;
                document.getElementById(`custom-${catId}-select-label`).style.color = '#1e293b';

                hiddenSelect.innerHTML = `<option value="${item.id_rap_detail_item}" 
                    data-kategori="${item.kategori}" 
                    data-nama="${item.nama_item}"
                    data-satuan="${item.satuan}" 
                    data-sisa="${item.qty_sisa}"
                    data-stok-lapangan="${item.stok_lapangan || 0}"
                    data-spek="${item.spesifikasi || ''}"
                    data-merk="${item.merk || ''}"
                    selected>${item.nama_item}</option>`;
                hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));

                closePanel();
            });

            list.appendChild(row);
        });
    };

    const openPanel = () => {
        panel.classList.remove('hidden');
        document.getElementById(`custom-${catId}-select-chevron`).style.transform = 'rotate(180deg)';
        panel.querySelector(`#custom-${catId}-select-search`).focus();
        renderList();
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        document.getElementById(`custom-${catId}-select-chevron`).style.transform = '';
    };

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.contains('hidden') ? openPanel() : closePanel();
    });

    panel.querySelector(`#custom-${catId}-select-search`).addEventListener('input', (e) => {
        renderList(e.target.value);
    });

    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) closePanel();
    });
}

function _bindDropdownChange() {
    document.addEventListener('change', (e) => {
        const selectId = e.target.id;
        if (!selectId || !selectId.endsWith('-select') || !selectId.startsWith('real-')) return;

        const catId = selectId.split('-')[1]; // bahan, alat, or upah
        const selected = e.target.selectedOptions[0];
        
        const satuanDisplay = document.getElementById(`real-${catId}-satuan-display`);
        const sisaDisplay   = document.getElementById(`real-${catId}-sisa-display`);
        const spekInput     = document.getElementById(`real-${catId}-spek`);
        const merkInput     = document.getElementById(`real-${catId}-merk`);
        
        const satuanInput   = document.getElementById(`real-${catId}-satuan`);
        const sisaInput     = document.getElementById(`real-${catId}-sisa`);

        const spekMobile    = document.getElementById(`real-${catId}-spek-mobile`);
        const merkMobile    = document.getElementById(`real-${catId}-merk-mobile`);

        if (!selected || !selected.value) {
            _resetDisplays(satuanDisplay, sisaDisplay, spekInput, merkInput, satuanInput, sisaInput);
            return;
        }

        const satuan = selected.dataset.satuan || '';
        const sisa   = parseFloat(selected.dataset.sisa) || 0;
        const stokLapangan = parseFloat(selected.dataset.stokLapangan) || 0;
        const spek   = selected.dataset.spek || '-';
        const merk   = selected.dataset.merk || '-';
        
        let catColor = catId === 'bahan' ? 'orange' : (catId === 'alat' ? 'blue' : 'red');

        if (satuanDisplay) satuanDisplay.textContent = satuan || '-';
        if (sisaDisplay) {
            if (catId === 'bahan' || catId === 'alat') {
                sisaDisplay.innerHTML = `
                    <div class="flex flex-col gap-1 mt-1 sm:mt-0">
                        <span class="text-slate-400 font-semibold text-[10px] mb-[-2px] uppercase">RAB: <span class="text-${catColor}-500 font-bold text-sm sm:text-[15px] ml-1">${sisa}</span></span>
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Gudang: <span class="text-emerald-600 font-bold text-sm sm:text-[15px] ml-1">${stokLapangan}</span></span>
                    </div>
                `;
            } else {
                sisaDisplay.textContent = sisa;
            }
        }
        
        if (spekInput) spekInput.value = spek;
        if (merkInput) merkInput.value = merk;

        if (spekMobile) spekMobile.textContent = spek;
        if (merkMobile) merkMobile.textContent = merk;

        if (satuanInput) satuanInput.value = satuan;
        if (sisaInput) sisaInput.value = sisa;
    });
}

function _bindAddButton() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[id^="btn-add-"]');
        if (!btn || btn.id === 'btn-add-sdm-progress') return;

        const catId = btn.id.split('-')[2]; 
        if (!CATEGORIES.includes(catId)) return;

        const select        = document.getElementById(`real-${catId}-select`);
        const qtyActualEl   = document.getElementById(`real-${catId}-qty-actual`);
        const keteranganEl  = document.getElementById(`real-${catId}-keterangan`);

        if (!select || !qtyActualEl) return;

        const selectedOption = select.selectedOptions[0];
        const qtyActual      = parseFloat(qtyActualEl.value);

        if (!selectedOption || !selectedOption.value) {
            _showFieldError(select.closest('.relative').querySelector('button'), 'Pilih item terlebih dahulu.');
            return;
        }

        if (isNaN(qtyActual) || qtyActual <= 0) {
            _showFieldError(qtyActualEl, 'Qty (Jumlah) harus lebih dari 0.');
            return;
        }

        const sisa = parseFloat(selectedOption.dataset.sisa) || 0;
        if (qtyActual > sisa) {
            toast.show(`Perhatian: Qty (${qtyActual}) melebihi sisa stok (${sisa} ${selectedOption.dataset.satuan}). Tercatat sebagai over-budget.`, 'warning');
        }

        const item = {
            _batchId        : Date.now(),
            id_rap_detail_item : parseInt(selectedOption.value),
            kategori        : selectedOption.dataset.kategori,
            nama_item       : selectedOption.dataset.nama,
            satuan          : selectedOption.dataset.satuan || '',
            spesifikasi     : selectedOption.dataset.spek || '-',
            merk            : selectedOption.dataset.merk || '-',
            qty             : qtyActual,
            keterangan      : keteranganEl?.value.trim() || '',
        };

        const { batchSdmItems } = getState();
        updateState({ batchSdmItems: [...batchSdmItems, item] });

        _renderBatchTable();
        _resetInputRow(catId);
    });
}

function _renderBatchTable() {
    const tbody = document.querySelector(SEL_BATCH_TBODY);
    if (!tbody) return;

    const { batchSdmItems } = getState();

    if (batchSdmItems.length === 0) {
        tbody.innerHTML = `
            <tr id="batch-sdm-empty-row">
                <td colspan="7" class="px-4 py-10 text-center text-slate-400 italic text-sm">
                    Belum ada item resource yang ditambahkan ke daftar simpan.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = batchSdmItems.map((item, index) => {
        let badgeClass = 'bg-slate-100 text-slate-600';
        if (item.kategori === 'bahan') badgeClass = 'bg-yellow-100 text-yellow-700';
        else if (item.kategori === 'alat') badgeClass = 'bg-blue-100 text-blue-700';
        else if (item.kategori === 'upah') badgeClass = 'bg-red-100 text-red-700';

        return `
            <tr class="bg-slate-50 text-sm" data-batch-id="${item._batchId}">
                <td class="px-4 py-3 text-center text-slate-600 font-medium">${index + 1}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded ${badgeClass}">${item.kategori}</span>
                </td>
                <td class="px-4 py-3 font-medium text-slate-700">${item.nama_item}</td>
                <td class="px-4 py-3 text-center text-slate-600">${item.satuan}</td>
                <td class="px-4 py-3 text-center font-bold text-orange-500">${item.qty}</td>
                <td class="px-4 py-3 text-slate-500 truncate max-w-[200px]" title="${item.keterangan}">${item.keterangan || '-'}</td>
                <td class="px-4 py-3 text-center">
                    <button
                        type="button"
                        class="btn-remove-batch-sdm w-6 h-6 inline-flex items-center justify-center rounded bg-red-50 hover:bg-red-500 text-red-400 hover:text-white transition-colors"
                        data-batch-id="${item._batchId}"
                        title="Hapus">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    tbody.querySelectorAll('.btn-remove-batch-sdm').forEach(btn => {
        btn.addEventListener('click', () => {
            const batchId   = parseInt(btn.dataset.batchId);
            const { batchSdmItems } = getState();
            updateState({ batchSdmItems: batchSdmItems.filter(i => i._batchId !== batchId) });
            _renderBatchTable();
        });
    });
}

function _bindSaveButton() {
    document.addEventListener('click', async (e) => {
        if (!e.target.closest(SEL_BTN_SAVE)) return;

        const tanggalEl = document.getElementById('real-sdm-tanggal');
        const { batchSdmItems } = getState();

        if (!tanggalEl?.value) {
            toast.show('Pilih tanggal pelaksanaan terlebih dahulu.', 'warning');
            return;
        }

        if (batchSdmItems.length === 0) {
            toast.show('Tambahkan minimal satu item resource terlebih dahulu.', 'warning');
            return;
        }

        const saveBtn = e.target.closest(SEL_BTN_SAVE);
        _setButtonLoading(saveBtn, true);

        try {
            const formData = new FormData();
            formData.append('tanggal', tanggalEl.value);
            formData.append('items', JSON.stringify(
                batchSdmItems.map(({ id_rap_detail_item, kategori, nama_item, satuan, spesifikasi, merk, qty, keterangan }) => ({
                    id_rap_detail_item,
                    kategori,
                    nama_item,
                    satuan,
                    spesifikasi,
                    merk,
                    qty,
                    keterangan,
                }))
            ));

            uploadedFiles.forEach((fileObj, i) => {
                formData.append(`foto[${i}]`, fileObj.file);
            });

            const slug = window.REALISASI_INIT?.slug || '';
            const response = await fetch(`/realisasi/${slug}/store-sdm`, {
                method : 'POST',
                body   : formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Gagal menyimpan data realisasi SDM.');
            }

            toast.show('Realisasi SDM berhasil disimpan!', 'success');
            _resetModal();
            setTimeout(() => {
                window.location.reload();
            }, 1000);

        } catch (err) {
            console.error('[RealisasiModalSDM] Save error:', err);
            toast.show(`Terjadi kesalahan: ${err.message}`, 'error');
        } finally {
            _setButtonLoading(saveBtn, false);
        }
    });
}

function _bindFileUpload() {
    const fileInput = document.querySelector(SEL_FOTO_INPUT);
    if (!fileInput) return;

    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                toast.show(`File "${file.name}" terlalu besar. Maksimal 5MB.`, 'warning');
                return;
            }
            uploadedFiles.push({
                id  : Date.now() + Math.random(),
                file,
                url : URL.createObjectURL(file),
            });
        });

        _renderFilePreviews();
        fileInput.value = '';
    });

    const previewContainer = document.querySelector(SEL_FOTO_PREVIEW);
    if (previewContainer) {
        previewContainer.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-remove-foto-sdm');
            if (!btn) return;

            const id = parseFloat(btn.dataset.id);
            const target = uploadedFiles.find(f => f.id === id);
            if (target) URL.revokeObjectURL(target.url);

            uploadedFiles = uploadedFiles.filter(f => f.id !== id);
            _renderFilePreviews();
        });
    }
}

function _renderFilePreviews() {
    const container = document.querySelector(SEL_FOTO_PREVIEW);
    const emptyState = document.querySelector(SEL_FOTO_EMPTY);
    if (!container) return;

    if (uploadedFiles.length === 0) {
        container.innerHTML = '';
        emptyState?.classList.remove('hidden');
        return;
    }

    emptyState?.classList.add('hidden');
    container.innerHTML = uploadedFiles.map(f => `
        <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 rounded overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200">
                    <img src="${f.url}" alt="Preview" class="w-full h-full object-cover">
                </div>
                <span class="text-sm font-semibold text-[#1e293b] truncate" title="${f.file.name}">${f.file.name}</span>
            </div>
            <button type="button" class="btn-remove-foto-sdm flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" data-id="${f.id}">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>
    `).join('');
}

function _bindModalOpen() {
    const openBtn = document.querySelector('[data-hs-overlay="#modal-real-sdm"]');
    if (!openBtn) return;

    openBtn.addEventListener('click', () => {
        _populateSDMDropdown();
        
        const tanggalEl = document.getElementById('real-sdm-tanggal');
        if (tanggalEl && !tanggalEl.value) {
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            tanggalEl.value = today;
        }
    });
}

function _resetDisplays(satuanDisplay, sisaDisplay, spekInput, merkInput, satuanInput, sisaInput) {
    if (satuanDisplay) satuanDisplay.textContent = '-';
    if (sisaDisplay) sisaDisplay.textContent = '0';
    
    if (spekInput) spekInput.value = '';
    if (merkInput) merkInput.value = '';

    if (satuanInput) satuanInput.value = '';
    if (sisaInput) sisaInput.value = '';

    // Clear mobile displays if any
    ['bahan','alat','upah'].forEach(cat => {
        const sm = document.getElementById(`real-${cat}-spek-mobile`);
        const mm = document.getElementById(`real-${cat}-merk-mobile`);
        if (sm) sm.textContent = '-';
        if (mm) mm.textContent = '-';
    });
}

function _resetInputRow(catId) {
    const labelEl = document.getElementById(`custom-${catId}-select-label`);
    if (labelEl) {
        labelEl.textContent = 'Pilih Item...';
        labelEl.style.color = '';
    }
    const selectEl = document.getElementById(`real-${catId}-select`);
    if (selectEl) selectEl.innerHTML = '';
    
    const qtyActualEl = document.getElementById(`real-${catId}-qty-actual`);
    if (qtyActualEl) qtyActualEl.value = '';
    
    const keteranganEl = document.getElementById(`real-${catId}-keterangan`);
    if (keteranganEl) keteranganEl.value = '';

    const satuanDisplay = document.getElementById(`real-${catId}-satuan-display`);
    const sisaDisplay   = document.getElementById(`real-${catId}-sisa-display`);
    const spekInput     = document.getElementById(`real-${catId}-spek`);
    const merkInput     = document.getElementById(`real-${catId}-merk`);
    
    const satuanInput   = document.getElementById(`real-${catId}-satuan`);
    const sisaInput     = document.getElementById(`real-${catId}-sisa`);

    _resetDisplays(satuanDisplay, sisaDisplay, spekInput, merkInput, satuanInput, sisaInput);
}

function _resetModal() {
    uploadedFiles = [];
    updateState({ batchSdmItems: [] });
    _renderBatchTable();
    _renderFilePreviews();

    const tanggalEl = document.getElementById('real-sdm-tanggal');
    if (tanggalEl) tanggalEl.value = '';

    CATEGORIES.forEach(catId => _resetInputRow(catId));
}

function _setButtonLoading(btn, isLoading) {
    if (!btn) return;
    btn.disabled  = isLoading;
    btn.innerHTML = isLoading
        ? '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...'
        : '<i class="fas fa-save"></i> Simpan Realisasi SDM';
}

function _showFieldError(element, message) {
    element.classList.add('border-red-400', 'ring-1', 'ring-red-300');
    element.focus();
    toast.show(message, 'warning'); 
    element.classList.remove('border-red-400', 'ring-1', 'ring-red-300');
}
