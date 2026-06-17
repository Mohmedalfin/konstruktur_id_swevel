import { api } from '../core/api.js';

/**
 * State manager for Pengadaan Module
 */
export function usePengadaanData() {
    let state = {
        stats: null,
        data: [],
        criticalItems: [],
        filters: {
            status: 'all',
            month: document.getElementById('filter-month')?.value || '',
            search: ''
        },
        loading: {
            stats: false,
            data: false,
            criticalItems: false,
            submit: false
        }
    };

    const listeners = [];

    const subscribe = (listener) => {
        listeners.push(listener);
        return () => {
            const index = listeners.indexOf(listener);
            if (index > -1) listeners.splice(index, 1);
        };
    };

    const notify = () => {
        listeners.forEach(listener => listener(state));
    };

    const setState = (newState) => {
        state = { ...state, ...newState };
        notify();
    };

    const setFilter = (key, value) => {
        state.filters[key] = value;
        notify();
        fetchData();
    };

    const fetchStats = async () => {
        setState({ loading: { ...state.loading, stats: true } });
        try {
            const result = await api.getStats();
            setState({ stats: result.data, loading: { ...state.loading, stats: false } });
        } catch (error) {
            console.error('Error fetching stats:', error);
            setState({ loading: { ...state.loading, stats: false } });
        }
    };

    const fetchData = async () => {
        setState({ loading: { ...state.loading, data: true } });
        try {
            const result = await api.getData(state.filters);
            setState({ data: result.data || [], loading: { ...state.loading, data: false } });
        } catch (error) {
            console.error('Error fetching data:', error);
            setState({ loading: { ...state.loading, data: false } });
        }
    };

    const fetchCriticalItems = async () => {
        setState({ loading: { ...state.loading, criticalItems: true } });
        try {
            const result = await api.getItemsKritis();
            setState({ criticalItems: result.data || [], loading: { ...state.loading, criticalItems: false } });
        } catch (error) {
            console.error('Error fetching critical items:', error);
            setState({ loading: { ...state.loading, criticalItems: false } });
        }
    };

    const submitPengajuan = async (payload) => {
        setState({ loading: { ...state.loading, submit: true } });
        try {
            const result = await api.store(payload);
            setState({ loading: { ...state.loading, submit: false } });
            
            // Refresh data after successful submit
            fetchStats();
            fetchData();
            fetchCriticalItems();
            
            return result;
        } catch (error) {
            setState({ loading: { ...state.loading, submit: false } });
            throw error;
        }
    };

    const deletePengajuan = async (prId) => {
        try {
            const result = await api.destroy(prId);
            fetchStats();
            fetchData();
            fetchCriticalItems();
            return result;
        } catch (error) {
            throw error;
        }
    };

    // Initial load
    const init = () => {
        fetchStats();
        fetchData();
        fetchCriticalItems();
    };

    return {
        subscribe,
        getState: () => state,
        setFilter,
        fetchStats,
        fetchData,
        fetchCriticalItems,
        submitPengajuan,
        deletePengajuan,
        init
    };
}
