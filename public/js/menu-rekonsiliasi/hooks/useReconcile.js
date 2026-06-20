import { ModalReconcile } from '../components/modalReconcile.js';
import { ProyekAPI } from '../core/api.js';
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';

// Same mixin as shared/ui/confirm.js
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
    scrollbarPadding: false
});

export const useReconcile = (state) => {
    const tbody = document.getElementById('table-reconcile-body');
    const btnProses = document.getElementById('btn-proses-reconcile');

    if (!tbody || !btnProses) return;

    // Handle input changes (Retur, Mutasi, Waste, Proyek Tujuan)
    const handleReconcileInput = (e) => {
        const target = e.target;
        const isInputNumber = target.classList.contains('input-retur') || 
                              target.classList.contains('input-mutasi') || 
                              target.classList.contains('input-waste');
        const isSelectProyek = target.classList.contains('select-mutasi');

        if (!isInputNumber && !isSelectProyek) return;

        const idBarang = parseInt(target.getAttribute('data-id'));
        if (!idBarang) return;

        if (isInputNumber) {
            let value = parseFloat(target.value) || 0;
            // Prevent negative values
            if (value < 0) {
                value = 0;
                target.value = 0;
            }

            if (target.classList.contains('input-retur')) state.updateAllocation(idBarang, 'retur', value);
            if (target.classList.contains('input-mutasi')) {
                state.updateAllocation(idBarang, 'mutasi', value);
                
                const selectMutasi = tbody.querySelector(`.select-mutasi[data-id="${idBarang}"]`);
                if (selectMutasi && value <= 0) {
                    selectMutasi.value = '';
                    // Preline JS update (if needed, but change event will fire or we update state manually)
                    state.updateAllocation(idBarang, 'id_proyek_tujuan', '');
                }
            }
            if (target.classList.contains('input-waste')) state.updateAllocation(idBarang, 'waste', value);
        }

        if (isSelectProyek) {
            state.updateAllocation(idBarang, 'id_proyek_tujuan', parseInt(target.value) || '');
        }

        // Validate and update UI
        const item = state.items.find(i => String(i.id_barang) === String(idBarang));
        if (item) {
            const totalAlokasi = (item.retur + item.mutasi + item.waste);
            const sisa = item.stok_aktual - totalAlokasi;
            const isBalanced = Math.abs(sisa) < 0.0001;
            const hasInvalidMutasi = item.mutasi > 0 && !item.id_proyek_tujuan;

            ModalReconcile.updateRowStatus(idBarang, sisa, isBalanced, hasInvalidMutasi);
        }

        // Check overall validity to enable/disable submit button
        ModalReconcile.toggleSubmitButton(state.isValid());
    };

    tbody.addEventListener('input', handleReconcileInput);
    tbody.addEventListener('change', handleReconcileInput);

    // Handle Submit
    btnProses.addEventListener('click', async () => {
        if (!state.isValid()) return;

        const { isConfirmed } = await AppSwal.fire({
            target: document.getElementById('modal-reconcile-proyek') || document.body,
            icon: 'warning',
            title: 'Selesaikan Proyek?',
            html: `Pastikan alokasi sisa material sudah benar. Tindakan ini tidak dapat dibatalkan.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal',
            focusCancel: true,
        });

        if (!isConfirmed) return;

        ModalReconcile.setLoadingSubmit(true);

        try {
            const payload = state.getPayload();
            const res = await ProyekAPI.selesaiReconcile(state.currentProjectId, payload);

            // Close Modal
            if (typeof window.closeReconcileModal === 'function') {
                window.closeReconcileModal();
            } else {
                const modal = document.getElementById('modal-reconcile-proyek');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            // Update DOM in-place without reload
            const card = document.getElementById(`proyek-card-${state.currentProjectId}`);
            if (card) {
                const dropdownWrap = card.querySelector('.hs-dropdown')?.closest('div.absolute');
                if (dropdownWrap) {
                    dropdownWrap.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/90 backdrop-blur-sm text-white text-xs font-semibold shadow">
                        <i class="fa-solid fa-circle-check"></i> Selesai
                    </span>`;
                }
                const footerLink = card.querySelector('.group-hover\\:underline');
                if (footerLink) {
                    footerLink.outerHTML = `
                    <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color:#10b981;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span class="hidden sm:inline">Selesai</span>
                    </span>`;
                }
            }

            AppSwal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: 'Proyek dan sisa material berhasil diselesaikan!',
                showConfirmButton: false, timer: 3000, timerProgressBar: true,
            });

        } catch (err) {
            AppSwal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: err.message || 'Terjadi kesalahan',
                showConfirmButton: false, timer: 3000,
            });
        } finally {
            ModalReconcile.setLoadingSubmit(false);
        }
    });
};
