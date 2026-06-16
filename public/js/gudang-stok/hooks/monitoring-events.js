import { getState, updateState } from '../core/state.js';
import { fetchStats, fetchStokData } from '../core/data.js';
import { renderStats, renderStokList } from '../components/render.js';
import { toast } from '../../shared/ui/toast.js';
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';

let searchTimeout;

export async function initMonitoring() {
    await reloadDashboard();

    const kategoriFilters = document.querySelectorAll('#filter-kategori button');
    kategoriFilters.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            kategoriFilters.forEach(b => {
                b.className = 'px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none';
            });
            btn.className = 'px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800 hover:bg-slate-700';

            const kategori = btn.dataset.kategori;
            updateState({ activeKategori: kategori });
            await fetchAndRenderList();
        });
    });

    const statusFilters = document.querySelectorAll('#filter-status button');
    statusFilters.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            statusFilters.forEach(b => {
                b.className = 'px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none';
            });
            btn.className = 'px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800 hover:bg-slate-700';

            const status = btn.dataset.status;
            updateState({ activeStatus: status });
            await fetchAndRenderList();
        });
    });

    const searchInput = document.getElementById('search-item');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                updateState({ searchQuery: e.target.value.trim() });
                await fetchAndRenderList();
            }, 300);
        });
    }

    // Handle Edit Minimum Click (Event Delegation)
    const tableBody = document.getElementById('stok-table-body');
    if (tableBody) {
        tableBody.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-minimum');
            if (!btn) return;
            
            const idBarang = btn.dataset.id;
            const namaBarang = btn.dataset.nama;
            const satuan = btn.dataset.satuan;
            const currentMinimum = btn.dataset.minimum;

            document.getElementById('edit-id-barang').value = idBarang;
            document.getElementById('edit-nama-barang').value = namaBarang;
            document.getElementById('edit-satuan').value = satuan;
            document.getElementById('edit-stok-minimum').value = currentMinimum;

            // Buka Modal secara programmatis karena tombol ini digenerate JS
            const modal = document.querySelector('#modal-edit-minimum');
            if (window.HSOverlay && modal) {
                window.HSOverlay.open(modal);
            } else if (modal) {
                modal.classList.remove('hidden');
            }
        });
    }

    // Handle Form Submit for Edit Minimum
    const formEdit = document.getElementById('form-edit-minimum');
    if (formEdit) {
        formEdit.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btnSubmit = document.getElementById('btn-save-minimum');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Menyimpan...`;
            btnSubmit.disabled = true;

            const idBarang = document.getElementById('edit-id-barang').value;
            const stokMinimum = document.getElementById('edit-stok-minimum').value;

            try {
                const response = await fetch('/api/stok/update-minimum', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ id_barang: idBarang, stok_minimum: stokMinimum })
                });

                const res = await response.json();

                if (response.ok && res.status === 'success') {
                    // Close modal
                    const modal = document.querySelector('#modal-edit-minimum');
                    if (window.HSOverlay && modal) {
                        window.HSOverlay.close(modal);
                    }

                    // Show success using standard toast
                    toast.show(res.message || 'Batas minimum stok diperbarui.', 'success');

                    // Reload data
                    await reloadDashboard();
                } else {
                    throw new Error(res.message || 'Gagal memperbarui batas minimum stok.');
                }
            } catch (error) {
                console.error('Update minimum error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan saat menyimpan data.'
                });
            } finally {
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        });
    }
}

async function reloadDashboard() {
    const stats = await fetchStats();
    if (stats) {
        updateState({ stats });
        renderStats(stats);
    }
    await fetchAndRenderList();
}

async function fetchAndRenderList() {
    const container = document.getElementById('stok-table-body');
    if (container) {
        container.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                    <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                    <p class="text-sm font-semibold">Memuat data stok...</p>
                </td>
            </tr>
        `;
    }

    const state = getState();
    const data = await fetchStokData(state.activeKategori, state.activeStatus, state.searchQuery);
    updateState({ items: data });
    renderStokList(data);
}
