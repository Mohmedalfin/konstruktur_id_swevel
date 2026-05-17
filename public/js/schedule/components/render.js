import { getState } from '../core/state.js';
import { renderHeadersTemplate, renderRekapFooterTemplate, renderCategoryRowTemplate, renderSCurveCategoryRow, renderSCurveItemRow } from './template.js';
import { refreshCategoryMetadata } from '../core/data.js';
import { bindCategoryToggle, bindInputChanges } from '../index.js';
import { renderSCurveChart } from './chart-render.js';
import { computeWeeklyPlanWeights } from '../core/s-curve-logic.js';

export function renderHeaders() {
    const thead = document.getElementById('schedule-thead');
    if (!thead) return;

    const { schedules, timelineConfig, globalStartDate, globalEndDate, viewMode } = getState();

    const planData = viewMode === 's-curve' && schedules?.length
        ? computeWeeklyPlanWeights(schedules, timelineConfig, globalStartDate, globalEndDate)
        : null;

    thead.innerHTML = renderHeadersTemplate(planData);

    const tfoot = document.getElementById('schedule-tfoot');
    if (tfoot) {
        tfoot.innerHTML = renderRekapFooterTemplate(planData);
    }
}

export function renderBody(changedId = null) {
    const tbody = document.getElementById('schedule-tbody');
    if (!tbody) return;

    refreshCategoryMetadata(changedId);
    
    const { schedules, filteredSchedules, collapsedCategories, viewMode } = getState();
    const activeSchedules = filteredSchedules || schedules;

    renderHeaders();
    let html = '';
    activeSchedules.forEach((cat, catIdx) => {
        const isOpen = !collapsedCategories[cat.id];
        html += renderCategoryRowTemplate(cat, catIdx, isOpen);
    });
    tbody.innerHTML = html || `<tr><td colspan="100%" class="text-center py-4 text-slate-500">Tidak ada data jadwal.</td></tr>`;
    
    bindCategoryToggle();
    bindInputChanges();

    if (viewMode === 's-curve') {
        renderSCurveChart();
    }
}
