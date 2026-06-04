import { state } from '../core/state.js';
import { renderReadonly } from './render.js';
import { fetchRabData } from '../core/data.js';
import { AppSwal } from '../../shared/ui/confirm.js';

export function bindFormatPenomoran() {
    const btn = document.getElementById('format-penomoran-btn');
    const overlay = document.getElementById('format-penomoran-modal-overlay');
    const closeBtn = document.getElementById('format-penomoran-modal-close');
    const cancelBtn = document.getElementById('format-penomoran-modal-cancel');
    const saveBtn = document.getElementById('format-penomoran-modal-save');

    if (!btn || !overlay) return;

    btn.classList.remove('hidden');

    const openModal = () => {
        if (state.sumber_data === 'boq' || state.sumber_data === 'import') {
            AppSwal.fire({
                title: 'Tidak Tersedia',
                text: 'Format penomoran otomatis tidak dapat diubah karena RAP ini menggunakan data hasil Import BOQ (mengikuti penomoran asli file Excel).',
                icon: 'info',
                showCancelButton: false,
                confirmButtonText: 'Mengerti',
                scrollbarPadding: false
            });
            return;
        }

        // Set values from state if they exist
        if (state.format_penomoran) {
            let parsed = state.format_penomoran;
            if (typeof parsed === 'string') {
                try {
                    parsed = JSON.parse(parsed);
                } catch(e) {
                    parsed = {};
                }
            }

            document.getElementById('format-kategori').value = parsed['-1'] || 'A';
            document.getElementById('format-pekerjaan').value = parsed['0'] || '1';
            document.getElementById('format-subpekerjaan').value = parsed['1'] || '1.1';
            const resetEl = document.getElementById('format-reset-pekerjaan');
            if (resetEl) {
                resetEl.value = typeof parsed['reset'] !== 'undefined' ? parsed['reset'] : '1';
            }
        } else {
            document.getElementById('format-kategori').value = 'A';
            document.getElementById('format-pekerjaan').value = '1';
            document.getElementById('format-subpekerjaan').value = '1.1';
            const resetEl = document.getElementById('format-reset-pekerjaan');
            if (resetEl) resetEl.value = '1';
        }
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    };

    const closeModal = () => {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    };

    btn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    saveBtn.addEventListener('click', async () => {
        const format = {
            '-1': document.getElementById('format-kategori').value,
            '0': document.getElementById('format-pekerjaan').value,
            '1': document.getElementById('format-subpekerjaan').value,
            'reset': document.getElementById('format-reset-pekerjaan') ? document.getElementById('format-reset-pekerjaan').value : '1'
        };

        const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;

        try {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            const res = await fetch('/api/rap/format_penomoran', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_project: idProject,
                    format_penomoran: format
                })
            });

            const json = await res.json();
            if (!res.ok || json.status !== 'success') {
                throw new Error(json.message || 'Gagal menyimpan format penomoran');
            }

            if (window.Toast) {
                window.Toast.show('Format penomoran berhasil disimpan', 'success');
            }

            state.format_penomoran = format;
            closeModal();
            
            // Re-render table
            if (idProject) {
                const data = await fetchRabData(idProject);
                renderReadonly(data);
            } else {
                window.location.reload();
            }

        } catch (err) {
            console.error(err);
            if (window.Toast) {
                window.Toast.show(err.message, 'error');
            } else {
                alert(err.message);
            }
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = 'Simpan Format';
        }
    });
}
