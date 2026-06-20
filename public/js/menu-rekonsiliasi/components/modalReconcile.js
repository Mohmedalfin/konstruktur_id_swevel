export const ModalReconcile = {
    renderRows: (items, activeProjects, state) => {
        const tbody = document.getElementById('table-reconcile-body');
        tbody.innerHTML = '';

        if (items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada sisa material.</td></tr>`;
            return;
        }

        items.forEach((item, index) => {
            // Filter allowed projects dynamically
            const allowedProjects = state.activeProjects.filter(p => 
                p.id_project !== state.currentProjectId && 
                p.allowed_materials && 
                p.allowed_materials.includes(Number(item.id_barang))
            );

            const projectOptions = allowedProjects.map(p => `<option value="${p.id_project}">${p.nama_proyek}</option>`).join('');

            const selectMarkup = allowedProjects.length > 0 
                ? `
                    <select class="select-mutasi hidden" data-id="${item.id_barang}" data-hs-select='{
                        "hasSearch": true,
                        "searchPlaceholder": "Cari proyek...",
                        "searchClasses": "block w-full text-sm border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500 py-2 px-3",
                        "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                        "placeholder": "Pilih Proyek...",
                        "dropdownScope": "window",
                        "toggleTag": "<button type=\\"button\\" aria-expanded=\\"false\\"></button>",
                        "toggleClasses": "relative py-2 px-3 flex items-center w-full cursor-pointer bg-white border border-gray-300 rounded-lg text-start text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all shadow-sm",
                        "dropdownClasses": "mt-2 z-[90] w-full max-h-[250px] p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden overflow-y-auto",
                        "optionClasses": "py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-selected:bg-blue-50 hs-selected:text-blue-600 hs-selected:font-semibold",
                        "extraMarkup": "<div class=\\"absolute top-1/2 end-3 -translate-y-1/2\\"><i class=\\"fas fa-chevron-down text-gray-400 text-xs\\"></i></div>"
                    }'>
                        <option value="">Pilih Proyek...</option>
                        ${projectOptions}
                    </select>
                `
                : `
                    <div class="relative w-full">
                        <select class="select-mutasi w-full pl-3 pr-8 py-2 text-sm border border-gray-300 bg-slate-50 text-gray-400 rounded-lg outline-none cursor-not-allowed appearance-none" data-id="${item.id_barang}" disabled>
                            <option value="" disabled selected>Material tidak ada di RAB Proyek Lain</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-300"></i>
                        </div>
                    </div>
                `;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors';
            
            tr.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${item.nama_barang}</div>
                    <div class="text-xs text-gray-500">${item.kode_barang || '-'}</div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-emerald-600">${parseFloat(item.stok_aktual)} ${item.satuan || ''}</span>
                        ${item.konversi_faktor && item.satuan_kemasan ? `
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded-md">
                            <i class="fa-solid fa-box-open text-slate-400"></i>
                            ${(parseFloat(item.stok_aktual) / parseFloat(item.konversi_faktor)).toFixed(2)} ${item.satuan_kemasan}
                        </span>
                        ` : ''}
                    </div>
                    ${item.konversi_faktor && item.satuan_kemasan ? `
                    <div class="text-[10px] text-slate-400 mt-1">
                        (1 ${item.satuan_kemasan} = ${parseFloat(item.konversi_faktor)} ${item.satuan})
                    </div>
                    ` : ''}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="relative">
                        <input type="number" min="0" step="0.01" class="input-retur w-full pl-3 pr-10 py-2 text-sm border border-gray-300 bg-white rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm disabled:opacity-50 disabled:bg-slate-50" data-id="${item.id_barang}" placeholder="0" value="">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-xs font-medium text-gray-500">${item.satuan || ''}</span>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex gap-2">
                        <div class="relative w-28">
                            <input type="number" min="0" step="0.01" class="input-mutasi w-full pl-3 pr-10 py-2 text-sm border border-gray-300 bg-white rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm ${allowedProjects.length === 0 ? 'bg-slate-50 cursor-not-allowed opacity-50' : ''}" data-id="${item.id_barang}" placeholder="0" value="" ${allowedProjects.length === 0 ? 'disabled' : ''}>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs font-medium text-gray-500">${item.satuan || ''}</span>
                            </div>
                        </div>
                        <div class="flex-1 ${allowedProjects.length === 0 ? 'opacity-50' : ''}">
                            ${selectMarkup}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="relative">
                        <input type="number" min="0" step="0.01" class="input-waste w-full pl-3 pr-10 py-2 text-sm border border-gray-300 bg-white rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm" data-id="${item.id_barang}" placeholder="0" value="">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-xs font-medium text-gray-500">${item.satuan || ''}</span>
                        </div>
                    </div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-center">
                    <span class="status-badge inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-red-100 text-red-800" id="status-${item.id_barang}">
                        Sisa: ${parseFloat(item.stok_aktual)}
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Initialize Preline Selects if available
        if (window.HSSelect) {
            window.HSSelect.autoInit();
        }
    },

    updateRowStatus: (idBarang, sisa, isBalanced, hasInvalidMutasi) => {
        const badge = document.getElementById(`status-${idBarang}`);
        if (!badge) return;

        if (isBalanced && !hasInvalidMutasi) {
            badge.className = 'status-badge inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800';
            badge.innerHTML = `<i class="fa-solid fa-check"></i> OK`;
        } else if (isBalanced && hasInvalidMutasi) {
            badge.className = 'status-badge inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-amber-100 text-amber-800';
            badge.innerHTML = `Pilih Proyek`;
        } else if (sisa < 0) {
            badge.className = 'status-badge inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-red-100 text-red-800';
            badge.innerHTML = `Lebih: ${Math.abs(sisa).toFixed(2)}`;
        } else {
            badge.className = 'status-badge inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-red-100 text-red-800';
            badge.innerHTML = `Sisa: ${sisa.toFixed(2)}`;
        }
    },

    setProjectName: (name) => {
        document.getElementById('reconcile-project-name').textContent = name;
    },

    toggleSubmitButton: (isValid) => {
        const btn = document.getElementById('btn-proses-reconcile');
        btn.disabled = !isValid;
    },
    
    setLoadingSubmit: (isLoading) => {
        const btn = document.getElementById('btn-proses-reconcile');
        if (isLoading) {
            btn.innerHTML = `<span class="animate-spin inline-block size-4 border-[2px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading"></span> Memproses...`;
            btn.disabled = true;
        } else {
            btn.innerHTML = `Proses & Selesaikan Proyek`;
            // Disabled state handled by toggleSubmitButton later
        }
    }
};
