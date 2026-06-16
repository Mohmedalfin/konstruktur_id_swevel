import { getState, updateState } from '../core/state.js';
import { fetchStats, fetchRequests, fetchRequestDetail, updateRequestStatus, deleteRequest } from '../core/data.js';
import { renderStats, renderRequestsList, renderDetailModal } from '../components/render.js';
import { toast } from '../../shared/ui/toast.js';

// SweetAlert2 helper (matching shared mixin in project)
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';
const AppSwal = Swal.mixin({
    customClass: {
        popup: 'app-swal-popup',
        title: 'app-swal-title',
        htmlContainer: 'app-swal-html',
        confirmButton: 'app-swal-confirm',
        cancelButton: 'app-swal-cancel',
        icon: 'app-swal-icon',
    },
    buttonsStyling: false,
    reverseButtons: true,
    scrollbarPadding: false,
});

export async function initMonitoring() {
    // 1. Fetch and render initial stats and list
    await reloadDashboard();

    // 1b. Bind month filter change
    const monthFilter = document.getElementById('filter-month');
    if (monthFilter) {
        monthFilter.addEventListener('change', async (e) => {
            updateState({ month: e.target.value });
            
            // Show spinner while loading list
            const container = document.getElementById('history-container');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                        <p class="text-sm font-semibold">Memuat riwayat permintaan...</p>
                    </div>
                `;
            }
            await reloadDashboard();
        });
    }

    // 2. Bind filter clicks
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            // Update active states
            filterButtons.forEach(b => {
                b.className = 'filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none';
            });
            btn.className = 'filter-btn px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800';

            const status = btn.dataset.status;
            updateState({ activeFilter: status });

            // Show spinner while loading list
            const container = document.getElementById('history-container');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                        <p class="text-sm font-semibold">Memuat riwayat permintaan...</p>
                    </div>
                `;
            }

            const data = await fetchRequests(status, getState().month);
            updateState({ requests: data });
            renderRequestsList(data);
        });
    });

    // 3. Bind Detail Button click (uses event delegation for dynamic cards)
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-detail-ajax');
        if (!btn) return;

        const id = parseInt(btn.dataset.id);
        const modalEl = document.getElementById('modal-detail-permintaan');
        if (!modalEl) return;

        // Reset modal body spinner
        const modalBody = document.getElementById('detail-modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="text-center py-10 text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-sm font-semibold">Memuat rincian...</p>
                </div>
            `;
        }

        // Open Preline modal
        if (window.HSOverlay) {
            window.HSOverlay.open(modalEl);
        } else {
            modalEl.classList.remove('hidden');
        }

        const request = await fetchRequestDetail(id);
        if (request) {
            updateState({ selectedRequest: request });
            renderDetailModal(request, window.PERMINTAAN_INIT.userRole);
        } else {
            // Error handling
            if (modalBody) modalBody.innerHTML = '<p class="text-center text-red-500 font-semibold py-4">Gagal memuat rincian permintaan.</p>';
        }
    });

    // 4. Bind Action Status Button clicks inside modal
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-change-status');
        if (!btn) return;

        const id = parseInt(btn.dataset.id);
        const action = btn.dataset.action; // 'disetujui' (Setuju), 'ditolak' (Tolak), 'selesai' (Kirim)

        let confirmTitle = '';
        let confirmText = '';
        let confirmBtnText = '';
        let successToast = '';
        let confirmBtnColor = '';

        if (action === 'disetujui') {
            confirmTitle = 'Setujui Permintaan?';
            confirmText = 'Permintaan barang ini akan disetujui untuk dipersiapkan logistik.';
            confirmBtnText = 'Ya, Setujui!';
            successToast = 'Permintaan disetujui!';
        } else if (action === 'ditolak') {
            confirmTitle = 'Tolak Permintaan?';
            confirmText = 'Dokumen permintaan ini akan ditolak.';
            confirmBtnText = 'Ya, Tolak!';
            successToast = 'Permintaan ditolak!';
        } else if (action === 'selesai') {
            confirmTitle = 'Kirim Barang?';
            confirmText = 'Kirim barang-barang ini ke lapangan. Status permintaan akan selesai.';
            confirmBtnText = 'Ya, Kirim!';
            successToast = 'Status selesai, barang dikirim!';
        }

        const confirmResult = await AppSwal.fire({
            icon: 'question',
            title: confirmTitle,
            text: confirmText,
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal',
            focusCancel: true
        });

        if (!confirmResult.isConfirmed) return;

        try {
            // Show loading
            if (window.showLoader) window.showLoader();

            const res = await updateRequestStatus(id, action);
            if (res.status === 'success') {
                if (window.hideLoader) window.hideLoader();

                // Close modal
                const modalEl = document.getElementById('modal-detail-permintaan');
                if (modalEl) {
                    if (window.HSOverlay) {
                        window.HSOverlay.close(modalEl);
                    } else {
                        modalEl.classList.add('hidden');
                    }
                }

                // Show toast
                toast.show(successToast, 'success');

                // Reload dashboard state
                await reloadDashboard();
            } else {
                if (window.hideLoader) window.hideLoader();
                throw new Error(res.message || 'Gagal mengubah status');
            }
        } catch (error) {
            if (window.hideLoader) window.hideLoader();
            AppSwal.fire({
                icon: 'error',
                title: 'Gagal memproses',
                text: error.message || 'Terjadi kesalahan sistem.'
            });
        }
    });

    // 5. Bind Delete Button click
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-delete-request');
        if (!btn) return;

        const id = parseInt(btn.dataset.id);

        const confirmResult = await AppSwal.fire({
            icon: 'warning',
            title: 'Batalkan Permintaan?',
            text: 'Permintaan ini akan dibatalkan dan dihapus secara permanen. Volume yang diminta akan dikembalikan ke sisa kuota RAP.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan!',
            confirmButtonColor: '#e11d48',
            cancelButtonText: 'Kembali',
            focusCancel: true
        });

        if (!confirmResult.isConfirmed) return;

        try {
            if (window.showLoader) window.showLoader();

            const res = await deleteRequest(id);
            if (res.status === 'success') {
                if (window.hideLoader) window.hideLoader();

                toast.show('Permintaan berhasil dibatalkan', 'success');

                await reloadDashboard();
            } else {
                if (window.hideLoader) window.hideLoader();
                throw new Error(res.message || 'Gagal menghapus permintaan');
            }
        } catch (error) {
            if (window.hideLoader) window.hideLoader();
            AppSwal.fire({
                icon: 'error',
                title: 'Gagal memproses',
                text: error.message || 'Terjadi kesalahan sistem.'
            });
        }
    });
}

async function reloadDashboard() {
    const state = getState();
    const [stats, requests] = await Promise.all([
        fetchStats(state.month),
        fetchRequests(state.activeFilter, state.month)
    ]);

    if (stats) updateState({ stats });
    if (requests) updateState({ requests });

    renderStats(stats);
    renderRequestsList(requests);
}
