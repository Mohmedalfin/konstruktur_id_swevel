/**
 * components/import.js
 * Handles reading a BOQ Excel file via ExcelJS, rendering the preview modal,
 * and dispatching the 'rabDataImported' custom event when confirmed.
 */

import { fetchKategoriMaster } from '../core/data.js';
import { generateTemplate } from './template.js';

// Cache of available categories (loaded from API)
let availableCategories = [];

// Expose refresh so index.js can update after a new custom category is added
export async function refreshImportCategories() {
    const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
    if (!idProject) return;
    try {
        availableCategories = await fetchKategoriMaster(idProject);
        const sel = document.getElementById('import-global-kategori');
        if (sel) {
            sel.innerHTML = '<option value="">-- Buat Otomatis Sesuai Baris --</option>';
            availableCategories.forEach(cat => {
                const id = cat.id_kategori_pekerjaan || cat.id;
                const nama = cat.nama_kategori || cat.name || cat.nama;
                sel.innerHTML += `<option value="${id}">${nama}</option>`;
            });
        }
    } catch (_) {
        availableCategories = [];
    }
}


const formatRp = (val) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);

const parseNumber = (val) => {
    if (!val) return 0;
    if (typeof val === 'number') return val;
    if (typeof val === 'object') return val.result || 0;
    const clean = val.toString().replace(/[^\d.,-]/g, '').replace(/,/g, '');
    return parseFloat(clean) || 0;
};

const SYSTEM_FIELDS = [
    { key: 'uraian', label: 'Uraian Pekerjaan', required: true, keywords: ['uraian', 'pekerjaan', 'item', 'deskripsi', 'nama'], width: 'col' },
    { key: 'volume', label: 'Volume', required: true, keywords: ['vol', 'volume', 'qty', 'kuantitas', 'jumlah'], width: 'col style="width: 7rem"' },
    { key: 'satuan', label: 'Satuan', required: true, keywords: ['sat', 'satuan', 'unit'], width: 'col style="width: 6rem"' },
    { key: 'kategori', label: 'Kategori', required: false, keywords: [], width: 'col style="width: 10rem"' }
];

let globalWorksheet = null;
let excelColumns = [];
let rawDataStore = [];
let currentMapping = {};
let currentStep = 1;
let organizedItems = []; // { id, nama, volume, satuan, type, level }
let selectedIndices = new Set();

function _openModal(overlay) {
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        document.body.style.overflow = 'hidden';
        _setStep(1);
    }
}

function _closeModal(overlay) {
    if (overlay) { overlay.classList.add('hidden'); overlay.classList.remove('flex'); document.body.style.overflow = ''; }
}

function _setStep(step) {
    currentStep = step;
    const s1 = document.getElementById('import-step-1');
    const s2 = document.getElementById('import-step-2');
    const btnNext = document.getElementById('import-rab-modal-next');
    const btnBack = document.getElementById('import-rab-modal-back');
    const btnConfirm = document.getElementById('import-rab-modal-confirm');
    const btnCancel = document.getElementById('import-rab-modal-cancel');
    const btnRepick = document.getElementById('import-rab-modal-repick');

    if (step === 1) {
        s1.classList.remove('hidden');
        s2.classList.add('hidden');
        btnNext.classList.remove('hidden');
        btnBack.classList.add('hidden');
        btnConfirm.classList.add('hidden');
        btnCancel.classList.remove('hidden');
        btnRepick?.classList.remove('hidden');
    } else {
        s1.classList.add('hidden');
        s2.classList.remove('hidden');
        btnNext.classList.add('hidden');
        btnBack.classList.remove('hidden');
        btnConfirm.classList.remove('hidden');
        btnCancel.classList.add('hidden');
        btnRepick?.classList.add('hidden');
        _renderOrganizeList();
    }
}

function _autoMapColumns() {
    currentMapping = {};
    const usedIndices = new Set();
    
    SYSTEM_FIELDS.forEach(sf => {
        if (sf.key === 'kategori') return; 
        let foundIdx = 0;
        for (let i = 1; i < excelColumns.length; i++) {
            if (usedIndices.has(i)) continue;
            const colName = excelColumns[i].name.toLowerCase();
            if (sf.keywords.some(kw => colName.includes(kw))) {
                foundIdx = i;
                usedIndices.add(i);
                break;
            }
        }
        currentMapping[sf.key] = foundIdx;
    });
}

function _renderTableHeaders(thead) {
    let colgroupHtml = '<colgroup><col style="width: 3.5rem">';
    let trHtml = `<tr><th class="px-2 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-100 border-r border-slate-200 sticky left-0 z-20 align-bottom pb-2">No</th>`;

    for (let i = 1; i < excelColumns.length; i++) {
        const col = excelColumns[i];
        colgroupHtml += `<col style="width: 14rem">`;
        let mappedSysKey = '';
        Object.keys(currentMapping).forEach(sysKey => {
            if (currentMapping[sysKey] === i) mappedSysKey = sysKey;
        });

        const isMapped = mappedSysKey !== '';
        const headerTheme = isMapped ? 'bg-blue-50 border-blue-200' : 'bg-white border-slate-200';

        let selectOptions = `<option value="">-- Abaikan Kolom Ini --</option>`;
        SYSTEM_FIELDS.forEach(sf => {
            if (sf.key === 'kategori') return;
            const reqMarker = sf.required ? ' *' : '';
            selectOptions += `<option value="${sf.key}" ${mappedSysKey === sf.key ? 'selected' : ''}>${sf.label}${reqMarker}</option>`;
        });

        trHtml += `
            <th class="px-2 py-2 border-r border-slate-200 bg-slate-50 align-top group min-w-[180px]">
                <div class="text-[11px] font-bold tracking-wider mb-2 flex items-center justify-center text-slate-700 truncate" title="${col.name}">[Excel] ${col.name}</div>
                <div class="flex items-center justify-between bg-white border ${headerTheme} rounded transition-colors duration-200 shadow-sm overflow-hidden mt-auto">
                    <select class="sys-mapping-select w-full text-[10px] font-bold text-center appearance-none outline-none cursor-pointer truncate px-2 py-1.5 ${isMapped ? 'text-blue-700' : 'text-slate-500'} bg-transparent" data-col-idx="${i}">
                        ${selectOptions}
                    </select>
                </div>
            </th>
        `;
    }
    
    colgroupHtml += '</colgroup>';
    trHtml += '</tr>';

    thead.parentElement.innerHTML = colgroupHtml + `<thead class="sticky top-0 z-10 shadow-sm border-b border-table-border" id="import-rab-modal-thead">${trHtml}</thead>` + `<tbody id="import-rab-modal-tbody" class="text-[11px] md:text-[13px] text-table-body"></tbody>`;
    
    const newThead = document.getElementById('import-rab-modal-thead');
    const newTbody = document.getElementById('import-rab-modal-tbody');

    newThead.querySelectorAll('.sys-mapping-select').forEach(sel => {
        sel.addEventListener('change', (e) => {
            const colIdx = parseInt(e.currentTarget.dataset.colIdx);
            const selectedSysKey = e.currentTarget.value;
            if (selectedSysKey !== '') {
                Object.keys(currentMapping).forEach(k => {
                    if (currentMapping[k] === colIdx) currentMapping[k] = 0;
                    if (k === selectedSysKey) currentMapping[k] = 0;
                });
                currentMapping[selectedSysKey] = colIdx;
            } else {
                Object.keys(currentMapping).forEach(k => {
                    if (currentMapping[k] === colIdx) currentMapping[k] = 0;
                });
            }
            const createdTbody = _renderTableHeaders(newThead);
            _renderTableBody(createdTbody);
            _validateImportState();
        });
    });

    return newTbody;
}

function _renderTableBody(tbody) {
    if (rawDataStore.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${excelColumns.length + 1}" class="text-center py-20 text-table-subtle text-xs italic">Tidak ada baris data dibaca.</td></tr>`;
        return;
    }

    const mapUraian = currentMapping.uraian || 0;
    const mapVolume = currentMapping.volume || 0;

    let html = '';
    rawDataStore.forEach((rowVals, index) => {
        const rawVolCell = mapVolume !== 0 ? rowVals[excelColumns[mapVolume].idxExcel] : null;
        let isVolEmpty = (mapVolume !== 0 && (rawVolCell === null || rawVolCell === undefined || rawVolCell.toString().trim() === ''));
        const hasUraian = mapUraian !== 0 && !!rowVals[excelColumns[mapUraian].idxExcel];
        const isHeaderStyle = isVolEmpty && hasUraian;
        const bgClass   = isHeaderStyle ? 'bg-amber-50/50' : 'bg-white';
        const textClass = isHeaderStyle ? 'font-bold text-amber-900' : 'font-medium text-table-strong';

        html += `<tr class="border-b border-table-border ${bgClass} hover:bg-slate-50 transition-colors">`;
        html += `<td class="px-2 py-2 text-center text-table-subtle font-medium tabular-nums sticky left-0 ${bgClass} border-r border-slate-100">${index + 1}</td>`;

        for (let i = 1; i < excelColumns.length; i++) {
            const excelIdx = excelColumns[i].idxExcel;
            const rawVal = rowVals[excelIdx];
            let mappedSysKey = '';
            Object.keys(currentMapping).forEach(sysKey => {
                if (currentMapping[sysKey] === i) mappedSysKey = sysKey;
            });

            let cellVal = '';
            let align = 'text-left';
            let extraClasses = '';
            if (rawVal !== null && rawVal !== undefined) {
                const txt = typeof rawVal === 'object' ? (rawVal.result || '') : rawVal.toString();
                if (mappedSysKey === 'volume' || mappedSysKey === 'satuan') {
                    cellVal = isHeaderStyle ? '' : txt; align = 'text-center';
                } else if (mappedSysKey === 'uraian') {
                    cellVal = txt; extraClasses = `${textClass} truncate max-w-[280px]`;
                } else {
                    cellVal = txt; extraClasses = 'text-slate-400 max-w-[170px] truncate';
                }
            }
            html += `<td class="px-3 py-2 align-middle ${align} ${extraClasses}" title="${cellVal}">${cellVal || '-'}</td>`;
        }
        html += `</tr>`;
    });
    tbody.innerHTML = html;
}

function _validateImportState() {
    const btnNext = document.getElementById('import-rab-modal-next');
    if (!btnNext) return;
    if (!currentMapping.uraian || !currentMapping.volume || !currentMapping.satuan) {
        btnNext.disabled = true;
    } else {
        btnNext.disabled = false;
    }
}

function _prepareOrganizedData() {
    if (!globalWorksheet) return;
    const mapUraian = currentMapping.uraian ? excelColumns[currentMapping.uraian].idxExcel : -1;
    const mapVolume = currentMapping.volume ? excelColumns[currentMapping.volume].idxExcel : -1;
    const mapSatuan = currentMapping.satuan ? excelColumns[currentMapping.satuan].idxExcel : -1;

    organizedItems = [];
    globalWorksheet.eachRow((row, rowNumber) => {
        if (rowNumber === 1) return;
        const vals = row.values;
        const nama = mapUraian !== -1 && vals[mapUraian] ? vals[mapUraian].toString().trim() : '';
        if (!nama) return;

        const volRaw = mapVolume !== -1 ? vals[mapVolume] : null;
        const isVolEmpty = (volRaw === null || volRaw === undefined || volRaw.toString().trim() === '');
        
        organizedItems.push({
            id: 'temp-' + Date.now() + '-' + rowNumber,
            nama: nama,
            volume: isVolEmpty ? 0 : parseNumber(volRaw),
            satuan: mapSatuan !== -1 && vals[mapSatuan] ? vals[mapSatuan].toString().trim() : '-',
            type: isVolEmpty ? 'kategori' : 'item',
            level: 0
        });
    });
    selectedIndices.clear();
}

function _renderOrganizeList() {
    const container = document.getElementById('import-organize-list');
    if (!container) return;

    if (organizedItems.length === 0) {
        container.innerHTML = '<div class="py-20 text-center text-slate-400 text-sm italic">Data kosong.</div>';
        return;
    }

    let html = '';
    organizedItems.forEach((item, idx) => {
        const isSelected = selectedIndices.has(idx);
        const isCat = item.type === 'kategori';
        const isInsertedKat = item.id.startsWith('temp-kat-');
        const indent = item.level * 1.5;

        html += `
            <div class="organize-item p-3 group transition-colors duration-150 flex items-center gap-3 ${isSelected ? 'bg-primary/5' : 'hover:bg-slate-50'}" data-index="${idx}">
                <!-- Left Slot (Checkbox or Trash) -->
                <div class="flex-shrink-0 ml-1 w-5 flex justify-center">
                    ${isInsertedKat ? `
                        <button type="button" class="organize-delete-kat text-red-500 hover:text-red-700 transition-colors focus:outline-none" data-index="${idx}" title="Hapus Kategori">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    ` : isCat ? `
                        <!-- Hidden checkbox for category -->
                    ` : `
                        <input type="checkbox" class="organize-check w-4 h-4 rounded text-primary cursor-pointer border-slate-300" ${isSelected ? 'checked' : ''} data-index="${idx}">
                    `}
                </div>

                <!-- Drag Handle -->
                <div class="cursor-grab text-slate-300 hover:text-slate-500 transition-colors drag-handle-organize">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                </div>

                <!-- Content -->
                <div class="flex-1 flex items-center min-w-0" style="padding-left: ${indent}rem">
                    ${item.level > 0 ? `<span class="text-slate-300 mr-2 shrink-0">└─</span>` : ''}
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs ${isCat ? 'font-bold text-slate-900 uppercase tracking-wide' : 'text-slate-700'} truncate">${item.nama}</span>
                        ${!isCat ? `<span class="text-[10px] text-slate-400 font-medium">${item.volume} ${item.satuan}</span>` : ''}
                    </div>
                </div>

                <!-- Type Toggle / Action -->
                <div class="flex-shrink-0">
                    ${isInsertedKat ? `
                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] bg-primary/10 text-primary font-bold uppercase border border-primary/20">
                            <i class="fas fa-layer-group mr-1.5"></i> Kategori
                        </span>
                    ` : `
                        <button type="button" class="organize-toggle-type inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-tight focus:outline-none transition-all shadow-sm ${isCat ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 border border-amber-200' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50 hover:text-primary'}" data-index="${idx}">
                            ${isCat ? '<i class="fas fa-undo mr-1.5"></i> Jadikan Pekerjaan' : '<i class="fas fa-layer-group mr-1.5"></i> Jadikan Kategori'}
                        </button>
                    `}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;

    // Bind events
    container.querySelectorAll('.organize-check').forEach(ck => {
        ck.addEventListener('change', (e) => {
            const idx = parseInt(e.target.dataset.index);
            if (e.target.checked) selectedIndices.add(idx);
            else selectedIndices.delete(idx);
            _renderOrganizeList();
        });
    });

    container.querySelectorAll('.organize-toggle-type').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const idx = parseInt(e.currentTarget.dataset.index);
            const currentType = organizedItems[idx].type;
            organizedItems[idx].type = currentType === 'kategori' ? 'item' : 'kategori';
            if (organizedItems[idx].type === 'kategori') {
                organizedItems[idx].level = 0; // Kategori is always root
                selectedIndices.delete(idx); // Remove from selection if turning into kat
            }
            _renderOrganizeList();
        });
    });

    container.querySelectorAll('.organize-delete-kat').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const idx = parseInt(e.currentTarget.dataset.index);
            organizedItems.splice(idx, 1);
            
            // Adjust selectedIndices
            const newIndices = new Set();
            selectedIndices.forEach(val => {
                if (val > idx) newIndices.add(val - 1);
                else if (val < idx) newIndices.add(val);
            });
            selectedIndices = newIndices;
            
            _renderOrganizeList();
        });
    });

    // Re-bind SortableJS
    if (window.Sortable) {
        if (window.organizeSortable) window.organizeSortable.destroy();
        window.organizeSortable = new Sortable(container, {
            animation: 150,
            handle: '.drag-handle-organize',
            onEnd: (evt) => {
                const item = organizedItems.splice(evt.oldIndex, 1)[0];
                organizedItems.splice(evt.newIndex, 0, item);
                selectedIndices.clear();
                _renderOrganizeList();
            }
        });
    }
}

function _indentItems(delta) {
    selectedIndices.forEach(idx => {
        organizedItems[idx].level = Math.max(0, organizedItems[idx].level + delta);
    });
    _renderOrganizeList();
}

function _setType(type) {
    selectedIndices.forEach(idx => {
        organizedItems[idx].type = type;
        if (type === 'kategori') organizedItems[idx].level = 0;
    });
    _renderOrganizeList();
}

function _buildHierarchy(items) {
    const result = [];
    const stack = [{ level: -1, children: result }];

    items.forEach(item => {
        const node = { ...item, children: [] };
        while (stack.length > 1 && stack[stack.length - 1].level >= item.level) {
            stack.pop();
        }
        stack[stack.length - 1].children.push(node);
        stack.push(node);
    });
    return result;
}

export async function initImport() {
    const fileInput    = document.getElementById('boq-file-input');
    const modalOverlay = document.getElementById('import-rab-modal-overlay');
    const modalCancel  = document.getElementById('import-rab-modal-cancel');
    const modalConfirm = document.getElementById('import-rab-modal-confirm');
    const modalNext    = document.getElementById('import-rab-modal-next');
    const modalBack    = document.getElementById('import-rab-modal-back');
    const modalClose   = document.getElementById('import-rab-modal-close');
    const modalRepick  = document.getElementById('import-rab-modal-repick');
    
    // Tools
    document.getElementById('import-organize-indent-in')?.addEventListener('click', () => _indentItems(1));
    document.getElementById('import-organize-indent-out')?.addEventListener('click', () => _indentItems(-1));

    document.getElementById('import-organize-insert-cat')?.addEventListener('click', () => {
        const selKat = document.getElementById('import-global-kategori');
        if (!selKat || !selKat.value) {
            alert('Pilih Kategori Master terlebih dahulu dari menu dropdown!');
            return;
        }

        const categoryId = selKat.value;
        const categoryName = selKat.options[selKat.selectedIndex].text;

        let insertIdx = 0;
        if (selectedIndices.size > 0) {
            insertIdx = Math.min(...Array.from(selectedIndices));
        }

        organizedItems.splice(insertIdx, 0, {
            id: 'temp-kat-' + Date.now(),
            nama: categoryName,
            id_kategori_master: categoryId,
            volume: 0,
            satuan: '-',
            type: 'kategori',
            level: 0
        });

        // Shift down the selected indices since we inserted a new item before them
        if (selectedIndices.size > 0) {
            const newIndices = new Set();
            selectedIndices.forEach(val => newIndices.add(val + 1));
            selectedIndices = newIndices;
        }

        _renderOrganizeList();
    });

    modalNext?.addEventListener('click', () => {
        _prepareOrganizedData();
        _setStep(2);
    });
    modalBack?.addEventListener('click', () => _setStep(1));
    modalCancel?.addEventListener('click', () => _closeModal(modalOverlay));
    modalClose?.addEventListener('click', () => _closeModal(modalOverlay));
    modalRepick?.addEventListener('click', () => {
        if (fileInput) fileInput.click();
    });

    if (fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            await refreshImportCategories();
            document.getElementById('import-file-name').textContent = file.name;
            _openModal(modalOverlay);
            try {
                const workbook = new ExcelJS.Workbook();
                await workbook.xlsx.load(await file.arrayBuffer());
                globalWorksheet = workbook.getWorksheet(1);
                excelColumns = [{ idxExcel: -1, name: '-- Kosongkan --' }];
                rawDataStore = [];
                globalWorksheet.getRow(1).eachCell((c, colNum) => excelColumns.push({ idxExcel: colNum, name: c.text?.toString().trim() || `Kolom ${colNum}` }));
                globalWorksheet.eachRow((row, rowNum) => { if (rowNum > 1 && rowNum <= 101) rawDataStore.push(row.values); });
                _autoMapColumns();
                const newTbody = _renderTableHeaders(document.getElementById('import-rab-modal-thead'));
                _renderTableBody(newTbody);
                _validateImportState();
                document.getElementById('import-rab-modal-count').innerHTML = `<span class="text-emerald-600 font-semibold">${globalWorksheet.rowCount - 1} baris</span> terbaca.`;
            } catch (err) { alert('Gagal baca Excel: ' + err.message); }
        });
    }

    modalConfirm?.addEventListener('click', async () => {
        const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
        const hierarchicalData = _buildHierarchy(organizedItems);
        const selKat = document.getElementById('import-global-kategori');
        const idKategori = selKat && selKat.value ? parseInt(selKat.value) : null;

        modalConfirm.disabled = true;
        modalConfirm.innerHTML = '<span class="animate-spin mr-2">...</span> Menyimpan';

        try {
            const res = await fetch('/api/rap/import', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id_project: Number(idProject), 
                    id_kategori: idKategori,
                    items: hierarchicalData 
                })
            });
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message);
            
            if (window.Toast) window.Toast.show('BOQ berhasil diimpor', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } catch (err) {
            alert('Gagal simpan: ' + err.message);
            modalConfirm.disabled = false;
            modalConfirm.textContent = 'Simpan ke RAB';
        }
    });

    // Handle template generating & trigger
    document.getElementById('boq-import-btn')?.addEventListener('click', () => {
        const overlay = document.getElementById('import-prompt-modal-overlay');
        const content = document.getElementById('import-prompt-modal-content');
        
        overlay?.classList.remove('hidden');
        overlay?.classList.add('flex');
        
        setTimeout(() => {
            overlay?.classList.remove('opacity-0');
            overlay?.classList.add('opacity-100');
            content?.classList.remove('scale-95');
            content?.classList.add('scale-100');
        }, 10);
    });

    document.getElementById('import-prompt-modal-excel')?.addEventListener('click', () => {
        document.getElementById('import-prompt-modal-overlay')?.classList.add('hidden');
        fileInput.click();
    });

    document.getElementById('import-prompt-modal-template')?.addEventListener('click', () => {
        document.getElementById('import-prompt-modal-overlay')?.classList.add('hidden');
        generateTemplate();
    });

    document.getElementById('import-prompt-modal-cancel')?.addEventListener('click', () => {
        const overlay = document.getElementById('import-prompt-modal-overlay');
        const content = document.getElementById('import-prompt-modal-content');
        
        overlay?.classList.add('opacity-0');
        overlay?.classList.remove('opacity-100');
        content?.classList.add('scale-95');
        content?.classList.remove('scale-100');
        
        setTimeout(() => {
            overlay?.classList.add('hidden');
            overlay?.classList.remove('flex');
        }, 300);
    });
}
