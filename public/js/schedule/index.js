import { fetchScheduleData, initTimelineConfig } from './core/data.js';
import { renderHeaders, renderBody } from './components/render.js';
import { getState, toggleCategoryCollapse } from './core/state.js';
import { bindSearch } from './hooks/search.js';
import { initFilterControls } from './hooks/view-selectors.js';

const yieldToMain = () => new Promise(resolve => setTimeout(resolve, 0));

document.addEventListener('DOMContentLoaded', async () => {
    try {
        if (window.showLoader) window.showLoader();
        
        initTimelineConfig();
        await yieldToMain();
        renderHeaders();

        await fetchScheduleData();
        await yieldToMain();

        renderBody();

        bindSearch();
        initFilterControls();
    } catch (error) {
        console.error('Error during schedule initialization:', error);
    } finally {
        if (window.hideLoader) window.hideLoader();
    }
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

async function handleDateChange(e) {
    const { schedules } = getState();
    const input = e.target;
    const tr = input.closest('tr');
    if (!tr) return;

    const id = tr.dataset.id;
    const type = tr.dataset.type;
    const field = input.dataset.field;
    const newVal = input.value;

    function findItemRecursively(items, targetId) {
        for (const item of items) {
            if (String(item.id) === String(targetId)) return item;
            if (item.children && item.children.length > 0) {
                const found = findItemRecursively(item.children, targetId);
                if (found) return found;
            }
        }
        return null;
    }

    let itemToUpdate = null;

    if (type === 'category') {
        const cat = schedules.find(c => String(c.id) === String(id));
        if (cat) cat[field] = newVal;
    } else {
        const catId = tr.dataset.catId;
        const cat = schedules.find(c => String(c.id) === String(catId));
        if (cat) {
            itemToUpdate = findItemRecursively(cat.items, id);
            if (itemToUpdate) itemToUpdate[field] = newVal;
        }
    }
    
    renderBody(id);

    if (type === 'item' && itemToUpdate && window.SCHEDULE_INIT) {
        try {
            const updateUrl = window.SCHEDULE_INIT.apiScheduleDataUrl.replace('/schedule/data', '/rap/schedule-dates');
            const response = await fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_rap_detail: id,
                    [field]: newVal
                })
            });
            const result = await response.json();
            if (result.status !== 'success') {
                console.error('Save error:', result.message);
                alert('Gagal menyimpan tanggal: ' + (result.message || 'Error'));
            }
        } catch (err) {
            console.error('Fetch error:', err);
            alert('Gagal menghubungi server untuk menyimpan tanggal.');
        }
    }
}
