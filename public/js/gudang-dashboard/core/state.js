let state = {
    stats: null,
    itemsKritis: [],
    activities: [],
    chartHealth: null
};

export function getState() {
    return state;
}

export function updateState(newState) {
    state = { ...state, ...newState };
}
