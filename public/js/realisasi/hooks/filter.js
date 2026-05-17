import { getState, updateState } from '../core/state.js';
import { renderTable } from '../components/render.js';

function getIconContainer(button) {
    return button ? button.querySelector('.dropdown-icon') : null;
}

function updateCategoryIcon(button, isOpen, isSelected) {
    const iconContainer = getIconContainer(button);
    if (!iconContainer) return;

    if (isSelected) {
        iconContainer.innerHTML = `
            <svg class="w-4 h-4 opacity-100 text-white clear-filter-btn cursor-pointer hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        `;
    } else {
        iconContainer.innerHTML = `
            <svg class="w-4 h-4 opacity-70 ${isOpen ? 'rotate-180' : ''} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        `;
    }
}

export function getFilteredData() {
    const { selectedCategories, realisasiData } = getState();
    
    if (!selectedCategories || selectedCategories.length === 0) {
        return realisasiData; 
    }
    
    return realisasiData.filter(category => selectedCategories.includes(category.uraian));
}

function updateFilterUI() {
    const { selectedCategories, realisasiData } = getState();
    const filterLabel = document.getElementById('selectedCategory');
    const filterBtn = document.getElementById('categoryDropdownBtn');
    const filterMenu = document.getElementById('categoryDropdownMenu');
    
    if (!filterLabel || !filterBtn || !realisasiData) return;

    const totalCategories = realisasiData.length;
    const isSelected = selectedCategories.length > 0;
    const isOpen = filterMenu && !filterMenu.classList.contains('hidden');

    if (selectedCategories.length === 0) {
        filterLabel.textContent = 'Filter Kategori';
    } else if (selectedCategories.length === totalCategories) {
        filterLabel.textContent = 'Semua Kategori';
    } else if (selectedCategories.length === 1) {
        let label = selectedCategories[0];
        if (label.length > 15) label = label.substring(0, 15) + '...';
        filterLabel.textContent = label;
    } else {
        filterLabel.textContent = `${selectedCategories.length} Kategori`;
    }
    
    updateCategoryIcon(filterBtn, isOpen, isSelected);
    
    // Sync to mobile list label if any
    const mobileLabel = document.getElementById('mobileFilterLabel');
    if (mobileLabel) {
        mobileLabel.textContent = filterLabel.textContent;
    }
}

function clearAllCategories() {
    const checkboxes = Array.from(document.querySelectorAll('.category-checkbox, .mobile-category-checkbox'));
    const selectAllCb = document.getElementById('category-checkbox-all');
    const mobileSelectAllCb = document.getElementById('mobile-category-all');
    
    checkboxes.forEach(cb => cb.checked = false);
    if (selectAllCb) {
        selectAllCb.checked = false;
        selectAllCb.indeterminate = false;
    }
    if (mobileSelectAllCb) {
        mobileSelectAllCb.checked = false;
        mobileSelectAllCb.indeterminate = false;
    }
    
    updateState({ selectedCategories: [] });
    updateFilterUI();
    
    const tbody = document.getElementById('realisasi-tbody');
    if (tbody) {
        renderTable(getFilteredData(), tbody);
    }
}

export function initFilter() {
    const { realisasiData } = getState();
    const listContainer = document.getElementById('category-checkbox-list');
    
    if (!listContainer || !realisasiData || realisasiData.length === 0) {
        if (listContainer) {
            listContainer.innerHTML = '<span class="block px-4 py-2 text-sm text-gray-400 italic">Tidak ada kategori</span>';
        }
        return;
    }

    updateState({ selectedCategories: [] });

    const categories = realisasiData.map(item => item.uraian);
    const checkboxesHtml = categories.map(cat => `
        <label class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
            <input type="checkbox" class="category-checkbox w-4 h-4 border-gray-300 rounded focus:ring-slate-800 accent-slate-800 cursor-pointer" value="${cat}">
            <span class="truncate">${cat}</span>
        </label>
    `).join('');

    listContainer.innerHTML = checkboxesHtml;

    const mobileListContainer = document.getElementById('mobile-category-checkbox-list');
    if (mobileListContainer) {
        const mobileHtml = categories.map(cat => `
            <div class="flex items-center gap-2 py-1 px-1">
                <input type="checkbox" class="mobile-category-checkbox w-4 h-4 border-gray-300 rounded focus:ring-slate-800 accent-slate-800" value="${cat}">
                <label class="text-xs font-semibold text-slate-600 truncate">${cat}</label>
            </div>
        `).join('');
        mobileListContainer.innerHTML = mobileHtml;
    }
    
    const selectAllCb = document.getElementById('category-checkbox-all');
    if (selectAllCb) {
        selectAllCb.checked = false;
        selectAllCb.indeterminate = false;
    }
    
    updateFilterUI();
    
    _setupDropdownToggle();
    _setupMobileActionMenu(); 
    _bindFilterEvents();
}

function _setupMobileActionMenu() {
    const mobileBtn = document.getElementById('mobileActionBtn');
    const mobileMenu = document.getElementById('mobileActionMenu');

    if (!mobileBtn || !mobileMenu) return;

    mobileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileMenu.classList.toggle('hidden');
    });

    mobileMenu.addEventListener('click', (e) => {
        const target = e.target.closest('button, a');
        if (target && target.hasAttribute('data-hs-overlay')) {
            mobileMenu.classList.add('hidden');
        }
        e.stopPropagation();
    });

    document.addEventListener('click', (e) => {
        if (!mobileBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
            mobileMenu.classList.add('hidden');
        }
    });
}

function _setupDropdownToggle() {
    const filterBtn = document.getElementById('categoryDropdownBtn');
    const filterMenu = document.getElementById('categoryDropdownMenu');
    
    if (!filterBtn || !filterMenu) return;

    filterBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        
        if (e.target.closest('.clear-filter-btn')) {
            clearAllCategories();
            return;
        }

        filterMenu.classList.toggle('hidden');
        
        const { selectedCategories } = getState();
        const isSelected = selectedCategories.length > 0;
        const isOpen = !filterMenu.classList.contains('hidden');
        updateCategoryIcon(filterBtn, isOpen, isSelected);
    });

    filterMenu.addEventListener('click', (e) => {
        e.stopPropagation(); 
    });

    document.addEventListener('click', (e) => {
        if (!filterBtn.contains(e.target) && !filterMenu.contains(e.target)) {
            filterMenu.classList.add('hidden');
            const { selectedCategories } = getState();
            const isSelected = selectedCategories.length > 0;
            updateCategoryIcon(filterBtn, false, isSelected);
        }
    });
}

function syncCheckboxes() {
    const checkboxes = document.querySelectorAll('.category-checkbox, .mobile-category-checkbox');
    const selectAllCb = document.getElementById('category-checkbox-all');
    const mobileSelectAllCb = document.getElementById('mobile-category-all');
    
    const { selectedCategories } = getState();
    
    let allChecked = true;
    let anyChecked = false;
    
    checkboxes.forEach(cb => {
        const isChecked = selectedCategories.includes(cb.value);
        cb.checked = isChecked;
        if (!isChecked) allChecked = false;
        if (isChecked) anyChecked = true;
    });
    
    if (selectAllCb) {
        selectAllCb.checked = allChecked && checkboxes.length > 0;
        selectAllCb.indeterminate = anyChecked && !allChecked;
    }
    if (mobileSelectAllCb) {
        mobileSelectAllCb.checked = allChecked && checkboxes.length > 0;
        mobileSelectAllCb.indeterminate = anyChecked && !allChecked;
    }
}

function _bindFilterEvents() {
    const checkboxes = document.querySelectorAll('.category-checkbox, .mobile-category-checkbox');
    const selectAllCb = document.getElementById('category-checkbox-all');
    const mobileSelectAllCb = document.getElementById('mobile-category-all');
    const tbody = document.getElementById('realisasi-tbody');
    
    if (!checkboxes.length || !tbody) return;

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const selected = Array.from(checkboxes)
                                 .filter(c => c.checked)
                                 .map(c => c.value);
            
            updateState({ selectedCategories: selected });
            syncCheckboxes();
            updateFilterUI();
            
            renderTable(getFilteredData(), tbody);
        });
    });

    if (selectAllCb) {
        selectAllCb.addEventListener('change', handleSelectAll);
    }
    if (mobileSelectAllCb) {
        mobileSelectAllCb.addEventListener('change', handleSelectAll);
    }

    function handleSelectAll(e) {
        const isChecked = e.target.checked;
        
        checkboxes.forEach(c => {
            c.checked = isChecked;
        });

        const selected = isChecked ? Array.from(document.querySelectorAll('.category-checkbox')).map(c => c.value) : [];
        
        updateState({ selectedCategories: selected });
        syncCheckboxes();
        updateFilterUI();
        
        renderTable(getFilteredData(), tbody);
    }
}
