/**
 * Global State for Schedule Module
 */

let state = {
    schedules: [],
    collapsedCategories: {},
    timelineConfig: [],
    globalStartDate: null,
    globalEndDate: null,
    totalTimelineDays: 0,
    totalWeeksCount: 0,
    filteredIds: null 
};

export function getState() {
    return state;
}

export function updateState(newState) {
    state = { ...state, ...newState };
}

export function setSchedules(data) {
    updateState({ schedules: data });
}

export function toggleCategoryCollapse(categoryId) {
    updateState({
        collapsedCategories: {
            ...state.collapsedCategories,
            [categoryId]: !state.collapsedCategories[categoryId]
        }
    });
}
