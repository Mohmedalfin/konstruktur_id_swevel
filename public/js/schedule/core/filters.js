import { getState, updateState } from './state.js';
import { renderBody } from '../components/render.js';

function normalizeText(value) {
    return String(value || '').toLowerCase().trim();
}

function checkCategoryMatch(rowCategory, selectedCategories) {
    if (!selectedCategories || selectedCategories.length === 0) return true;
    return selectedCategories.some(cat => normalizeText(cat) === normalizeText(rowCategory));
}

function checkDateMatch(taskStartStr, taskFinishStr, filterStartStr, filterEndStr) {
    if (!filterStartStr && !filterEndStr) return true;
    if (!taskStartStr || !taskFinishStr) return false;

    const taskStart = new Date(taskStartStr);
    const taskFinish = new Date(taskFinishStr);
    
    if (filterStartStr) {
        const filterStart = new Date(filterStartStr);
        if (taskFinish < filterStart) return false;
    }
    
    if (filterEndStr) {
        const filterEnd = new Date(filterEndStr);
        if (taskStart > filterEnd) return false;
    }
    
    return true;
}

function itemMatchesSearch(item, query, categoryName) {
    if (!query) return true;
    const itemName = normalizeText(item?.nama);
    const category = normalizeText(categoryName);
    return itemName.includes(query) || category.includes(query);
}

function cloneItemWithAllChildren(item) {
    return {
        ...item,
        children: Array.isArray(item.children)
            ? item.children.map(cloneItemWithAllChildren)
            : []
    };
}

function filterItemTree(item, query, categoryName, filterState) {
    const passesSearch = itemMatchesSearch(item, query, categoryName);
    const passesDate = checkDateMatch(item.start_date, item.finish_date, filterState.startDate, filterState.endDate);
    
    const selfMatches = passesSearch && passesDate;
    
    const children = Array.isArray(item.children) ? item.children : [];

    if (selfMatches) {
        return cloneItemWithAllChildren(item);
    }

    const filteredChildren = children
        .map(child => filterItemTree(child, query, categoryName, filterState))
        .filter(Boolean);

    if (filteredChildren.length > 0) {
        return {
            ...item,
            children: filteredChildren
        };
    }

    return null;
}

function filterSchedulesTree(schedules, query, filterState) {
    return schedules
        .map(category => {
            const categoryName = category?.kategori || '';
            const items = Array.isArray(category?.items) ? category.items : [];

            const passesCategoryFilter = checkCategoryMatch(categoryName, filterState.categories);
            if (!passesCategoryFilter) return null;

            const filteredItems = items
                .map(item => filterItemTree(item, query, categoryName, filterState))
                .filter(Boolean);

            if (filteredItems.length === 0 && (query || filterState.startDate || filterState.endDate)) {
                return null;
            }

            return {
                ...category,
                items: filteredItems
            };
        })
        .filter(Boolean);
}

export function applyAllFilters() {
    const { schedules, searchQuery, filters } = getState();

    if (!searchQuery && (!filters.categories || filters.categories.length === 0) && !filters.startDate && !filters.endDate) {
        updateState({ filteredSchedules: null });
    } else {
        const filteredSchedules = filterSchedulesTree(
            Array.isArray(schedules) ? schedules : [],
            searchQuery,
            filters
        );
        updateState({ filteredSchedules });
    }

    renderBody();
}
