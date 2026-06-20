import { ProyekAPI } from '../core/api.js';
import { ModalReconcile } from '../components/modalReconcile.js';
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
    reverseButtons: true
});

function closeAllDropdowns() {
    document.querySelectorAll('.hs-dropdown.open, .hs-dropdown[open]').forEach(dd => {
        // Gunakan Preline API jika tersedia
        if (window.HSDropdown) {
            const instance = HSDropdown.getInstance(dd, true);
            if (instance?.element) instance.element.close();
        }
        // Fallback: hapus class open dan sembunyikan menu secara langsung
        dd.removeAttribute('open');
        dd.classList.remove('open');
        const menu = dd.querySelector('.hs-dropdown-menu');
        if (menu) {
            menu.classList.add('hidden');
            menu.classList.remove('block', 'opacity-100', 'pointer-events-auto');
        }
    });
}

export const useEvents = (state) => {
    document.addEventListener('click', async function (e) {
        // 1. Tangani Klik Selesai
        const btnSelesai = e.target.closest('.btn-selesai-proyek');
        if (btnSelesai) {
            e.stopPropagation();
            closeAllDropdowns();

            const id = btnSelesai.dataset.id;
            const nama = btnSelesai.dataset.nama;
            const originalContent = btnSelesai.innerHTML;

            try {
                // Show inline loading
                btnSelesai.innerHTML = `<span class="animate-spin inline-block size-4 border-[2px] border-current border-t-transparent text-emerald-600 rounded-full" role="status" aria-label="loading"></span> <span class="text-emerald-600">Memeriksa...</span>`;
                btnSelesai.disabled = true;

                // Fetch sisa stok
                const sisaStok = await ProyekAPI.checkSisaStok(id);
                
                if (sisaStok.length === 0) {
                    // Skenario 1: Tidak ada sisa material -> Konfirmasi langsung selesai
                    
                    const { isConfirmed } = await AppSwal.fire({
                        icon: 'question',
                        title: 'Tandai Proyek Selesai?',
                        html: `Proyek <strong>${nama}</strong> tidak memiliki sisa material di lapangan.<br>Akan ditandai sebagai <strong style="color:#10b981">Selesai</strong>.`,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Selesai!',
                        cancelButtonText: 'Batal',
                        focusCancel: true,
                    });

                    if (!isConfirmed) return;

                    const res = await ProyekAPI.selesaikanTanpaSisa(id);
                    
                    // Update UI in-place
                    const card = document.getElementById(`proyek-card-${id}`);
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
                        title: 'Proyek ditandai selesai!',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true,
                    });

                } else {
                    // Skenario 2: Ada sisa material -> Buka Modal Rekonsiliasi
                    state.setCurrentProject(id, nama);
                    state.setItems(sisaStok);
                    
                    // Fetch list proyek aktif untuk pilihan mutasi
                    if (state.activeProjects.length === 0) {
                        const activeProjects = await ProyekAPI.getActiveProjects();
                        state.setActiveProjects(activeProjects);
                    }

                    // Render Modal
                    ModalReconcile.setProjectName(nama);
                    ModalReconcile.renderRows(state.items, state.activeProjects, state);
                    ModalReconcile.toggleSubmitButton(state.isValid());

                    // Open Modal (Custom Toggle)
                    const modalEl = document.getElementById('modal-reconcile-proyek');
                    if (modalEl) {
                        modalEl.classList.remove('hidden');
                        modalEl.classList.add('flex');
                        
                        // Prevent body scrolling when custom modal is open to avoid double scrollbars
                        document.body.style.overflow = 'hidden';
                    }
                }

            } catch (err) {
                AppSwal.fire({
                    toast: true, position: 'top-end', icon: 'error',
                    title: err.message || 'Terjadi kesalahan sistem',
                    showConfirmButton: false, timer: 3000,
                });
            } finally {
                // Revert button state
                btnSelesai.innerHTML = originalContent;
                btnSelesai.disabled = false;
            }
            return;
        }

        // 2. Tangani Klik Hapus
        const btnHapus = e.target.closest('.btn-hapus-proyek');
        if (btnHapus) {
            e.stopPropagation();
            closeAllDropdowns();

            const id = btnHapus.dataset.id;
            const nama = btnHapus.dataset.nama;

            const { isConfirmed } = await AppSwal.fire({
                icon: 'warning',
                title: 'Hapus Proyek Permanen?',
                html: `Proyek <strong>${nama}</strong> beserta seluruh data RAP dan AHS akan <strong style="color:#ef4444">dihapus selamanya</strong>.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                focusCancel: true,
            });

            if (!isConfirmed) return;

            try {
                const res = await ProyekAPI.deleteProject(id);
                
                // Remove card from DOM with animation
                const card = document.getElementById(`proyek-card-${id}`);
                if (card) {
                    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(10px)';
                    setTimeout(() => card.remove(), 400);
                }

                AppSwal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: 'Proyek berhasil dihapus!',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true
                });

            } catch (err) {
                AppSwal.fire({
                    toast: true, position: 'top-end', icon: 'error',
                    title: err.message || 'Gagal menghapus proyek',
                    showConfirmButton: false, timer: 3000
                });
            }
        }
    });
};
