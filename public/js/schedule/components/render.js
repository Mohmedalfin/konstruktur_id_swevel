import { getState } from '../core/state.js';
import { renderHeadersTemplate, renderCategoryRowTemplate } from './template.js';
import { refreshCategoryMetadata } from '../core/data.js';
import { bindCategoryToggle, bindInputChanges } from '../index.js';

export function renderHeaders() {
    const thead = document.getElementById('schedule-thead');
    if (!thead) return;
    thead.innerHTML = renderHeadersTemplate();
}

export function renderBody(changedId = null) {
    const tbody = document.getElementById('schedule-tbody');
    if (!tbody) return;

    // Recalculate
    refreshCategoryMetadata(changedId);

    const { schedules, collapsedCategories } = getState();
    let html = '';

    schedules.forEach((cat, catIdx) => {
        const isOpen = !collapsedCategories[cat.id];
        html += renderCategoryRowTemplate(cat, catIdx, isOpen);
    });

    tbody.innerHTML = html || `<tr><td colspan="100%" class="text-center py-4 text-slate-500">Tidak ada data jadwal.</td></tr>`;
    
    bindCategoryToggle();
    bindInputChanges();
}

export function renderLoading() {
    const tbody = document.getElementById('schedule-tbody');
    if (!tbody) return;
    tbody.innerHTML = `
        <tr>
            <td colspan="100%" class="text-center py-8 text-slate-500 text-sm">
                <div class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat Schedule...
                </div>
            </td>
        </tr>
    `;
}
