import { ReconcileState } from './core/state.js';
import { FilterProyek } from './components/filterProyek.js';
import { useEvents } from './hooks/useEvents.js';
import { useReconcile } from './hooks/useReconcile.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Fitur Filter
    FilterProyek.init();

    // 2. Inisialisasi State Manajemen Rekonsiliasi
    const state = new ReconcileState();

    // 3. Pasang Event Listeners Utama (Tombol Hapus & Selesai di luar Modal)
    useEvents(state);

    // 4. Pasang Event Listeners khusus di dalam Modal Rekonsiliasi
    useReconcile(state);
});
