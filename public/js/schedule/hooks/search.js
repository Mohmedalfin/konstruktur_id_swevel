import { getState, updateState } from '../core/state.js';
import { renderBody } from '../components/render.js';

export function handleSearch(query) {
    const { schedules } = getState();
    const lowerQuery = query.toLowerCase().trim();

    if (!lowerQuery) {
        updateState({ filteredIds: null });
        renderBody();
        return;
    }

    const matchedIds = [];
    schedules.forEach(cat => {
        cat.items.forEach(item => {
            if (item.nama.toLowerCase().includes(lowerQuery) || cat.kategori.toLowerCase().includes(lowerQuery)) {
                matchedIds.push(item.id);
            }
        });
    });

    updateState({ filteredIds: matchedIds });
    renderBody();
}

export function bindSearch() {
    const searchInput = document.getElementById('schedule-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
        handleSearch(e.target.value);
    });
}
