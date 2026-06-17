import { getState } from '../core/state.js';

export function initListSDMEvents() {
    const searchInput = document.getElementById('search-list-sdm');
    if (!searchInput) return;

    // We keep a reference to render so we can call it when data updates or search changes
    window.renderListSDM = (searchQuery = '') => {
        const { sdmResources } = getState();
        if (!sdmResources || sdmResources.length === 0) return;

        // 1. Group Identical items
        const uniqueItems = {};
        
        sdmResources.forEach(item => {
            const key = `${item.nama_item}|${item.satuan}|${item.spesifikasi}|${item.merk}|${item.kategori}`;
            if (!uniqueItems[key]) {
                uniqueItems[key] = {
                    ...item,
                    qty_budget: parseFloat(item.qty_budget) || 0,
                    qty_used: parseFloat(item.qty_used) || 0,
                    qty_sisa: parseFloat(item.qty_sisa) || 0
                };
            } else {
                uniqueItems[key].qty_budget += parseFloat(item.qty_budget) || 0;
                uniqueItems[key].qty_used += parseFloat(item.qty_used) || 0;
                uniqueItems[key].qty_sisa = uniqueItems[key].qty_budget - uniqueItems[key].qty_used;
            }
        });

        // 2. Filter by search
        const query = searchQuery.toLowerCase();
        const filteredItems = Object.values(uniqueItems).filter(item => {
            return item.nama_item.toLowerCase().includes(query) ||
                   (item.spesifikasi && item.spesifikasi.toLowerCase().includes(query)) ||
                   (item.merk && item.merk.toLowerCase().includes(query));
        });

        // 3. Group by Katanya (First Word)
        const grouped = {
            'Bahan': {},
            'Alat': {},
            'Upah': {}
        };

        filteredItems.forEach(item => {
            let catStr = (item.kategori || '').toLowerCase();
            let cat = null;
            if (catStr === 'bahan') cat = 'Bahan';
            else if (catStr === 'alat') cat = 'Alat';
            else if (catStr === 'upah' || catStr === 'tenaga' || catStr === 'tenaga kerja') cat = 'Upah';

            if (cat) {
                const firstWord = item.nama_item.trim().split(/\s+/)[0].toUpperCase();
                if (!grouped[cat][firstWord]) {
                    grouped[cat][firstWord] = [];
                }
                grouped[cat][firstWord].push(item);
            }
        });

        // 4. Render
        renderCategoryAccordion(grouped['Bahan'], 'bahan');
        renderCategoryAccordion(grouped['Alat'], 'alat');
        renderCategoryAccordion(grouped['Upah'], 'upah');

        // Re-init Preline components for newly injected accordions
        if (window.HSStaticMethods && window.HSStaticMethods.autoInit) {
            window.HSStaticMethods.autoInit();
        }
    };

    function renderCategoryAccordion(group, categoryId) {
        const container = document.getElementById(`accordion-list-${categoryId}`);
        const emptyState = document.getElementById(`empty-list-${categoryId}`);
        const badge = document.querySelector(`.badge-${categoryId}`);
        
        if (!container || !emptyState) return;

        container.innerHTML = '';
        
        const keys = Object.keys(group).sort();
        let totalItemsInCat = 0;

        if (keys.length === 0) {
            container.classList.add('hidden');
            emptyState.classList.remove('hidden');
            if (badge) badge.innerText = '0';
            return;
        }

        container.classList.remove('hidden');
        emptyState.classList.add('hidden');

        let html = '';

        keys.forEach((keyword, index) => {
            const items = group[keyword];
            totalItemsInCat += items.length;
            const accordionId = `accordion-${categoryId}-${index}`;
            
            const renderItemRow = (item) => {
                // Determine icon based on category
                let iconHtml = '';
                if (categoryId === 'bahan') {
                    iconHtml = '<img src="/assets/images/icons/material.png" alt="Icon" class="w-6 h-6 opacity-70" onerror="this.onerror=null; this.outerHTML=\'<div class=\\\'w-6 h-6 rounded flex items-center justify-center text-slate-400 bg-slate-100\\\'><i class=\\\'fas fa-cube text-[10px]\\\'></i></div>\'">';
                } else if (categoryId === 'alat') {
                    iconHtml = '<div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center text-blue-500"><i class="fas fa-tools text-[10px]"></i></div>';
                } else {
                    iconHtml = '<div class="w-6 h-6 rounded bg-red-50 flex items-center justify-center text-red-500"><i class="fas fa-hard-hat text-[10px]"></i></div>';
                }

                // Category Text
                const catText = categoryId.charAt(0).toUpperCase() + categoryId.slice(1);

                return `
                    <div class="grid grid-cols-12 gap-4 px-5 py-2.5 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors items-center bg-white group">
                        <div class="col-span-8 flex items-center gap-3">
                            ${iconHtml}
                            <div>
                                <h4 class="text-[12px] font-bold text-slate-800 leading-tight">${item.nama_item}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">${catText}</p>
                            </div>
                        </div>
                        <div class="col-span-2 text-left">
                            <span class="font-bold text-indigo-700 text-[13px]">${formatNumber(item.qty_budget)} ${item.satuan}</span>
                        </div>
                        <div class="col-span-2 flex items-center justify-between">
                            <span class="font-bold text-emerald-500 text-[13px]">${formatNumber(item.qty_sisa)} ${item.satuan}</span>
                            <button class="text-slate-400 hover:text-slate-600 w-6 h-6 flex items-center justify-center focus:outline-none">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>
                    </div>
                `;
            };

            if (items.length === 1) {
                // Single item
                html += renderItemRow(items[0]);
            } else {
                // Group Wrapper
                const itemsHtml = items.map(item => renderItemRow(item)).join('');
                
                html += `
                    <div class="hs-accordion border-b border-slate-100 last:border-0" id="${accordionId}">
                        <button class="hs-accordion-toggle w-full px-5 py-2.5 inline-flex items-center justify-between bg-slate-50/50 hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:pointer-events-none" aria-controls="${accordionId}-collapse">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-[11px]">
                                    ${items.length}
                                </div>
                                <span class="font-bold text-[13px] text-slate-700 uppercase tracking-widest">${keyword}</span>
                            </div>
                            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-300 hs-accordion-active:rotate-180"></i>
                        </button>
                        <div id="${accordionId}-collapse" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" aria-labelledby="${accordionId}">
                            <div class="flex flex-col border-t border-slate-100">
                                ${itemsHtml}
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;
        if (badge) badge.innerText = totalItemsInCat;

        // Update overall total
        updateTotalItems();
    }

    function updateTotalItems() {
        const bahanCount = parseInt(document.querySelector('.badge-bahan')?.innerText || '0');
        const alatCount = parseInt(document.querySelector('.badge-alat')?.innerText || '0');
        const upahCount = parseInt(document.querySelector('.badge-upah')?.innerText || '0');
        const total = bahanCount + alatCount + upahCount;
        
        const totalEl = document.getElementById('total-items-text');
        if (totalEl) totalEl.innerText = `${total} item`;
    }

    function formatNumber(num) {
        return Number(num).toLocaleString('id-ID', { maximumFractionDigits: 2 });
    }

    searchInput.addEventListener('input', (e) => {
        window.renderListSDM(e.target.value);
    });

    // Render initially
    window.renderListSDM();
}
