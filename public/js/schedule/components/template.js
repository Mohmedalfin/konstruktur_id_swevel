import { getState } from '../core/state.js';

function getWeekCoordinate(date, isEnd = false) {
    const { timelineConfig, globalStartDate } = getState();
    if (date < globalStartDate) return 0;
    
    let currentWeekIndex = 0;
    for (let i = 0; i < timelineConfig.length; i++) {
        const monthConfig = timelineConfig[i];
        for (let w = 0; w < monthConfig.weeks.length; w++) {
            const wStartDay = w * 7 + 1;
            const lastDay = new Date(monthConfig.year, monthConfig.month + 1, 0).getDate();
            const isLastWeek = (w === monthConfig.weeks.length - 1);
            const wEndDay = isLastWeek ? lastDay : wStartDay + 6;
            
            const weekStart = new Date(monthConfig.year, monthConfig.month, wStartDay);
            const weekEnd = new Date(monthConfig.year, monthConfig.month, wEndDay, 23, 59, 59);
            
            if (date >= weekStart && date <= weekEnd) {
                const daysInWeek = wEndDay - wStartDay + 1;
                let diffDays = (date - weekStart) / (1000 * 60 * 60 * 24);
                
                if (isEnd) {
                    diffDays += 1;
                }
                
                return currentWeekIndex + (diffDays / daysInWeek);
            }
            currentWeekIndex++;
        }
    }
    return currentWeekIndex; 
}

function getPositionPercentage(dateStr) {
    const { totalWeeksCount } = getState();
    if (!dateStr || !totalWeeksCount) return null;
    const date = new Date(dateStr);
    if (isNaN(date)) return null;
    
    const coord = getWeekCoordinate(date, false);
    return (coord / totalWeeksCount) * 100;
}

function getWidthPercentage(startStr, finishStr) {
    const { totalWeeksCount } = getState();
    if (!startStr || !finishStr || !totalWeeksCount) return 0;
    
    const s = new Date(startStr);
    const f = new Date(finishStr);
    if (isNaN(s) || isNaN(f) || f < s) return 0;
    
    const startCoord = getWeekCoordinate(s, false);
    const endCoord = getWeekCoordinate(f, true);
    
    return ((endCoord - startCoord) / totalWeeksCount) * 100;
}

export function renderHeadersTemplate(planData = null) {
    const { timelineConfig, viewMode } = getState();
    const isSCurve = viewMode === 's-curve';
    const hideClass = isSCurve ? 'hidden' : '';
    const bobotLeft = isSCurve ? 'md:left-[320px]' : 'md:left-[720px]';

    let monthHeadersHtml    = '';
    let weekHeadersHtml     = '';
    let bobotPerMingguHtml  = '';
    let kumulatifHtml       = '';
    let weekGlobalIdx       = 0;

    timelineConfig.forEach(tl => {
        monthHeadersHtml += `<th colspan="${tl.weeks.length}" class="px-4 py-3 md:py-3.5 text-center bg-primary text-[10px] md:text-xs font-semibold tracking-wider text-white sticky top-0 z-30 shadow-sm">${tl.monthName.toUpperCase()}</th>`;

        tl.weeks.forEach(w => {
            weekHeadersHtml += `<th class="px-2 py-2 text-center min-w-[50px] bg-slate-100 text-[10px] font-bold sticky top-[40px] md:top-[44px] z-30 shadow-sm border-b border-slate-200">${w}</th>`;

            if (planData) {
                const wVal = planData.weeklyWeights[weekGlobalIdx] ?? 0;
                const cVal = planData.cumulativeWeights[weekGlobalIdx] ?? 0;
                bobotPerMingguHtml += `<th class="px-1 py-1.5 text-center min-w-[50px] bg-blue-50 text-[9px] md:text-[10px] font-bold text-blue-700 border-b border-blue-100">${wVal > 0 ? wVal.toFixed(2) + '%' : '-'}</th>`;
                kumulatifHtml      += `<th class="px-1 py-1.5 text-center min-w-[50px] bg-slate-100 text-[9px] md:text-[10px] font-bold text-slate-700 border-b border-slate-300">${cVal > 0 ? cVal.toFixed(2) + '%' : '-'}</th>`;
            }

            weekGlobalIdx++;
        });
    });

    return `
        <colgroup>
            <col style="width: 60px">
            <col style="width: 320px">
            ${isSCurve ? '' : `
            <col style="width: 150px">
            <col style="width: 150px">
            <col style="width: 100px">
            `}
            <col style="width: 100px">
        </colgroup>
        <tr class="bg-primary text-white text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center bg-primary w-[60px] sticky top-0 z-30 shadow-sm">No</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 sticky md:left-0 top-0 z-40 bg-primary w-[320px] shadow-sm">Pekerjaan</th>
            <th rowspan="2" class="${hideClass} px-3 md:px-5 py-3 md:py-3.5 text-center sticky md:left-[320px] top-0 z-40 bg-primary w-[150px] shadow-sm">Start</th>
            <th rowspan="2" class="${hideClass} px-3 md:px-5 py-3 md:py-3.5 text-center sticky md:left-[470px] top-0 z-40 bg-primary w-[150px] shadow-sm">Finish</th>
            <th rowspan="2" class="${hideClass} px-3 md:px-5 py-3 md:py-3.5 text-center sticky md:left-[620px] top-0 z-40 bg-primary w-[100px] shadow-sm">Durasi</th>
            <th rowspan="2" class="px-3 md:px-5 py-3 md:py-3.5 text-center sticky ${bobotLeft} top-0 z-40 bg-primary w-[100px] shadow-sm">Bobot (%)</th>
            ${monthHeadersHtml}
        </tr>
        <tr class="bg-slate-100 text-slate-800 text-[10px] font-bold uppercase tracking-wider">
            ${weekHeadersHtml}
        </tr>
    `;
}

export function renderRekapFooterTemplate(planData) {
    if (!planData) return '';

    const { timelineConfig } = getState();
    let bobotPerMingguHtml = '';
    let kumulatifHtml      = '';
    let weekGlobalIdx      = 0;

    timelineConfig.forEach(tl => {
        tl.weeks.forEach(() => {
            const wVal = planData.weeklyWeights[weekGlobalIdx] ?? 0;
            const cVal = planData.cumulativeWeights[weekGlobalIdx] ?? 0;
            bobotPerMingguHtml += `<td class="px-1 py-2 text-center min-w-[50px] bg-blue-50 text-[9px] md:text-[10px] font-bold text-blue-700 border-t-2 border-blue-200">${wVal > 0 ? wVal.toFixed(2) + '%' : '-'}</td>`;
            kumulatifHtml      += `<td class="px-1 py-2 text-center min-w-[50px] bg-slate-50 text-[9px] md:text-[10px] font-bold text-slate-700 border-t border-slate-200">${cVal > 0 ? cVal.toFixed(2) + '%' : '-'}</td>`;
            weekGlobalIdx++;
        });
    });

    return `
        <tr class="bg-blue-50">
            <td colspan="3" class="px-4 py-2 text-right bg-blue-50 text-[9px] md:text-[10px] font-bold text-blue-700 uppercase tracking-wide border-t-2 border-blue-200 md:sticky md:left-0 z-20 whitespace-nowrap">Bobot Rencana (%)</td>
            ${bobotPerMingguHtml}
        </tr>
        <tr class="bg-slate-50">
            <td colspan="3" class="px-4 py-2 text-right bg-slate-50 text-[9px] md:text-[10px] font-bold text-slate-700 uppercase tracking-wide border-t border-slate-200 md:sticky md:left-0 z-20 whitespace-nowrap">Kumulatif Rencana (%)</td>
            ${kumulatifHtml}
        </tr>
    `;
}

function computeWeeklyWeights(startStr, finishStr, weight) {
    if (!startStr || !finishStr || weight <= 0) return [];

    const { globalStartDate, totalWeeksCount } = getState();
    if (!globalStartDate || !totalWeeksCount) return [];

    const itemStart  = new Date(startStr);
    const itemFinish = new Date(finishStr);
    if (isNaN(itemStart) || isNaN(itemFinish) || itemFinish < itemStart) return [];

    const totalDays    = Math.ceil((itemFinish - itemStart) / 86400000) + 1;
    const weightPerDay = weight / totalDays;

    const result    = [];
    let   dayOffset = 0;  

    while (dayOffset < totalDays) {
        const chunkDays   = Math.min(7, totalDays - dayOffset);
        const chunkWeight = chunkDays * weightPerDay;
        const chunkMidDay = dayOffset + (chunkDays - 1) / 2;
        
        const chunkMidDate = new Date(itemStart.getTime() + chunkMidDay * 86400000);
        const centerCoord = getWeekCoordinate(chunkMidDate, false);
        const centerPct = Math.max(0, Math.min(100, (centerCoord / totalWeeksCount) * 100));

        result.push({ centerPct, weight: chunkWeight });
        dayOffset += chunkDays;
    }

    return result;
}

function renderGanttCellTemplate(startStr, finishStr, barColor = 'bg-emerald-500', isCategory = false, weight = 0) {
    const { totalWeeksCount } = getState();
    const left    = getPositionPercentage(startStr);
    const width   = getWidthPercentage(startStr, finishStr);
    const opacity = isCategory ? 'opacity-80' : 'opacity-90';
    const height  = isCategory ? 'h-5' : 'h-5';   
    const rounded = isCategory ? 'rounded' : 'rounded-sm';

    const colWidth = (1 / totalWeeksCount) * 100;
    const gridBg   = `repeating-linear-gradient(to right, transparent, transparent calc(${colWidth}% - 1px), #e2e8f0 calc(${colWidth}% - 1px), #e2e8f0 ${colWidth}%)`;

    let weightLabelsHtml = '';
    if (!isCategory && weight > 0 && left !== null && width > 0) {
        const weeklyWeights = computeWeeklyWeights(startStr, finishStr, weight);
        weeklyWeights.forEach(({ centerPct, weight: wt }) => {
            weightLabelsHtml += `
                <div class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 z-10 pointer-events-none"
                     style="left: ${centerPct.toFixed(3)}%;">
                    <span class="text-[8px] md:text-[9px] font-bold text-white drop-shadow whitespace-nowrap">${wt.toFixed(2)}%</span>
                </div>`;
        });
    }

    return `
        <td colspan="${totalWeeksCount}" class="p-0 border-b border-l border-slate-200 relative align-middle overflow-hidden" style="height: 48px; background: ${gridBg};">
            ${(left !== null && width > 0) ? `
                <div class="absolute top-1/2 -translate-y-1/2 ${height} ${barColor} ${opacity} ${rounded} shadow-sm z-10 transition-all duration-300 hover:opacity-100 cursor-pointer"
                     style="left: ${left}%; width: ${width}%;"
                     title="${startStr} → ${finishStr} | Bobot: ${weight.toFixed(2)}%">
                </div>
                ${weightLabelsHtml}
            ` : ''}
        </td>
    `;
}

export function renderCategoryRowTemplate(cat, catIdx, isOpen) {
    const { viewMode } = getState();
    const isSCurve = viewMode === 's-curve';
    const hideClass = isSCurve ? 'hidden' : '';
    const bobotLeft = isSCurve ? 'md:left-[320px]' : 'md:left-[720px]';

    const kategoriNum = catIdx + 1;
    const subClass = isOpen ? '' : 'hidden';

    let html = `
        <tr class="schedule-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors group" data-category="${cat.kategori}" data-cat="${cat.id}" data-id="${cat.id}" data-type="category" role="button" tabindex="0">
            <td class="px-3 md:px-5 py-2.5 text-center font-semibold bg-table-category group-hover:bg-table-category-hover shadow-sm border-y border-table-category-hover z-10 relative">
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
            <td class="px-5 py-2.5 font-bold md:sticky md:left-0 z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover text-[10px] md:text-xs uppercase tracking-widest">
                <span class="flex items-center gap-2">
                    <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                    ${cat.kategori}
                </span>
            </td>
            <td class="${hideClass} px-4 py-2.5 text-center font-medium md:sticky md:left-[320px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover" data-action="prevent-collapse">
                <input type="date" value="${cat.start_date || ''}" data-field="start_date" class="schedule-input w-full max-w-[110px] bg-white border border-slate-300 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-800 font-bold">
            </td>
            <td class="${hideClass} px-4 py-2.5 text-center font-medium md:sticky md:left-[470px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover" data-action="prevent-collapse">
                <input type="date" value="${cat.finish_date || ''}" data-field="finish_date" class="schedule-input w-full max-w-[110px] bg-white border border-slate-300 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-800 font-bold">
            </td>
            <td class="${hideClass} px-4 py-2.5 text-center font-bold md:sticky md:left-[620px] z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover">
                ${cat.duration || 0} Hari
            </td>
            <td class="px-4 py-2.5 text-center font-bold md:sticky ${bobotLeft} z-20 bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover">
                ${(cat.weight || 0).toFixed(2)}%
            </td>
            ${renderGanttCellTemplate(cat.start_date, cat.finish_date, 'bg-white', true, cat.weight)}
        </tr>
    `;

    cat.items.forEach((item, itemIdx) => {
        html += renderItemRowTemplate(item, itemIdx, "", cat.id, subClass, 0, cat.kategori);
    });

    return html;
}

function renderItemRowTemplate(item, itemIdx, prefix, catId, subClass, depth = 0, categoryName = "") {
    const { viewMode } = getState();
    const isSCurve = viewMode === 's-curve';
    const hideClass = isSCurve ? 'hidden' : '';
    const bobotLeft = isSCurve ? 'md:left-[320px]' : 'md:left-[720px]';

    const itemNum = item.displayNumber || (prefix ? `${prefix}${itemIdx + 1}` : `${itemIdx + 1}`);
    
    const rowClass = subClass;

    const noPadding = `padding-left: ${(depth * 0.5) + 1.5}rem`;
    const textPadding = `padding-left: ${depth * 1.5}rem`;
    
    const textStyle = depth > 0 ? "text-[12px] text-slate-700" : "text-[12px] text-slate-800 font-medium";

    const hasChildren = item.children && item.children.length > 0;

    let html = `
        <tr class="subrow-${catId} ${rowClass} bg-white hover:bg-slate-50 transition-colors duration-200 group border-b border-table-border" data-id="${item.id}" data-category="${categoryName}" data-cat-id="${catId}" data-type="item">
            <td class="px-1 md:px-2 py-3 text-left text-slate-600 bg-white group-hover:bg-slate-50 border-b border-slate-100 whitespace-nowrap z-10 relative">
                <div style="${noPadding}">
                    <span class="tabular-nums">${itemNum}</span>
                </div>
            </td>
            <td class="px-5 py-3 md:sticky md:left-0 z-20 bg-white group-hover:bg-slate-50 capitalize ${textStyle} border-b border-slate-100">
                <div class="flex items-start gap-2" style="${textPadding}">
                    ${depth > 0 ? `<span class="text-slate-300">└─</span>` : ''}
                    <span>${item.nama}</span>
                </div>
            </td>
            <td class="${hideClass} px-4 py-3 text-center md:sticky md:left-[320px] z-20 bg-white group-hover:bg-slate-50 border-b border-slate-100" data-action="prevent-collapse">
                ${hasChildren ? '' : `<input type="date" value="${item.start_date || ''}" data-field="start_date" class="schedule-input w-full max-w-[110px] bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-700 hover:border-slate-300 transition-colors focus:bg-white">`}
            </td>
            <td class="${hideClass} px-4 py-3 text-center md:sticky md:left-[470px] z-20 bg-white group-hover:bg-slate-50 border-b border-slate-100" data-action="prevent-collapse">
                ${hasChildren ? '' : `<input type="date" value="${item.finish_date || ''}" data-field="finish_date" class="schedule-input w-full max-w-[110px] bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5 text-[12px] text-center focus:ring-primary focus:border-primary cursor-pointer text-slate-700 hover:border-slate-300 transition-colors focus:bg-white">`}
            </td>
            <td class="${hideClass} px-4 py-3 text-center md:sticky md:left-[620px] z-20 bg-white group-hover:bg-slate-50 text-slate-700 font-medium font-mono text-[12px] border-b border-slate-100">
                ${hasChildren ? '' : `${item.duration || 0} Hari`}
            </td>
            <td class="px-4 py-3 text-center md:sticky ${bobotLeft} z-20 bg-white group-hover:bg-slate-50 border-b border-slate-100 shadow-[4px_0_10px_-4px_rgba(0,0,0,0.1)]">
                ${hasChildren ? '' : `${(item.weight || 0).toFixed(2)}%`}
            </td>
            
            ${renderGanttCellTemplate(hasChildren ? null : item.start_date, hasChildren ? null : item.finish_date, depth > 0 ? 'bg-sky-400' : 'bg-emerald-500', false, hasChildren ? 0 : item.weight)}
        </tr>
    `;

    if (item.children && item.children.length > 0) {
        item.children.forEach((childItem, childIdx) => {
            html += renderItemRowTemplate(childItem, childIdx, `${itemNum}.`, catId, subClass, depth + 1, categoryName);
        });
    }

    return html;
}

export function renderSCurveCategoryRow(cat, catIdx) {
    return `
        <tr class="bg-table-category text-white hover:bg-table-category-hover transition-colors group">
            <td class="px-3 md:px-5 py-2.5 text-center font-semibold bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover text-xs">${catIdx + 1}</td>
            <td class="px-5 py-2.5 font-bold bg-table-category group-hover:bg-table-category-hover border-y border-table-category-hover text-[10px] md:text-xs uppercase tracking-widest" colspan="2">
                <span class="flex items-center gap-2">
                    <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                    ${cat.kategori}
                </span>
            </td>
        </tr>
    `;
}

export function renderSCurveItemRow(item, itemIdx, prefix, depth = 0) {
    const itemNum = item.displayNumber || (prefix ? `${prefix}${itemIdx + 1}` : `${itemIdx + 1}`);
    const textPadding = `padding-left: ${(depth * 1.5)}rem`;
    const noPadding = `padding-left: ${(depth * 0.5) + 1.5}rem`;
    const textStyle = depth > 0 ? "text-[12px] text-slate-700" : "text-[12px] text-slate-800 font-medium";
    
    const hasChildren = item.children && item.children.length > 0;

    let html = `
        <tr class="bg-white hover:bg-slate-50 border-b border-table-border transition-colors group">
            <td class="px-1 md:px-2 py-3 text-left text-slate-600 bg-white group-hover:bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                <div style="${noPadding}"><span class="tabular-nums text-xs">${itemNum}</span></div>
            </td>
            <td class="px-5 py-3 ${textStyle} capitalize bg-white group-hover:bg-slate-50 border-b border-slate-100">
                <div class="flex items-start gap-2" style="${textPadding}">
                    ${depth > 0 ? `<span class="text-slate-300">└─</span>` : ''}
                    <span>${item.nama}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-center md:px-5 font-medium text-slate-700 bg-white group-hover:bg-slate-50 border-b border-slate-100 text-xs">${hasChildren ? '' : `${(item.weight || 0).toFixed(2)}%`}</td>
        </tr>
    `;

    if (item.children && item.children.length > 0) {
        item.children.forEach((childItem, childIdx) => {
            html += renderSCurveItemRow(childItem, childIdx, `${itemNum}.`, depth + 1);
        });
    }

    return html;
}
