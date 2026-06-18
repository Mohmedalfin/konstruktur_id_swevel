import { api } from '../core/api.js';

export class FormModal {
    constructor(onSubmit) {
        this.onSubmit = onSubmit;
        this.modal = document.getElementById('modal-create-pengadaan');
        this.overlay = document.getElementById('modal-create-overlay');
        this.panel = document.getElementById('modal-create-panel');
        this.form = document.getElementById('form-create-pengadaan');
        this.tbody = document.getElementById('pengadaan-items-tbody');
        this.template = document.getElementById('row-item-template');
        this.emptyState = document.getElementById('empty-state-row');
        this.smartAlert = document.getElementById('smart-mode-alert');
        this.btnSubmit = document.getElementById('btn-submit-create');
        this.itemCountBadge = document.getElementById('item-count-badge');
        
        // Manual search elements
        this.manualSearchSection = document.getElementById('manual-search-section');
        
        // Custom select elements
        this.customSelectContainer = document.getElementById('custom-select-container');
        this.customSelectTrigger = document.getElementById('custom-select-trigger');
        this.customSelectDisplay = document.getElementById('custom-select-display');
        this.customSelectIcon = document.getElementById('custom-select-icon');
        this.customSelectPanel = document.getElementById('custom-select-panel');
        
        this.inputGlobalSearch = document.getElementById('input-global-search');
        this.globalSearchDropdown = document.getElementById('global-search-dropdown');
        
        this.btnAddSelectedItem = document.getElementById('btn-add-selected-item');
        this.selectedItemInfo = document.getElementById('selected-item-info');
        this.selectedItemName = document.getElementById('selected-item-name');
        this.selectedItemStok = document.getElementById('selected-item-stok');
        this.selectedItemSatuan = document.getElementById('selected-item-satuan');
        
        this.isSmartMode = false;
        this.selectedBarang = null; // Temp storage for selected item in search
        
        this.initEvents();
        this.initCustomSelect();
    }

    initEvents() {
        document.getElementById('btn-close-create-modal')?.addEventListener('click', () => this.close());
        document.getElementById('btn-cancel-create')?.addEventListener('click', () => this.close());
        
        this.btnAddSelectedItem?.addEventListener('click', () => {
            if (this.selectedBarang) {
                // Check if already exists
                const existing = this.tbody.querySelector(`input.input-id-barang[value="${this.selectedBarang.id_barang}"]`);
                if (existing) {
                    alert('Barang ini sudah ada di dalam daftar pengajuan.');
                    return;
                }
                
                this.addRow({
                    id_barang: this.selectedBarang.id_barang,
                    nama_barang: this.selectedBarang.nama_barang,
                    satuan: this.selectedBarang.satuan,
                    stok_aktual: this.selectedBarang.stok_aktual || 0,
                    stok_minimum: this.selectedBarang.stok_minimum || 0,
                    volume: ''
                });
                
                this.clearSearch();
            }
        });

        this.btnSubmit?.addEventListener('click', () => this.handleSubmit());
    }

    initCustomSelect() {
        if (!this.customSelectTrigger) return;

        let timeout = null;

        const performSearch = async (keyword) => {
            // Show loading state
            this.globalSearchDropdown.innerHTML = '<div class="px-4 py-8 text-sm text-slate-500 text-center"><i class="fas fa-spinner fa-spin text-2xl mb-2 text-indigo-500 block"></i>Memuat data barang...</div>';

            try {
                const result = await api.searchBarang(keyword);
                const items = result.data || [];
                
                if (items.length === 0) {
                    this.globalSearchDropdown.innerHTML = '<div class="px-4 py-8 text-sm text-slate-500 text-center">Barang tidak ditemukan</div>';
                } else {
                    this.globalSearchDropdown.innerHTML = items.map(item => `
                        <div class="px-4 py-2 hover:bg-indigo-50 cursor-pointer border-b border-slate-100 last:border-0 transition-colors"
                                data-item='${JSON.stringify(item).replace(/'/g, "&#39;")}'>
                            <div class="font-semibold text-sm text-slate-700">${item.nama_barang}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Stok Gudang: <span class="font-bold">${parseFloat(item.stok_aktual || 0)}</span> ${item.satuan}</div>
                        </div>
                    `).join('');

                    this.globalSearchDropdown.querySelectorAll('div[data-item]').forEach(option => {
                        option.addEventListener('click', () => {
                            const data = JSON.parse(option.dataset.item);
                            this.selectBarangFromSearch(data);
                            toggleDropdown(false); // Close dropdown on select
                        });
                    });
                }
            } catch (error) {
                console.error('Error search:', error);
                this.globalSearchDropdown.innerHTML = '<div class="px-4 py-8 text-sm text-red-500 text-center">Gagal memuat data barang</div>';
            }
        };

        const toggleDropdown = (show) => {
            if (show) {
                this.customSelectPanel.classList.remove('hidden');
                this.customSelectTrigger.classList.add('border-indigo-500', 'ring-1', 'ring-indigo-500');
                // Change search icon to chevron up
                this.customSelectIcon.classList.remove('fa-search');
                this.customSelectIcon.classList.add('fa-chevron-up');
                
                this.inputGlobalSearch.focus();
                
                // Auto fetch if empty state to show default list
                if (!this.inputGlobalSearch.value.trim() && this.globalSearchDropdown.innerHTML.includes('Ketik nama barang')) {
                    performSearch('');
                }
            } else {
                this.customSelectPanel.classList.add('hidden');
                this.customSelectTrigger.classList.remove('border-indigo-500', 'ring-1', 'ring-indigo-500');
                
                // Revert icon
                this.customSelectIcon.classList.remove('fa-chevron-up');
                this.customSelectIcon.classList.add('fa-search');
            }
        };

        // Toggle dropdown on trigger click
        this.customSelectTrigger.addEventListener('click', () => {
            const isHidden = this.customSelectPanel.classList.contains('hidden');
            toggleDropdown(isHidden);
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.customSelectContainer.contains(e.target)) {
                toggleDropdown(false);
            }
        });

        // Handle search input
        this.inputGlobalSearch.addEventListener('input', (e) => {
            const keyword = e.target.value.trim();
            clearTimeout(timeout);
            
            timeout = setTimeout(() => {
                performSearch(keyword);
            }, 300);
        });
    }

    selectBarangFromSearch(data) {
        this.selectedBarang = data;
        
        // Update Trigger Display
        this.customSelectDisplay.textContent = data.nama_barang;
        this.customSelectDisplay.classList.remove('text-slate-400');
        this.customSelectDisplay.classList.add('text-slate-800', 'font-semibold');
        
        // Enable Add Button
        if (this.btnAddSelectedItem) {
            this.btnAddSelectedItem.disabled = false;
            this.btnAddSelectedItem.classList.remove('bg-slate-200', 'text-slate-500', 'cursor-not-allowed');
            this.btnAddSelectedItem.classList.add('bg-indigo-100', 'text-indigo-700', 'hover:bg-indigo-200');
        }
    }

    clearSearch() {
        this.selectedBarang = null;
        
        if (this.inputGlobalSearch) {
            this.inputGlobalSearch.value = '';
            this.globalSearchDropdown.innerHTML = `
                <div class="px-4 py-8 text-sm text-slate-400 text-center flex flex-col items-center">
                    <i class="fas fa-search text-2xl mb-2 text-slate-200"></i>
                    Ketik nama barang untuk memulai pencarian
                </div>
            `;
        }

        if (this.customSelectDisplay) {
            this.customSelectDisplay.textContent = 'Pilih barang untuk ditambahkan ke daftar...';
            this.customSelectDisplay.classList.add('text-slate-400');
            this.customSelectDisplay.classList.remove('text-slate-800', 'font-semibold');
        }
        
        if (this.btnAddSelectedItem) {
            this.btnAddSelectedItem.disabled = true;
            this.btnAddSelectedItem.classList.add('bg-slate-200', 'text-slate-500', 'cursor-not-allowed');
            this.btnAddSelectedItem.classList.remove('bg-indigo-100', 'text-indigo-700', 'hover:bg-indigo-200');
        }
    }

    open(smartModeItems = null) {
        this.modal.classList.remove('hidden');
        // Trigger reflow
        void this.modal.offsetWidth;
        
        this.overlay.classList.remove('opacity-0');
        this.overlay.classList.add('opacity-100');
        
        this.panel.classList.remove('opacity-0', 'scale-95');
        this.panel.classList.add('opacity-100', 'scale-100');

        this.form.reset();
        this.clearSearch();
        this.tbody.innerHTML = ''; // clear rows
        
        if (smartModeItems && smartModeItems.length > 0) {
            this.isSmartMode = true;
            this.smartAlert.classList.remove('hidden');
            
            smartModeItems.forEach(item => {
                // Calculate suggested volume: stok_minimum - stok_aktual
                const suggestedVolume = Math.max(0, parseFloat(item.stok_minimum) - parseFloat(item.stok_aktual));
                this.addRow({
                    id_barang: item.id_barang,
                    nama_barang: item.nama_barang,
                    satuan: item.satuan,
                    stok_aktual: item.stok_aktual,
                    stok_minimum: item.stok_minimum,
                    volume: suggestedVolume > 0 ? suggestedVolume : 1
                });
            });
        } else {
            this.isSmartMode = false;
            this.smartAlert.classList.add('hidden');
            if (this.emptyState) {
                this.tbody.appendChild(this.emptyState.cloneNode(true));
            }
        }
        
        this.updateRowNumbers();
    }

    close() {
        this.overlay.classList.remove('opacity-100');
        this.overlay.classList.add('opacity-0');
        
        this.panel.classList.remove('opacity-100', 'scale-100');
        this.panel.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            this.modal.classList.add('hidden');
        }, 300);
    }

    addRow(data) {
        // Remove empty state if exists
        const emptyState = this.tbody.querySelector('#empty-state-row');
        if (emptyState) {
            emptyState.remove();
        }

        const clone = this.template.content.cloneNode(true);
        const tr = clone.querySelector('tr');
        
        const inputIdBarang = tr.querySelector('.input-id-barang');
        const displayNama = tr.querySelector('.nama-text');
        const displaySatuanBadge = tr.querySelector('.satuan-badge');
        const displayStokAktual = tr.querySelector('.display-stok-aktual');
        const displayStokMin = tr.querySelector('.display-stok-min');
        const inputVolume = tr.querySelector('.input-volume');
        const btnRemove = tr.querySelector('.btn-remove-row');

        inputIdBarang.value = data.id_barang;
        displayNama.textContent = data.nama_barang;
        displaySatuanBadge.textContent = data.satuan;
        displayStokAktual.textContent = parseFloat(data.stok_aktual || 0);
        displayStokMin.textContent = `Min: ${parseFloat(data.stok_minimum || 0)}`;
        inputVolume.value = data.volume || '';

        // Auto focus the volume input
        setTimeout(() => {
            inputVolume.focus();
        }, 10);

        btnRemove.addEventListener('click', () => {
            tr.remove();
            this.updateRowNumbers();
            
            // Re-add empty state if table is empty
            if (this.tbody.children.length === 0 && this.emptyState) {
                this.tbody.appendChild(this.emptyState.cloneNode(true));
            }
        });

        // Insert at the top
        this.tbody.insertBefore(tr, this.tbody.firstChild);
        this.updateRowNumbers();
    }

    updateRowNumbers() {
        const rows = this.tbody.querySelectorAll('tr.item-row');
        rows.forEach((row, index) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = index + 1;
        });
        
        if (this.itemCountBadge) {
            this.itemCountBadge.textContent = `${rows.length} Item`;
            
            if (rows.length > 0) {
                this.itemCountBadge.classList.replace('bg-slate-100', 'bg-indigo-100');
                this.itemCountBadge.classList.replace('text-slate-500', 'text-indigo-700');
            } else {
                this.itemCountBadge.classList.replace('bg-indigo-100', 'bg-slate-100');
                this.itemCountBadge.classList.replace('text-indigo-700', 'text-slate-500');
            }
        }
    }

    async handleSubmit() {
        if (!this.form.checkValidity()) {
            this.form.reportValidity();
            return;
        }

        const rows = this.tbody.querySelectorAll('tr.item-row');
        if (rows.length === 0) {
            alert('Minimal harus ada 1 item barang yang diajukan.');
            return;
        }

        const items = [];
        let isValid = true;
        
        rows.forEach(row => {
            const idBarang = row.querySelector('.input-id-barang').value;
            const volume = row.querySelector('.input-volume').value;
            
            if (!idBarang || !volume || parseFloat(volume) <= 0) {
                row.querySelector('.input-volume').classList.add('border-red-500', 'ring-red-200');
                isValid = false;
            } else {
                row.querySelector('.input-volume').classList.remove('border-red-500', 'ring-red-200');
                items.push({
                    id_barang: idBarang,
                    volume: parseFloat(volume)
                });
            }
        });

        if (!isValid) {
            alert('Pastikan semua item memiliki volume yang valid (> 0).');
            return;
        }

        const payload = {
            keterangan: document.getElementById('create-keterangan').value,
            items: items
        };

        const originalText = this.btnSubmit.innerHTML;
        this.btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
        this.btnSubmit.disabled = true;

        try {
            if (this.onSubmit) {
                await this.onSubmit(payload);
            }
            this.close();
            // Optional: alert('Pengajuan berhasil dibuat!');
        } catch (error) {
            alert(error.message || 'Terjadi kesalahan saat menyimpan pengajuan.');
        } finally {
            this.btnSubmit.innerHTML = originalText;
            this.btnSubmit.disabled = false;
        }
    }
}
