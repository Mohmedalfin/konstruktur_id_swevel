let state = {
    selectedCategories: [],
    realisasiData: [],
    batchItems: [],
    sdmResources: [],
    batchSdmItems: [],
    sdmData: [],
    sdmFilterStart: null,
    sdmFilterEnd: null,
    logFilterStart: null,
    logFilterEnd: null,
};

export function getState() {
    return state;
}

export function updateState(newState) {
    state = { ...state, ...newState };
}
