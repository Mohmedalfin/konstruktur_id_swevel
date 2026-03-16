/**
 * components/import.js
 * Handles reading a BOQ Excel file via ExcelJS, rendering the preview modal,
 * and dispatching the 'rabDataImported' custom event when confirmed.
 */

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
let excelColumns = []; // [{idxExcel: -1, name: '-- Kosongkan --'}, {idxExcel: 1, name: 'Uraian'}, ...]
let rawDataStore = []; // The raw rows from excel (up to ~100 for preview)
let currentMapping = {}; // key: index in excelColumns array (e.g., uraian: 1)

function _openModal(overlay) {
    if (overlay) { overlay.classList.remove('hidden'); overlay.classList.add('flex'); document.body.style.overflow = 'hidden'; }
}

function _closeModal(overlay) {
    if (overlay) { overlay.classList.add('hidden'); overlay.classList.remove('flex'); document.body.style.overflow = ''; }
}

function _autoMapColumns() {
    currentMapping = {};
    const usedIndices = new Set();
    
    SYSTEM_FIELDS.forEach(sf => {
        if (sf.key === 'kategori') return; 
        let foundIdx = 0; // Default '-- Kosongkan --'
        for (let i = 1; i < excelColumns.length; i++) {
            if (usedIndices.has(i)) continue; // Don't claim an already mapped column
            
            const colName = excelColumns[i].name.toLowerCase();
            // exact word or very close substring match
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
    let colgroupHtml = '<colgroup><col style="width: 3.5rem">'; // No
    
    let trHtml = `
        <tr>
            <th class="px-2 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-100 border-r border-slate-200 sticky left-0 z-20 align-bottom pb-2">No</th>
    `;

    // Loop through ALL parsed Excel Columns (skipping index 0 which is our internal '-- Kosongkan --')
    for (let i = 1; i < excelColumns.length; i++) {
        const col = excelColumns[i];
        colgroupHtml += `<col style="width: 14rem">`;
        
        // Find if this excel column is currently mapped to a system field
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
                <!-- Native Excel Field Header -->
                <div class="text-[11px] font-bold tracking-wider mb-2 flex items-center justify-center text-slate-700 truncate" title="${col.name}">
                    [Excel] ${col.name}
                </div>
                
                <!-- Dropdown to assign to System Column -->
                <div class="flex items-center justify-between bg-white border ${headerTheme} rounded transition-colors duration-200 shadow-sm overflow-hidden mt-auto">
                    <select class="sys-mapping-select w-full text-[10px] font-bold text-center appearance-none outline-none cursor-pointer truncate px-2 py-1.5 ${isMapped ? 'text-blue-700' : 'text-slate-500'} bg-transparent" data-col-idx="${i}" title="Petakan ke Kolom Sistem">
                        ${selectOptions}
                    </select>
                </div>
            </th>
        `;
    }
    
    // Always render Kategori at the far right
    colgroupHtml += `<col style="width: 10rem">`;
    trHtml += `<th class="px-3 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-100 align-bottom pb-2">Kategori (Sistem)</th>`;

    colgroupHtml += '</colgroup>';
    trHtml += '</tr>';

    thead.parentElement.innerHTML = colgroupHtml + `<thead class="sticky top-0 z-10 shadow-sm border-b border-table-border" id="import-rab-modal-thead">${trHtml}</thead>` + `<tbody id="import-rab-modal-tbody" class="text-[11px] md:text-[13px] text-table-body"></tbody>`;
    
    const newThead = document.getElementById('import-rab-modal-thead');
    const newTbody = document.getElementById('import-rab-modal-tbody');

    // Attach Mapping Change Event
    newThead.querySelectorAll('.sys-mapping-select').forEach(sel => {
        sel.addEventListener('change', (e) => {
            const colIdx = parseInt(e.currentTarget.dataset.colIdx);
            const selectedSysKey = e.currentTarget.value;
            
            // If another column already holds this system key, unmap it first
            if (selectedSysKey !== '') {
                Object.keys(currentMapping).forEach(k => {
                    if (currentMapping[k] === colIdx) currentMapping[k] = 0; // Clear old mapping for this column
                    if (k === selectedSysKey) currentMapping[k] = 0; // Clear the system key from its previous owner
                });
                currentMapping[selectedSysKey] = colIdx;
            } else {
                // Determine which sys key was removed
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
        // Check if row operates as a "Header Row" 
        const rawVolCell = mapVolume !== 0 ? rowVals[excelColumns[mapVolume].idxExcel] : null;
        let isVolEmpty = false;
        if (mapVolume !== 0 && (rawVolCell === null || rawVolCell === undefined || rawVolCell.toString().trim() === '')) {
            isVolEmpty = true;
        }

        const hasUraian = mapUraian !== 0 && !!rowVals[excelColumns[mapUraian].idxExcel];
        const isHeaderStyle = isVolEmpty && hasUraian;
        
        const bgClass   = isHeaderStyle ? 'bg-amber-50/50' : 'bg-white';
        const textClass = isHeaderStyle ? 'font-bold text-amber-900' : 'font-medium text-table-strong';

        html += `<tr class="border-b border-table-border ${bgClass} hover:bg-slate-50 transition-colors">`;
        html += `<td class="px-2 py-2 text-center text-table-subtle font-medium tabular-nums sticky left-0 ${bgClass} border-r border-slate-100">${index + 1}</td>`;

        // Render ALL Excel columns
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
                
                if (mappedSysKey.startsWith('harga_')) {
                    const parsed = parseNumber(rawVal);
                    cellVal = isHeaderStyle ? '' : ((parsed === 0 && txt.trim() !== '0') ? txt : formatRp(parsed));
                    align = 'text-right tabular-nums';
                    extraClasses = isHeaderStyle ? '' : 'font-medium';
                } else if (mappedSysKey === 'volume' || mappedSysKey === 'satuan') {
                    cellVal = isHeaderStyle ? '' : txt;
                    align = 'text-center';
                } else if (mappedSysKey === 'uraian') {
                    cellVal = txt;
                    extraClasses = `${textClass} truncate max-w-[280px]`;
                } else {
                    // Unmapped raw text
                    cellVal = txt;
                    extraClasses = 'text-slate-400 max-w-[200px] truncate';
                }
            }
            
            html += `<td class="px-3 py-2 align-middle ${align} ${extraClasses}" title="${cellVal}">${cellVal || '-'}</td>`;
        }
        
        // Render system Kategori at the end
        const selectCat = `
            <select class="category-select w-full border border-slate-200 rounded text-[10px] px-1 py-1 focus:border-primary outline-none bg-white" data-index="${index}">
                <option value="persiapan">Pekerjaan Persiapan</option>
                <option value="tanah">Pekerjaan Tanah</option>
                <option value="struktur">Pekerjaan Struktur</option>
                <option value="arsitektur">Pekerjaan Arsitektur</option>
                <option value="mep">Pekerjaan MEP</option>
                <option value="finishing">Pekerjaan Finishing</option>
            </select>`;
        html += `<td class="px-2 py-2 text-center align-middle">${isHeaderStyle ? '' : selectCat}</td>`;
        
        html += `</tr>`;
    });

    tbody.innerHTML = html;
}

function _validateImportState() {
    const btnConfirm = document.getElementById('import-rab-modal-confirm');
    if (!btnConfirm) return;

    if (!currentMapping.uraian || !currentMapping.volume || !currentMapping.satuan) {
        btnConfirm.disabled = true;
        btnConfirm.title = 'Kolom data Excel untuk Uraian, Volume, dan Satuan wajib dipilih';
    } else {
        btnConfirm.disabled = false;
        btnConfirm.title = '';
    }
}

function _getFinalParsedData() {
    if (!globalWorksheet) return [];
    
    // Convert mapping object values to the actual Excel Column Index 
    // (In the new design `currentMapping[sysKey]` already stores the direct column index `i`,
    // and `excelColumns[i].idxExcel` is the true excel index). 
    // We get 0 if unmapped.
    const _getExcelIdx = (sysKey) => {
        const i = currentMapping[sysKey] || 0;
        return i === 0 ? -1 : excelColumns[i].idxExcel;
    };

    const mapUraian = _getExcelIdx('uraian');
    const mapVolume = _getExcelIdx('volume');
    const mapSatuan = _getExcelIdx('satuan');
    
    const finalData = [];
    let tbody = document.getElementById('import-rab-modal-tbody');
    let selects = tbody ? tbody.querySelectorAll('.category-select') : [];

    globalWorksheet.eachRow((row, rowNumber) => {
        if (rowNumber === 1) return; // Skip header
        
        const vals = row.values;
        // Verify we actually have a valid Uraian string mapped from the correct column
        const uraianTxt = mapUraian !== -1 && vals[mapUraian] ? (vals[mapUraian]).toString().trim() : '';
        if (!uraianTxt) return;

        const rawVolCell = mapVolume !== -1 ? vals[mapVolume] : null;
        let isVolEmpty = false;
        if (mapVolume !== -1 && (rawVolCell === null || rawVolCell === undefined || rawVolCell.toString().trim() === '')) {
            isVolEmpty = true;
        }

        const volVal = isVolEmpty ? 0 : parseNumber(rawVolCell);
        
        // Grab the category selection from the UI state if available
        let cat = 'persiapan';
        if (rowNumber - 2 < selects.length && !isVolEmpty) {
            cat = selects[rowNumber - 2].value;
        }

        finalData.push({
            id:          'import-' + Date.now() + '-' + rowNumber,
            uraian:      uraianTxt,
            volume:      volVal,
            satuan:      mapSatuan !== -1 && vals[mapSatuan] ? (vals[mapSatuan]).toString().trim() : '-',
            harga_bahan: 0,
            harga_alat:  0,
            harga_upah:  0,
            type:        !isVolEmpty ? 'item' : 'header',
            kategori:    cat
        });
    });
    
    return finalData;
}

export function initImport() {
    const importBtn    = document.getElementById('boq-import-btn');
    const fileInput    = document.getElementById('boq-file-input');
    const modalOverlay = document.getElementById('import-rab-modal-overlay');
    const modalClose   = document.getElementById('import-rab-modal-close');
    const modalCancel  = document.getElementById('import-rab-modal-cancel');
    const modalConfirm = document.getElementById('import-rab-modal-confirm');
    
    const countDisplay  = document.getElementById('import-rab-modal-count');
    const fileNameDisp  = document.getElementById('import-file-name');

    if (importBtn && fileInput) {
        importBtn.addEventListener('click', () => { fileInput.value = ''; fileInput.click(); });
    }

    if (fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const thead = document.getElementById('import-rab-modal-thead');
            const tbody = document.getElementById('import-rab-modal-tbody');
            
            modalConfirm.classList.remove('hidden');
            modalConfirm.disabled = true;
            if (fileNameDisp) fileNameDisp.textContent = file.name;
            countDisplay.innerHTML = 'Membaca file Excel...';
            
            thead.innerHTML = '<tr><th class="px-4 py-3 text-center text-xs font-semibold text-table-subtle">Memuat Struktur...</th></tr>';
            tbody.innerHTML = '<tr><td class="text-center py-20 text-table-subtle text-xs animate-pulse">Memproses Data Excel...</td></tr>';

            _openModal(modalOverlay);

            try {
                const workbook = new ExcelJS.Workbook();
                await workbook.xlsx.load(await file.arrayBuffer());
                globalWorksheet = workbook.getWorksheet(1);
                
                excelColumns = [{ idxExcel: -1, name: '-- Kosongkan --', sample: '-' }];
                rawDataStore = [];
                
                const headerRow = globalWorksheet.getRow(1);
                headerRow.eachCell({ includeEmpty: false }, (cell, colNumber) => {
                    const colName = cell.text ? cell.text.toString().trim() : `Kolom ${colNumber}`;
                    excelColumns.push({ idxExcel: colNumber, name: colName });
                });

                if (excelColumns.length <= 1) throw new Error("Tidak menemukan header di baris pertama Excel.");

                // Load preview data (up to 100 rows to keep UI snappy)
                globalWorksheet.eachRow((row, rowNumber) => {
                    if (rowNumber === 1) return;
                    if (rowNumber <= 101) {
                        rawDataStore.push(row.values);
                    }
                });

                _autoMapColumns();
                
                const newTbody = _renderTableHeaders(document.getElementById('import-rab-modal-thead'));
                _renderTableBody(newTbody);
                _validateImportState();
                
                const totalRows = Math.max(0, globalWorksheet.rowCount - 1);
                countDisplay.innerHTML = `<span class="text-emerald-600 font-semibold">${totalRows} baris</span> tersedia. Tinjau mapping sebelum menyimpan.`;

            } catch (err) {
                console.error('Gagal membaca Excel: ', err);
                const tbody = document.getElementById('import-rab-modal-tbody');
                if (tbody) tbody.innerHTML = `<tr><td class="text-center py-20"><p class="text-red-500 text-xs font-semibold">Gagal membaca struktur Excel.</p><p class="text-[10px] text-red-400 mt-1">${err && err.message ? err.message : String(err)}</p></td></tr>`;
            }
        });
    }

    if (modalConfirm) {
        modalConfirm.addEventListener('click', () => {
            const finalData = _getFinalParsedData();
            if (finalData.length === 0) { 
                alert('Tidak ada data valid yang bisa diimpor. Pastikan kolom Uraian telah dipetakan dengan benar.'); 
                return; 
            }
            window.dispatchEvent(new CustomEvent('rabDataImported', { detail: finalData }));
            _closeModal(modalOverlay);
            if (fileInput) fileInput.value = '';
        });
    }

    if (modalClose)  modalClose.addEventListener('click',  () => _closeModal(modalOverlay));
    if (modalCancel) modalCancel.addEventListener('click', () => _closeModal(modalOverlay));
}
