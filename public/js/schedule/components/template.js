import { getState } from '../core/state.js';

function getPositionPercentage(dateStr) {
    const { globalStartDate, globalEndDate, totalTimelineDays } = getState();
    if (!dateStr || !globalStartDate) return null;
    const date = new Date(dateStr);
    if (isNaN(date)) return null;
    
    let relativeDate = date < globalStartDate ? globalStartDate : date;
    if (relativeDate > globalEndDate) relativeDate = globalEndDate;

    const offsetMs = relativeDate - globalStartDate;
    const offsetDays = offsetMs / (1000 * 60 * 60 * 24);
    return (offsetDays / totalTimelineDays) * 100;
}

function getWidthPercentage(startStr, finishStr) {
    const { globalStartDate, globalEndDate, totalTimelineDays } = getState();
    if (!startStr || !finishStr || !globalStartDate) return 0;
    const s = new Date(startStr);
    const f = new Date(finishStr);
    if (isNaN(s) || isNaN(f)) return 0;

    let start = s < globalStartDate ? globalStartDate : s;
    let finish = f > globalEndDate ? globalEndDate : f;
    
    if (finish < start) return 0;

    const durationMs = (finish.getTime() - start.getTime());
    const durationDays = (durationMs / (1000 * 60 * 60 * 24)) + 1;
    return (durationDays / totalTimelineDays) * 100;
}

export function renderHeadersTemplate() {
    const { timelineConfig } = getState();
    let monthHeadersHtml = '';
    let weekHeadersHtml = '';

    timelineConfig.forEach(tl => {
        monthHeadersHtml += `<th colspan="${tl.weeks.length}" class="px-4 py-3 md:py-3.5 text-center bg-primary text-[10px] md:text-xs font-semibold tracking-wider text-white">${tl.monthName.toUpperCase()}</th>`;
        tl.weeks.forEach((w) => {
            weekHeadersHtml += `<th class="px-2 py-2 text-center min-w-[50px] bg-slate-100 text-[10px] font-bold">${w}</th>`;
        });
    });

    return `
        <colgroup>
            <col style="width: 60px">
            <col style="width: 300px">
            <col style="width: 120px">
            <col style="width: 120px">
            <col style="width: 80px">
            <col style="width: 90px">
            <col style="width: 80px">
        </colgroup>
        <tr class="bg-primary text-white text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky left-0 z-30 bg-primary w-[60px]">No</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 sticky left-[60px] z-30 bg-primary w-[300px]">Pekerjaan</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky left-[360px] z-30 bg-primary w-[120px]">Start</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky left-[480px] z-30 bg-primary w-[120px]">Finish</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky left-[600px] z-30 bg-primary w-[80px]">Durasi</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky left-[680px] z-30 bg-primary w-[90px]">Bobot (%)</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky left-[770px] z-30 bg-primary w-[80px] shadow-[4px_0_10px_rgba(0,0,0,0.3)]">Detail</th>
            ${monthHeadersHtml}
        </tr>
        <tr class="bg-slate-100 text-slate-800 text-[10px] font-bold uppercase tracking-wider">
            ${weekHeadersHtml}
        </tr>
    `;
}

function renderGanttCellTemplate(startStr, finishStr, barColor = 'bg-emerald-500', isCategory = false) {
    const { totalWeeksCount } = getState();
    const left = getPositionPercentage(startStr);
    const width = getWidthPercentage(startStr, finishStr);
    const opacity = isCategory ? 'opacity-80' : 'opacity-90';
    const height = isCategory ? 'h-5' : 'h-4';
    const rounded = isCategory ? 'rounded' : 'rounded-sm';

    let gridLines = '';
    for (let i = 1; i < totalWeeksCount; i++) {
        const leftPos = (i / totalWeeksCount) * 100;
        gridLines += `<div class="absolute h-full border-l border-slate-200" style="left: ${leftPos}%"></div>`;
    }

    return `
        <td colspan="${totalWeeksCount}" class="p-0 border-b border-l border-slate-200 relative align-middle group/timeline overflow-hidden bg-white/50" style="height: 48px;">
            <!-- Vertical Grid Lines Layer -->
            <div class="absolute inset-0 pointer-events-none">
                ${gridLines}
            </div>
            <!-- Gantt Bar Layer -->
            ${(left !== null && width > 0) ? `
                <div class="absolute top-1/2 -translate-y-1/2 ${height} ${barColor} ${opacity} ${rounded} shadow-sm z-10 transition-all duration-300 hover:opacity-100 hover:scale-[1.02] cursor-pointer" 
                     style="left: ${left}%; width: ${width}%;"
                     title="${startStr} to ${finishStr}">
                </div>
            ` : ''}
        </td>
    `;
}

export function renderCategoryRowTemplate(cat, catIdx, isOpen) {
    const kategoriNum = catIdx + 1;
    const subClass = isOpen ? '' : 'hidden';

    let html = `
        <tr class="schedule-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors group" data-cat="${cat.id}" data-id="${cat.id}" data-type="category" role="button" tabindex="0">
            <td class="px-3 md:px-5 py-2.5 text-center font-semibold sticky left-0 z-20 bg-table-category group-hover:bg-table-category-hover shadow-sm border-y border-table-category-hover">
                <div class="relative flex items-center justify-center w-5 h-5 mx-auto">
                    <svg class="cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? '' : 'hidden'}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg class="cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? 'hidden' : ''}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </td>
            <td class="px-5 py-2.5 font-bold sticky left-[60px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover text-[10px] md:text-xs uppercase tracking-widest">
                <span class="flex items-center gap-2">
                    <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                    ${cat.kategori}
                </span>
            </td>
            <td class="px-4 py-2.5 text-center font-medium sticky left-[360px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover" data-action="prevent-collapse">
                <input type="date" value="${cat.start_date || ''}" data-field="start_date" class="schedule-input w-full max-w-[110px] bg-white border border-slate-300 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-800 font-bold">
            </td>
            <td class="px-4 py-2.5 text-center font-medium sticky left-[480px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover" data-action="prevent-collapse">
                <input type="date" value="${cat.finish_date || ''}" data-field="finish_date" class="schedule-input w-full max-w-[110px] bg-white border border-slate-300 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-800 font-bold">
            </td>
            <td class="px-4 py-2.5 text-center font-bold sticky left-[600px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover">
                ${cat.duration || 0} Hari
            </td>
            <td class="px-4 py-2.5 text-center font-bold sticky left-[680px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover">
                ${(cat.weight || 0).toFixed(1)}%
            </td>
            <td class="px-4 py-2.5 text-center sticky left-[770px] z-20 bg-table-category shadow-[4px_0_8px_rgba(0,0,0,0.05)] group-hover:bg-table-category-hover border-y border-table-category-hover" data-action="prevent-collapse">
                <button class="px-2 py-1 bg-white/20 text-white rounded text-[11px] font-medium hover:bg-white/30 transition-colors shadow-sm border border-white/20">Detail</button>
            </td>
            ${renderGanttCellTemplate(cat.start_date, cat.finish_date, 'bg-slate-500', true)}
        </tr>
    `;

    cat.items.forEach((item, itemIdx) => {
        html += renderItemRowTemplate(item, itemIdx, kategoriNum, cat.id, subClass);
    });

    return html;
}

function renderItemRowTemplate(item, itemIdx, kategoriNum, catId, subClass) {
    const itemNum = `${kategoriNum}.${itemIdx + 1}`;
    
    // Check if item should be hidden due to search filter
    const { filteredIds } = getState();
    const isFilteredOut = filteredIds !== null && !filteredIds.includes(item.id);
    const rowClass = isFilteredOut ? 'hidden' : subClass;

    return `
        <tr class="subrow-${catId} ${rowClass} bg-white hover:bg-slate-50 transition-colors group border-b border-table-border" data-id="${item.id}" data-cat-id="${catId}" data-type="item">
            <td class="px-4 py-3 text-center text-slate-600 sticky left-0 z-20 bg-inherit border-b border-table-border">
                ${itemNum}
            </td>
            <td class="px-5 py-3 pl-8 text-[12px] sticky left-[60px] z-20 bg-inherit capitalize text-slate-800 border-b border-table-border ">
                ${item.nama}
            </td>
            <td class="px-4 py-3 text-center sticky left-[360px] z-20 bg-inherit border-b border-table-border">
                <input type="date" value="${item.start_date || ''}" data-field="start_date" class="schedule-input w-full max-w-[110px] bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-700 hover:border-slate-300 transition-colors focus:bg-white">
            </td>
            <td class="px-4 py-3 text-center sticky left-[480px] z-20 bg-inherit border-b border-table-border">
                <input type="date" value="${item.finish_date || ''}" data-field="finish_date" class="schedule-input w-full max-w-[110px] bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-700 hover:border-slate-300 transition-colors focus:bg-white">
            </td>
            <td class="px-4 py-3 text-center sticky left-[600px] z-20 bg-inherit text-slate-700 font-medium font-mono text-[12px] border-b border-table-border">
                ${item.duration || 0} Hari
            </td>
            <td class="px-4 py-3 text-center sticky left-[680px] z-20 bg-inherit border-b border-table-border">
                ${(item.weight || 0).toFixed(1)}%
            </td>
            <td class="px-4 py-3 text-center sticky left-[770px] z-20 bg-inherit shadow-[4px_0_8px_rgba(0,0,0,0.05)] border-b border-table-border">
                <button class="px-2 py-1 bg-slate-100 text-slate-700 border border-slate-300 rounded text-[11px] font-medium hover:bg-slate-200 transition-colors">Detail</button>
            </td>
            ${renderGanttCellTemplate(item.start_date, item.finish_date, 'bg-emerald-500', false)}
        </tr>
    `;
}
