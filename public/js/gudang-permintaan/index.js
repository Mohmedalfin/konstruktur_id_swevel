import { initMonitoring } from './hooks/monitoring-events.js';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        if (window.showLoader) window.showLoader();

        if (document.getElementById('status-filters')) {
            await initMonitoring();
        }

    } catch (error) {
        console.error('Error initializing Permintaan module:', error);
    } finally {
        if (window.hideLoader) window.hideLoader();
    }
});
