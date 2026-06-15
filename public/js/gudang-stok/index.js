import { initMonitoring } from './hooks/monitoring-events.js';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        if (window.showLoader) window.showLoader();
        await initMonitoring();
    } catch (error) {
        console.error('Error initializing Gudang Stok module:', error);
    } finally {
        if (window.hideLoader) window.hideLoader();
    }
});
