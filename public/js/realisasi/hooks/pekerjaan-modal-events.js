import { getState, updateState } from '../core/state.js';
import { flattenLeafItems }     from '../core/helpers.js';
import { toast }                from '../../shared/ui/toast.js';
import { confirmDelete }        from '../../shared/ui/confirm.js';
import { renderTable }          from '../components/render.js';
import { getFilteredData }      from './filter.js';

const SEL_TASK_SELECT   = '#real-task-select';
const SEL_VOL_TARGET    = '#real-vol-target';
const SEL_SATUAN        = '#real-satuan';
const SEL_VOL_ACTUAL    = '#real-vol-actual';
const SEL_KETERANGAN    = '#real-keterangan';
const SEL_BATCH_TBODY   = '#batch-progress-tbody';
const SEL_BTN_ADD       = '#btn-add-progress';
const SEL_BTN_SAVE      = '#btn-save-realisasi';
const SEL_FOTO_INPUT    = '#upload-foto-input';
const SEL_FOTO_PREVIEW  = '#foto-preview-container';
const SEL_FOTO_EMPTY    = '#foto-empty-state';

let uploadedFiles = [];

export function initPekerjaanModalEvents() {
    _bindDropdownChange();
    _bindAddButton();
    _bindSaveButton();
    _bindFileUpload();
    _bindModalOpen();
    _bindDeleteLog();
}

function _populateTaskDropdown() {
    const container = document.querySelector('#real-task-select')?.closest('.relative');
    if (!container) return;

    const { realisasiData } = getState();
    _buildCustomDropdown(container, realisasiData);
}

function _buildCustomDropdown(container, realisasiData) {
    container.innerHTML = '';

    const hiddenSelect = document.createElement('select');
    hiddenSelect.id = 'real-task-select';
    hiddenSelect.className = 'hidden';
    container.appendChild(hiddenSelect);

    const allItems = [];
    realisasiData.forEach(cat => {
        const leafItems = _collectLeafItems(cat.children || []);
        if (leafItems.length === 0) return;
        leafItems.forEach(item => {
            allItems.push({ ...item, categoryName: cat.uraian });
        });
    });

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.id = 'custom-select-trigger';
    trigger.className = 'relative w-full py-3 px-4 flex items-center justify-between bg-white border border-gray-300 rounded-lg text-start text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-primary font-medium text-slate-500 transition-all';
    trigger.innerHTML = `<span id="custom-select-label">Pilih Pekerjaan...</span><i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200" id="custom-select-chevron"></i>`;
    container.appendChild(trigger);

    const panel = document.createElement('div');
    panel.id = 'custom-select-panel';
    panel.className = 'hidden absolute top-full left-0 right-0 mt-1 z-[70] bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden';
    panel.style.maxHeight = '310px';
    panel.innerHTML = `
        <div class="p-2 border-b border-slate-100 sticky top-0 bg-white z-10">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="text" id="custom-select-search" placeholder="Cari pekerjaan..." class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-primary bg-slate-50">
            </div>
        </div>
        <div id="custom-select-list" class="overflow-y-auto" style="max-height:240px"></div>
    `;
    container.appendChild(panel);

    let selectedItem = null;

    const renderList = (query = '') => {
        const list = panel.querySelector('#custom-select-list');
        const q = query.toLowerCase().trim();

        const grouped = {};
        allItems.forEach(item => {
            const label = `${item.no} ${item.uraian}`;
            if (q && !label.toLowerCase().includes(q)) return;
            if (!grouped[item.categoryName]) grouped[item.categoryName] = [];
            grouped[item.categoryName].push(item);
        });

        list.innerHTML = '';

        if (Object.keys(grouped).length === 0) {
            list.innerHTML = `<div class="px-4 py-6 text-center text-sm text-slate-400 italic">Tidak ada pekerjaan ditemukan.</div>`;
            return;
        }

        Object.entries(grouped).forEach(([catName, items]) => {
            const catHeader = document.createElement('div');
            catHeader.className = 'flex items-center gap-2 px-3 py-1.5 mt-1';
            catHeader.style.cssText = 'background: #f1f5f9; border-left: 3px solid #94a3b8; border-radius: 5px; margin: 4px 4px 2px; pointer-events: none;';
            catHeader.innerHTML = `<i class="fas fa-folder-open" style="color:#64748b;font-size:9px;"></i><span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#475569;">${catHeader.textContent}${catName}</span>`;
            catHeader.innerHTML = '';
            const icon = document.createElement('i');
            icon.className = 'fas fa-folder-open';
            icon.style.cssText = 'color:#94a3b8; font-size:9px; flex-shrink:0;';
            const label = document.createElement('span');
            label.textContent = catName;
            label.style.cssText = 'font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#475569;';
            catHeader.appendChild(icon);
            catHeader.appendChild(label);
            list.appendChild(catHeader);

            items.forEach(item => {
                const row = document.createElement('button');
                row.type = 'button';
                row.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors rounded-md mx-1';
                row.style.width = 'calc(100% - 8px)';
                row.textContent = `${item.no} ${item.uraian}`;

                if (selectedItem && selectedItem.id === item.id) {
                    row.classList.add('bg-blue-50', 'text-blue-700', 'font-semibold');
                }

                row.addEventListener('click', () => {
                    selectedItem = item;

                    document.getElementById('custom-select-label').textContent = `${item.no} ${item.uraian}`;
                    document.getElementById('custom-select-label').style.color = '#1e293b';

                    hiddenSelect.innerHTML = `<option value="${item.id}" data-vol-target="${item.volTarget}" data-satuan="${item.satuan}" data-vol-current="${item.volTercapai}" selected>${item.no} ${item.uraian}</option>`;
                    hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));

                    closePanel();
                });

                list.appendChild(row);
            });
        });
    };

    const openPanel = () => {
        panel.classList.remove('hidden');
        document.getElementById('custom-select-chevron').style.transform = 'rotate(180deg)';
        panel.querySelector('#custom-select-search').focus();
        renderList();
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        document.getElementById('custom-select-chevron').style.transform = '';
    };

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.contains('hidden') ? openPanel() : closePanel();
    });

    panel.querySelector('#custom-select-search').addEventListener('input', (e) => {
        renderList(e.target.value);
    });

    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) closePanel();
    });
}



function _collectLeafItems(nodes) {
    const result = [];
    const traverse = (items) => {
        items.forEach(node => {
            const hasChildren = node.children && node.children.length > 0;
            if (hasChildren) {
                traverse(node.children);
            } else {
                result.push(node);
            }
        });
    };
    traverse(nodes);
    return result;
}


function _bindDropdownChange() {
    document.addEventListener('change', (e) => {
        if (!e.target.matches(SEL_TASK_SELECT)) return;

        const selected = e.target.selectedOptions[0];
        const volTargetInput = document.querySelector(SEL_VOL_TARGET);
        if (!volTargetInput) return;

        if (!selected || !selected.value) {
            volTargetInput.value = '';
            return;
        }

        const volTarget = parseFloat(selected.dataset.volTarget) || 0;
        const volCurrent = parseFloat(selected.dataset.volCurrent) || 0;
        const volSisa = Math.max(0, volTarget - volCurrent);
        const satuan    = selected.dataset.satuan || '';
        
        volTargetInput.value = volTarget;
        const satuanInput = document.querySelector(SEL_SATUAN);
        if (satuanInput) satuanInput.value = satuan;

        const volTargetDisplay = document.getElementById('real-vol-target-display');
        if (volTargetDisplay) volTargetDisplay.textContent = volTarget;

        const volSisaDisplay = document.getElementById('real-vol-sisa-display');
        if (volSisaDisplay) volSisaDisplay.textContent = volSisa.toFixed(2).replace(/\.00$/, '');

        const satuanDisplay = document.getElementById('real-satuan-display');
        if (satuanDisplay) satuanDisplay.textContent = satuan || '-';
    });
}

function _bindAddButton() {
    document.addEventListener('click', (e) => {
        if (!e.target.closest(SEL_BTN_ADD) && !e.target.closest('#btn-add-progress-mobile')) return;

        const select        = document.querySelector(SEL_TASK_SELECT);
        const volActualEl   = document.querySelector(SEL_VOL_ACTUAL);
        const keteranganEl  = document.querySelector(SEL_KETERANGAN);

        if (!select || !volActualEl) return;

        const selectedOption = select.selectedOptions[0];
        const volActual      = parseFloat(volActualEl.value);

        if (!selectedOption || !selectedOption.value) {
            _showFieldError(select, 'Pilih pekerjaan terlebih dahulu.');
            return;
        }

        if (isNaN(volActual) || volActual <= 0) {
            _showFieldError(volActualEl, 'Volume harus lebih dari 0.');
            return;
        }

        const volCurrent = parseFloat(selectedOption.dataset.volCurrent) || 0;
        const volTarget  = parseFloat(selectedOption.dataset.volTarget)  || 0;
        const volSisa    = Math.max(0, volTarget - volCurrent);
        if (volTarget > 0 && (volCurrent + volActual) > volTarget) {
            _showFieldError(volActualEl, `Volume melebihi sisa target yang tersedia (${volSisa.toFixed(2).replace(/\.00$/, '')} ${selectedOption.dataset.satuan}).`);
            return;
        }

        const item = {
            _batchId      : Date.now(),
            id_rap_detail : parseInt(selectedOption.value),
            taskLabel     : selectedOption.textContent,
            volTarget     : volTarget,
            volSisa       : volSisa,
            volActual     : volActual,
            satuan        : selectedOption.dataset.satuan || '',
            keterangan    : keteranganEl?.value.trim() || '',
        };

        const { batchItems } = getState();
        updateState({ batchItems: [...batchItems, item] });

        _renderBatchTable();
        _resetInputRow(select, volActualEl, keteranganEl);
    });
}

function _renderBatchTable() {
    const tbody = document.querySelector(SEL_BATCH_TBODY);
    if (!tbody) return;

    const { batchItems } = getState();

    if (batchItems.length === 0) {
        tbody.innerHTML = '';
        return;
    }

    tbody.innerHTML = batchItems.map((item, index) => `
        <tr class="bg-slate-50 text-sm" data-batch-id="${item._batchId}">
            <td class="px-4 py-3 text-center text-slate-600 font-medium">${index + 1}</td>
            <td class="px-4 py-3 font-medium text-slate-700">${item.taskLabel}</td>
            <td class="px-4 py-3 text-center text-slate-600">${item.satuan}</td>
            <td class="px-4 py-3 text-center text-slate-600 font-medium">${item.volSisa !== undefined ? item.volSisa.toFixed(2).replace(/\.00$/, '') : item.volTarget}</td>
            <td class="px-4 py-3 text-center font-bold text-primary">${item.volActual}</td>
            <td class="px-4 py-3 text-slate-500 truncate max-w-[200px]" title="${item.keterangan}">${item.keterangan || '-'}</td>
            <td class="px-4 py-3 text-center">
                <button
                    type="button"
                    class="btn-remove-batch w-6 h-6 inline-flex items-center justify-center rounded bg-red-50 hover:bg-red-500 text-red-400 hover:text-white transition-colors"
                    data-batch-id="${item._batchId}"
                    title="Hapus">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </td>
        </tr>
    `).join('');

    tbody.querySelectorAll('.btn-remove-batch').forEach(btn => {
        btn.addEventListener('click', () => {
            const batchId   = parseInt(btn.dataset.batchId);
            const { batchItems } = getState();
            updateState({ batchItems: batchItems.filter(i => i._batchId !== batchId) });
            _renderBatchTable();
        });
    });
}

function _bindSaveButton() {
    document.addEventListener('click', async (e) => {
        if (!e.target.closest(SEL_BTN_SAVE)) return;

        const tanggalEl = document.getElementById('real-tanggal');
        const { batchItems } = getState();

        if (!tanggalEl?.value) {
            toast.show('Pilih tanggal pelaksanaan terlebih dahulu.', 'warning');
            return;
        }

        if (batchItems.length === 0) {
            toast.show('Tambahkan minimal satu item progress terlebih dahulu.', 'warning');
            return;
        }

        const saveBtn = e.target.closest(SEL_BTN_SAVE);
        _setButtonLoading(saveBtn, true);

        try {
            const formData = new FormData();
            formData.append('tanggal', tanggalEl.value);
            formData.append('items', JSON.stringify(
                batchItems.map(({ id_rap_detail, volActual, keterangan }) => ({
                    id_rap_detail,
                    volume_tercapai : volActual,
                    keterangan,
                }))
            ));

            uploadedFiles.forEach((fileObj, i) => {
                formData.append(`foto[${i}]`, fileObj.file);
            });

            const slug = window.REALISASI_INIT?.slug || '';
            const response = await fetch(`/realisasi/${slug}/store`, {
                method : 'POST',
                body   : formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Gagal menyimpan data.');
            }

            toast.show('Progress berhasil disimpan!', 'success');
            _resetModal();
            setTimeout(() => {
                window.location.reload();
            }, 1000);

        } catch (err) {
            console.error('[RealisasiModal] Save error:', err);
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
            const btn = e.target.closest('.btn-remove-foto');
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
            <button type="button" class="btn-remove-foto flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" data-id="${f.id}">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>
    `).join('');
}

function _bindModalOpen() {
    const openBtn = document.querySelector('[data-hs-overlay="#modal-tambah-realisasi"]');
    if (!openBtn) return;

    openBtn.addEventListener('click', () => {
        _populateTaskDropdown();
        
        const tanggalEl = document.getElementById('real-tanggal');
        if (tanggalEl && !tanggalEl.value) {
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            tanggalEl.value = today;
        }
    });
}

function _bindDeleteLog() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-delete-log');
        if (!btn) return;

        const logId = parseInt(btn.dataset.logId, 10);
        if (!logId) return;

        const confirmed = await confirmDelete('catatan progress ini');
        if (!confirmed) return;

        btn.disabled = true;

        try {
            const response = await fetch(`/realisasi/pekerjaan/log/${logId}`, {
                method : 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Gagal menghapus log.');
            }

            const { realisasiData } = getState();
            _removeLogFromState(realisasiData, logId);
            updateState({ realisasiData });

            const tbody = document.getElementById('realisasi-tbody');
            if (tbody) {
                renderTable(getFilteredData(), tbody);
            }

            toast.show('Log progress berhasil dihapus.', 'success');
        } catch (err) {
            console.error('[RealisasiModal] Delete log error:', err);
            toast.show(`Gagal menghapus: ${err.message}`, 'error');
            btn.disabled = false;
        }
    });
}

function _removeLogFromState(nodes, logId) {
    for (const category of nodes) {
        _removeLogFromItems(category.children || [], logId);
    }
}

function _removeLogFromItems(items, logId) {
    for (const item of items) {
        if (item.logs) {
            const logToRemove = item.logs.find(l => l.id_realisasi === logId);
            if (logToRemove) {
                const removedVol = logToRemove.volRaw || 0;
                item.volTercapai = Math.max(0, item.volTercapai - removedVol);
                
                item.volumeTercapai = `${item.volTercapai} ${item.satuan}`;
                const pct = item.volTarget > 0 ? (item.volTercapai / item.volTarget) * 100 : 0;
                item.progress = `${pct.toFixed(2)}%`;
                
                item.logs = item.logs.filter(l => l.id_realisasi !== logId);
                return true;
            }
        }
        if (item.children?.length) {
            if (_removeLogFromItems(item.children, logId)) return true;
        }
    }
    return false;
}

function _resetInputRow(selectEl, volActualEl, keteranganEl) {
    // Reset custom dropdown trigger label
    const labelEl = document.getElementById('custom-select-label');
    if (labelEl) {
        labelEl.textContent = 'Pilih Pekerjaan...';
        labelEl.style.color = '';
    }
    // Reset hidden select
    if (selectEl) selectEl.innerHTML = '';

    if (volActualEl) volActualEl.value = '';
    if (keteranganEl) keteranganEl.value = '';

    const volTargetEl = document.querySelector(SEL_VOL_TARGET);
    if (volTargetEl) volTargetEl.value = '';
    const volTargetDisplay = document.getElementById('real-vol-target-display');
    if (volTargetDisplay) volTargetDisplay.textContent = '0';

    const volSisaDisplay = document.getElementById('real-vol-sisa-display');
    if (volSisaDisplay) volSisaDisplay.textContent = '0';

    const satuanEl = document.querySelector(SEL_SATUAN);
    if (satuanEl) satuanEl.value = '';
    const satuanDisplay = document.getElementById('real-satuan-display');
    if (satuanDisplay) satuanDisplay.textContent = '-';
}

function _resetModal() {
    uploadedFiles = [];
    updateState({ batchItems: [] });
    _renderBatchTable();
    _renderFilePreviews();

    const tanggalEl = document.getElementById('real-tanggal');
    if (tanggalEl) tanggalEl.value = '';

    const select = document.querySelector(SEL_TASK_SELECT);
    _resetInputRow(select, document.querySelector(SEL_VOL_ACTUAL), document.querySelector(SEL_KETERANGAN));
}

function _setButtonLoading(btn, isLoading) {
    if (!btn) return;
    btn.disabled  = isLoading;
    btn.innerHTML = isLoading
        ? '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...'
        : '<i class="fas fa-save"></i> Simpan';
}

function _showFieldError(element, message) {
    element.classList.add('border-red-400', 'ring-1', 'ring-red-300');
    element.focus();
    toast.show(message, 'warning'); 
    element.classList.remove('border-red-400', 'ring-1', 'ring-red-300');
}
