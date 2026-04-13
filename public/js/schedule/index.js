import { fetchScheduleData } from './core/data.js';
import { renderHeaders, renderBody, renderLoading } from './components/render.js';
import { getState, toggleCategoryCollapse } from './core/state.js';
import { bindSearch } from './hooks/search.js';

document.addEventListener('DOMContentLoaded', async () => {
    
    renderLoading();

    await fetchScheduleData();

    renderHeaders();
    renderBody();

    bindSearch();

});


export function bindCategoryToggle() {
    const tbody = document.getElementById('schedule-tbody');
    if (!tbody) return;

    tbody.querySelectorAll('.schedule-category[data-cat]').forEach(row => {
        const newRow = row.cloneNode(true);
        row.parentNode.replaceChild(newRow, row);

        newRow.addEventListener('click', function (e) {
            if (e.target.closest('[data-action="prevent-collapse"]') || e.target.closest('input') || e.target.closest('button')) {
                return;
            }

            const catId = newRow.dataset.cat;
            toggleCategoryCollapse(catId);
            renderBody(); 
        });
    });
}

export function bindInputChanges() {
    const tbody = document.getElementById('schedule-tbody');
    if (!tbody) return;

    const inputs = tbody.querySelectorAll('.schedule-input');
    inputs.forEach(input => {
        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);

        newInput.addEventListener('change', handleDateChange);
        
        newInput.addEventListener('click', (e) => e.stopPropagation());
    });
}

function handleDateChange(e) {
    const { schedules } = getState();
    const input = e.target;
    const tr = input.closest('tr');
    if (!tr) return;

    const id = tr.dataset.id;
    const type = tr.dataset.type;
    const field = input.dataset.field;
    const newVal = input.value;

    if (type === 'category') {
        const cat = schedules.find(c => c.id === id);
        if (cat) cat[field] = newVal;
    } else {
        const catId = tr.dataset.catId;
        const cat = schedules.find(c => c.id === catId);
        if (cat) {
            const task = cat.items.find(i => i.id === id);
            if (task) task[field] = newVal;
        }
    }
    
    renderBody(id);
}
