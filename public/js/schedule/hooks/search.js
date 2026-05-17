import { getState, updateState } from '../core/state.js';
import { applyAllFilters } from '../core/filters.js';

export function handleSearch(query) {
    updateState({ searchQuery: query });
    applyAllFilters();
}

export function bindSearch() {
    const searchInput = document.getElementById('schedule-search');

    if (!searchInput) return;

    searchInput.addEventListener('input', event => {
        handleSearch(event.target.value);
    });
}