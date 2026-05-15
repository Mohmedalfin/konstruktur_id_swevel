import { renderTable } from '../components/render.js';
import { getState, updateState } from '../core/state.js';
import { getFilteredData } from './filter.js';

export function initPekerjaanEvents(tbodyElement) {
    tbodyElement.addEventListener('click', (e) => {
        const toggleCatBtn = e.target.closest('.toggle-category');
        if (toggleCatBtn) {
            const id = toggleCatBtn.dataset.id;
            const { realisasiData } = getState();

            const updated = realisasiData.map(cat => {
                if (String(cat.id) === String(id)) {
                    return { ...cat, expanded: !cat.expanded };
                }
                return cat;
            });

            updateState({ realisasiData: updated });
            renderTable(getFilteredData(), tbodyElement);
            return;
        }

        const toggleItemBtn = e.target.closest('.toggle-item');
        if (toggleItemBtn) {
            const id = parseInt(toggleItemBtn.dataset.id);
            const { realisasiData } = getState();

            const toggleItem = (items) => items.map(item => {
                if (item.id === id) {
                    return { ...item, expandedItem: !item.expandedItem };
                }
                if (item.children && item.children.length > 0) {
                    return { ...item, children: toggleItem(item.children) };
                }
                return item;
            });

            const updated = realisasiData.map(cat => ({
                ...cat,
                children: toggleItem(cat.children || [])
            }));

            updateState({ realisasiData: updated });
            renderTable(getFilteredData(), tbodyElement);
        }
    });
}