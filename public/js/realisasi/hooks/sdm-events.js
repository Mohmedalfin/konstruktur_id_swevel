import { renderSDMTable } from '../components/render.js';
import { getState } from '../core/state.js';
import { confirmAction } from '../../shared/ui/confirm.js';
import { toast } from '../../shared/ui/toast.js';
import { getFilteredSDMData } from './sdm-filter.js';

export function initSDMEvents(tbodyElement) {
    if (!tbodyElement) return;

    tbodyElement.addEventListener('click', (e) => {
        const dateRow = e.target.closest('tr[data-sdm-id]');
        const toggleBtn = e.target.closest('.toggle-sdm');
        
        if (dateRow || toggleBtn) {
            const targetRow = toggleBtn ? toggleBtn.closest('tr') : dateRow;
            if (!targetRow) return;

            const id = targetRow.dataset.sdmId;
            const { sdmData } = getState();
            const item = sdmData.find(i => i.id === id || i.id == id);
            
            if (item) {
                item.expanded = !item.expanded;
                renderSDMTable(getFilteredSDMData(), tbodyElement);
            }
            return;
        }

        const tabBtn = e.target.closest('button[data-tab]');
        if (tabBtn) {
            const id = tabBtn.dataset.id;
            const tabName = tabBtn.dataset.tab;
            
            const { sdmData } = getState();
            const item = sdmData.find(i => i.id === id || i.id == id);
            if (item && item.activeTab !== tabName) {
                item.activeTab = tabName;
                renderSDMTable(getFilteredSDMData(), tbodyElement);
            }
            return;
        }

        const deleteBtn = e.target.closest('.btn-delete-sdm-item');
        if (deleteBtn) {
            e.stopPropagation();
            const itemId = deleteBtn.dataset.id;
            if (!itemId) return;

            confirmAction(
                'Hapus Item Penggunaan?',
                'Data yang dihapus tidak dapat dikembalikan, dan volume stok akan otomatis kembali ke sisa stok persediaan.',
                'Ya, Hapus!'
            ).then(async (isConfirmed) => {
                if (isConfirmed) {
                    try {
                        const response = await fetch(`/realisasi/sdm/item/${itemId}`, {
                            method: 'DELETE',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        
                        const res = await response.json();
                        
                        if (!response.ok || res.status !== 'success') {
                            throw new Error(res.message || 'Gagal menghapus item.');
                        }
                        
                        toast.show('Item penggunaan berhasil dihapus.', 'success');
                        
                        const { fetchSDMData, fetchSDMResources } = await import('../core/data.js');
                        const { updateState } = await import('../core/state.js');
                        
                        const newData = await fetchSDMData();
                        const newResources = await fetchSDMResources();
                        const { sdmData } = getState();
                        
                        // Pertahankan state toggle (buka/tutup) dan pindah tab jika tab saat ini kosong
                        newData.forEach(newItem => {
                            const oldItem = sdmData.find(old => old.id === newItem.id);
                            if (oldItem) {
                                newItem.expanded = oldItem.expanded;
                                
                                let currentTab = oldItem.activeTab || 'bahan';
                                // Jika tab saat ini kosong, cari tab lain yang ada isinya
                                if ((currentTab === 'bahan' && newItem.bahan.length === 0) ||
                                    (currentTab === 'alat' && newItem.alat.length === 0) ||
                                    (currentTab === 'tenaga' && newItem.tenaga.length === 0)) {
                                    
                                    if (newItem.bahan.length > 0) currentTab = 'bahan';
                                    else if (newItem.alat.length > 0) currentTab = 'alat';
                                    else if (newItem.tenaga.length > 0) currentTab = 'tenaga';
                                    else if (newItem.dokumentasi.length > 0) currentTab = 'dokumentasi';
                                }
                                newItem.activeTab = currentTab;
                            }
                        });
                        
                        updateState({ sdmData: newData, sdmResources: newResources });
                        renderSDMTable(getFilteredSDMData(), tbodyElement);
                        
                    } catch (err) {
                        console.error('Delete item error:', err);
                        toast.show(`Terjadi kesalahan: ${err.message}`, 'error');
                    }
                }
            });
            return;
        }
    });
}
