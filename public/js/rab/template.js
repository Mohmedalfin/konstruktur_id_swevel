/**
 * rab/template.js
 * Excel template download handler for the BOQ template button.
 */

import { boqDownloadTplBtn } from './state.js';

export function initTemplate() {
    if (!boqDownloadTplBtn) return;

    boqDownloadTplBtn.addEventListener('click', async function () {
        if (typeof ExcelJS === 'undefined') {
            alert('Library ExcelJS belum dimuat. Coba muat ulang halaman.');
            return;
        }

        const workbook  = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Template BOQ');

        worksheet.columns = [
            { header: 'No',               key: 'no',     width: 6  },
            { header: 'Uraian Pekerjaan', key: 'uraian', width: 50 },
            { header: 'Volume',           key: 'volume', width: 12 },
            { header: 'Satuan',           key: 'satuan', width: 12 },
        ];

        const headerRow = worksheet.getRow(1);
        headerRow.eachCell(cell => {
            cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
            cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1E3A5F' } };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
            cell.border    = {
                top: { style: 'thin' }, left: { style: 'thin' },
                bottom: { style: 'thin' }, right: { style: 'thin' }
            };
        });
        headerRow.height = 28;

        const buffer = await workbook.xlsx.writeBuffer();
        const blob   = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url    = URL.createObjectURL(blob);
        const a      = document.createElement('a');
        a.href       = url;
        a.download   = 'template_boq.xlsx';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
}
