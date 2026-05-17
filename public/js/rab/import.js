/**
 * rab/import.js
 * Handles reading a BOQ Excel file via ExcelJS, rendering the preview modal,
 * and dispatching the 'rabDataImported' custom event when confirmed.
 * (Formerly ajax_import_rab.js)
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

function openModal(modalOverlay) {
    if (modalOverlay) {
        modalOverlay.classList.remove('hidden');
        modalOverlay.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalOverlay) {
    if (modalOverlay) {
        modalOverlay.classList.add('hidden');
        modalOverlay.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

function renderPreview(dataArray, tbody, countDisplay) {
    if (!dataArray || dataArray.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-table-subtle text-xs italic">Tidak ada data ditemukan dalam file.</td></tr>`;
        countDisplay.textContent = '0 baris terdeteksi';
        return;
    }

    let html = '';
    dataArray.forEach((item, index) => {
        const isHeader  = item.type === 'header';
        const bgClass   = isHeader ? 'bg-slate-50' : 'bg-white';
        const textClass = isHeader ? 'font-bold text-table-strong' : 'font-medium text-table-strong';

        const categorySelect = `
            <select class="category-select w-full border border-table-border rounded bg-white text-xs px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all" data-index="${index}">
                <option value="persiapan"  ${item.kategori === 'persiapan'  ? 'selected' : ''}>Pekerjaan Persiapan</option>
                <option value="tanah"      ${item.kategori === 'tanah'      ? 'selected' : ''}>Pekerjaan Tanah</option>
                <option value="struktur"   ${item.kategori === 'struktur'   ? 'selected' : ''}>Pekerjaan Struktur</option>
                <option value="arsitektur" ${item.kategori === 'arsitektur' ? 'selected' : ''}>Pekerjaan Arsitektur</option>
                <option value="mep"        ${item.kategori === 'mep'        ? 'selected' : ''}>Pekerjaan MEP</option>
                <option value="finishing"  ${item.kategori === 'finishing'  ? 'selected' : ''}>Pekerjaan Finishing</option>
            </select>`;

        html += `
            <tr class="border-b border-table-border ${bgClass} hover:bg-slate-50 transition-colors">
                <td class="px-4 py-2 text-center text-table-subtle font-medium tabular-nums">${index + 1}</td>
                <td class="px-4 py-2 ${textClass}">${item.uraian}</td>
                <td class="px-4 py-2 text-center tabular-nums">${isHeader ? '' : item.volume}</td>
                <td class="px-4 py-2 text-center text-table-subtle">${isHeader ? '' : item.satuan}</td>
                <td class="px-4 py-2 text-center">${isHeader ? '' : categorySelect}</td>
            </tr>`;
    });

    tbody.innerHTML = html;
    countDisplay.textContent = `${dataArray.length} baris siap diimpor`;

    tbody.querySelectorAll('.category-select').forEach(select => {
        select.addEventListener('change', (e) => {
            const idx = e.target.dataset.index;
            if (dataArray[idx]) dataArray[idx].kategori = e.target.value;
        });
    });
}

export function initImport() {
    const importBtn     = document.getElementById('boq-import-btn');
    const fileInput     = document.getElementById('boq-file-input');
    const modalOverlay  = document.getElementById('import-rab-modal-overlay');
    const modalClose    = document.getElementById('import-rab-modal-close');
    const modalCancel   = document.getElementById('import-rab-modal-cancel');
    const modalConfirm  = document.getElementById('import-rab-modal-confirm');
    const tbody         = document.getElementById('import-rab-modal-tbody');
    const countDisplay  = document.getElementById('import-rab-modal-count');

    let parsedData = [];

    // 1. Trigger hidden file input
    if (importBtn && fileInput) {
        importBtn.addEventListener('click', () => {
            fileInput.value = '';
            fileInput.click();
        });
    }

    // 2. Handle file selection → read with ExcelJS
    if (fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            openModal(modalOverlay);
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-table-subtle text-xs italic">Membaca file Excel...</td></tr>`;
            countDisplay.textContent = 'Memproses...';

            try {
                const workbook    = new ExcelJS.Workbook();
                const arrayBuffer = await file.arrayBuffer();
                await workbook.xlsx.load(arrayBuffer);

                const worksheet = workbook.getWorksheet(1);
                parsedData = [];
                let rowCount = 0;

                worksheet.eachRow((row, rowNumber) => {
                    if (rowNumber === 1) return; // skip header

                    const vals     = row.values;
                    const uraianTxt = (vals[2] || '').toString().trim();
                    if (!uraianTxt) return;

                    parsedData.push({
                        id:          'import-' + Date.now() + '-' + rowCount,
                        uraian:      uraianTxt,
                        volume:      parseNumber(vals[3]),
                        satuan:      (vals[4] || '').toString().trim() || '-',
                        harga_bahan: parseNumber(vals[5]),
                        harga_alat:  parseNumber(vals[6]),
                        harga_upah:  parseNumber(vals[7]),
                        type:        parseNumber(vals[3]) > 0 ? 'item' : 'header',
                        kategori:    'persiapan'
                    });
                    rowCount++;
                });

                renderPreview(parsedData, tbody, countDisplay);

            } catch (err) {
                console.error('Gagal membaca Excel: ', err);
                const errMsg = err && err.message ? err.message : String(err);
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-red-500 text-xs italic font-semibold">Gagal membaca file Excel.<br/><span class="font-normal text-[10px] text-red-400 mt-2 block">${errMsg}</span></td></tr>`;
                countDisplay.textContent = '0 baris terdeteksi';
            }
        });
    }

    // 3. Konfirmasi → dispatch rabDataImported
    if (modalConfirm) {
        modalConfirm.addEventListener('click', () => {
            if (parsedData.length === 0) {
                alert('Tidak ada data untuk diimpor.');
                return;
            }
            window.dispatchEvent(new CustomEvent('rabDataImported', { detail: parsedData }));
            closeModal(modalOverlay);
            if (fileInput) fileInput.value = '';
        });
    }

    if (modalClose)  modalClose.addEventListener('click',  () => closeModal(modalOverlay));
    if (modalCancel) modalCancel.addEventListener('click', () => closeModal(modalOverlay));
}
