let state = {
    // Monitoring Dashboard State
    requests: [],
    stats: {
        total: 0,
        pending: 0,
        disetujui: 0,
        selesai: 0,
        ditolak: 0
    },
    activeFilter: 'all',
    month: document.getElementById('filter-month') ? document.getElementById('filter-month').value : '',
    selectedRequest: null,

    // Creation Form State
    projectRows: []
};

export function getState() {
    return state;
}

export function updateState(newState) {
    state = { ...state, ...newState };
}

export function resetFormState() {
    state.projectRows = [
        {
            id: Date.now(),
            selectedProjectId: '',
            rapItems: [],
            items: []
        }
    ];
}
