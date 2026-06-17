import { usePengadaanData } from './hooks/usePengadaanData.js';
import { DashboardStats } from './components/DashboardStats.js';
import { DataTable } from './components/DataTable.js';
import { FormModal } from './components/FormModal.js';
import { DetailModal } from './components/DetailModal.js';
import { AppSwal } from '../shared/ui/confirm.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize state manager
    const dataManager = usePengadaanData();

    // Initialize UI Components
    const dashboardStats = new DashboardStats('stats-container');
    const detailModal = new DetailModal();
    const dataTable = new DataTable('table-container', 
        (prId) => {
            detailModal.open(prId);
        },
        async (prId) => {
            const confirmed = await AppSwal.fire({
                title: 'Batalkan Pengajuan?',
                text: "Pengajuan ini akan dihapus permanen dan tidak akan diteruskan ke tim Purchasing.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Tutup'
            });

            if (confirmed.isConfirmed) {
                try {
                    await dataManager.deletePengajuan(prId);
                    AppSwal.fire({
                        title: 'Berhasil!',
                        text: 'Pengajuan telah dibatalkan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (error) {
                    AppSwal.fire('Gagal!', error.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            }
        }
    );

    const formModal = new FormModal(async (payload) => {
        await dataManager.submitPengajuan(payload);
    });

    // Subscribe UI components to state changes
    dataManager.subscribe((state) => {
        dashboardStats.render(state);
        dataTable.render(state);
    });

    // Setup event listeners for filtering
    const filterMonth = document.getElementById('filter-month');
    const statusFilters = document.querySelectorAll('.filter-btn');

    filterMonth?.addEventListener('change', (e) => {
        dataManager.setFilter('month', e.target.value);
    });

    statusFilters.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Update active state
            statusFilters.forEach(b => {
                b.className = 'filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none';
            });
            const target = e.currentTarget;
            target.className = 'filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800';

            // Apply filter
            dataManager.setFilter('status', target.getAttribute('data-status'));
        });
    });

    // Setup event listeners for actions
    document.getElementById('btn-create-manual')?.addEventListener('click', () => {
        formModal.open(); // Open empty form
    });

    document.getElementById('btn-smart-procurement')?.addEventListener('click', () => {
        const state = dataManager.getState();
        formModal.open(state.criticalItems); // Open pre-filled form with critical items
    });

    // Initial fetch
    dataManager.init();
});
