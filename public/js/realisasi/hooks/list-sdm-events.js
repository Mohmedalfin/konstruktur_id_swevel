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
        
        if (!container || !emptyState) return;

        container.innerHTML = '';
        
        const keys = Object.keys(group).sort();
        if (keys.length === 0) {
            container.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        container.classList.remove('hidden');
        emptyState.classList.add('hidden');

        keys.forEach((keyword, index) => {
            const items = group[keyword];
            const accordionId = `accordion-${categoryId}-${index}`;
            
            const renderItemRow = (item, isInside) => {
                const paddingClass = isInside ? "py-2.5 pl-8 pr-4 sm:pl-10 sm:pr-5" : "py-3 px-4 sm:px-5";
                
                return `
                    <div class="grid grid-cols-1 md:grid-cols-12 items-center ${paddingClass} border-b border-slate-100 last:border-0 hover:bg-slate-50/80 transition-colors gap-y-2 group">
                        <div class="md:col-span-6 font-bold text-slate-700 text-[13px] pr-2 truncate">
                            ${item.nama_item}
                        </div>
                        
                        <div class="md:col-span-6 flex items-center justify-end text-xs">
                            <div class="px-3 text-right whitespace-nowrap">
                                <span class="text-slate-400">Kebutuhan:</span> 
                                <span class="font-bold text-indigo-600 ml-1 inline-block min-w-[50px] text-right">${formatNumber(item.qty_budget)} ${item.satuan}</span>
                            </div>
                            <div class="pl-3 border-l border-slate-200 text-right whitespace-nowrap">
                                <span class="text-slate-400">Sisa:</span> 
                                <span class="font-bold ${item.qty_sisa < 0 ? 'text-red-600' : 'text-emerald-600'} ml-1 inline-block min-w-[50px] text-right">${formatNumber(item.qty_sisa)} ${item.satuan}</span>
                            </div>
                        </div>
                    </div>
                `;
            };

            if (items.length === 1) {
                // Single item, no accordion wrapper
                container.insertAdjacentHTML('beforeend', renderItemRow(items[0], false));
            } else {
                // Accordion wrapper
                const itemsHtml = items.map(item => renderItemRow(item, true)).join('');
                
                const html = `
                    <div class="hs-accordion border-b border-slate-200 last:border-0" id="${accordionId}">
                        <button class="hs-accordion-toggle w-full py-3 px-4 sm:px-5 inline-flex items-center justify-between gap-x-3 text-sm font-bold text-slate-800 text-left hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:pointer-events-none" aria-controls="${accordionId}-collapse">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[11px]">${items.length}</span>
                                </div>
                                <span class="uppercase tracking-wide text-[13px]">${keyword}</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-400 text-xs transition-transform duration-300 hs-accordion-active:rotate-90"></i>
                        </button>
                        <div id="${accordionId}-collapse" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" aria-labelledby="${accordionId}">
                            <div class="pb-2 flex flex-col border-t border-slate-50 bg-[#fafafa]">
                                ${itemsHtml}
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            }
        });
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
