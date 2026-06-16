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

    const uniqueCategories = new Set(Array.from(document.querySelectorAll('.category-checkbox')).map(c => c.value));
    const totalCategories = uniqueCategories.size;
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
    updateState({ selectedCategories: [] });

    const selectAllCb = document.getElementById('category-checkbox-all');
    if (selectAllCb) {
        selectAllCb.checked = false;
        selectAllCb.indeterminate = false;
    }
    const mobileSelectAllCb = document.getElementById('mobile-category-all');
    if (mobileSelectAllCb) {
        mobileSelectAllCb.checked = false;
        mobileSelectAllCb.indeterminate = false;
    }
    
    // Uncheck all category checkboxes initially since selectedCategories is empty
    const checkboxes = document.querySelectorAll('.category-checkbox, .mobile-category-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    
    updateFilterUI();
    
    _setupDropdownToggle();
    _setupMobileActionMenu(); 
    _bindFilterEvents();
}

function _setupMobileActionMenu() {
    const mobileBtn = document.getElementById('mobileActionBtn');
    const mobileMenu = document.getElementById('mobileActionMenu');
    const mobCategoryBtn = document.getElementById('mobileCategoryBtn');
    const mobCategoryMenu = document.getElementById('mobileCategoryMenu');

    if (!mobileBtn || !mobileMenu) return;

    mobileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileMenu.classList.toggle('hidden');
    });

    if (mobCategoryBtn && mobCategoryMenu) {
        mobCategoryBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            mobCategoryMenu.classList.toggle('hidden');
            const icon = mobCategoryBtn.querySelector('.dropdown-icon svg');
            if (icon) {
                icon.classList.toggle('rotate-180', !mobCategoryMenu.classList.contains('hidden'));
            }
        });
    }

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
            if (mobCategoryMenu) {
                mobCategoryMenu.classList.add('hidden');
            }
            if (mobCategoryBtn) {
                const icon = mobCategoryBtn.querySelector('.dropdown-icon svg');
                if (icon) icon.classList.remove('rotate-180');
            }
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
        cb.addEventListener('change', (e) => {
            const isChecked = e.target.checked;
            const value = e.target.value;
            let { selectedCategories } = getState();
            
            if (isChecked) {
                if (!selectedCategories.includes(value)) {
                    selectedCategories = [...selectedCategories, value];
                }
            } else {
                selectedCategories = selectedCategories.filter(v => v !== value);
            }
            
            updateState({ selectedCategories });
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
        
        let selected = [];
        if (isChecked) {
            const allValues = Array.from(document.querySelectorAll('.category-checkbox')).map(c => c.value);
            selected = [...new Set(allValues)];
        }
        
        updateState({ selectedCategories: selected });
        syncCheckboxes();
        updateFilterUI();
        
        renderTable(getFilteredData(), tbody);
    }
}
