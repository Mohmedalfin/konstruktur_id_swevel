let state = {
    schedules: [],
    collapsedCategories: {},
    timelineConfig: [],
    globalStartDate: null,
    globalEndDate: null,
    totalTimelineDays: 0,
    totalWeeksCount: 0,
    filteredIds: null,
    searchQuery: '',
    viewMode: 'gantt',
    filters: {
        categories: [],
        startDate: '',
        endDate: ''
    }
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
