let dashboardState = {
    isLoading: true,
    error: null,
    data: null // Akan menyimpan data overview, summary, dsb.
};

/**
 * Mengambil seluruh state saat ini
 */
export function getState() {
    return dashboardState;
}

/**
 * Mengupdate state secara parsial (merging)
 * @param {Object} newState 
 */
export function setState(newState) {
    dashboardState = { ...dashboardState, ...newState };
}

/**
 * Setter khusus untuk data dashboard utama
 * @param {Object} data 
 */
export function setDashboardData(data) {
    dashboardState.data = data;
}
