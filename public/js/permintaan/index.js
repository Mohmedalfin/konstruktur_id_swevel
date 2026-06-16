import { initMonitoring } from './hooks/monitoring-events.js';
import { initForm } from './hooks/form-events.js';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        if (window.showLoader) window.showLoader();

        if (document.getElementById('status-filters')) {
            await initMonitoring();
        }

        if (document.getElementById('permintaan-form')) {
            initForm();
        }

        if (window.PERMINTAAN_INIT && window.PERMINTAAN_INIT.openCreateModal) {
            const modalEl = document.getElementById('modal-buat-permintaan');
            if (modalEl) {
                setTimeout(() => {
                    if (window.HSOverlay) {
                        window.HSOverlay.open(modalEl);
                    } else {
                        modalEl.classList.remove('hidden');
                    }
                }, 100);
            }
        }

    } catch (error) {
        console.error('Error initializing Permintaan module:', error);
    } finally {
        if (window.hideLoader) window.hideLoader();
    }
});
