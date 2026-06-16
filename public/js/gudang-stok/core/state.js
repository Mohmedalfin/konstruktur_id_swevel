let state = {
    items: [],
    stats: {
        total: 0,
        aman: 0,
        kritis: 0,
        kosong: 0
    },
    activeKategori: 'all',
    activeStatus: 'all',
    searchQuery: ''
};

export function getState() {
    return state;
}

export function updateState(newState) {
    state = { ...state, ...newState };
}
