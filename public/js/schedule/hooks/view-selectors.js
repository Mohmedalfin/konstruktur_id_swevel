import { getState, updateState } from '../core/state.js';
import { applyAllFilters } from '../core/filters.js';
import { renderBody } from '../components/render.js';
import { renderSCurveChart, hideSCurveChart } from '../components/chart-render.js';

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
            <svg class="w-4 h-4 opacity-70 ${isOpen ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        `;
    }
}

function updateCategoryUI(categoryLabel, mobileCategoryLabel, categoryBtn, mobileCategoryBtn, defaultLabel) {
    const { filters } = getState();
    const categories = filters.categories || [];
    let displayValue = defaultLabel;
    
    if (categories.length === 1) {
        displayValue = categories[0];
        if (displayValue.length > 15) displayValue = displayValue.substring(0, 15) + '...';
    } else if (categories.length > 1) {
        displayValue = `${categories.length} Kategori`;
    }

    const isSelected = categories.length > 0;

    if (categoryLabel) categoryLabel.textContent = displayValue;
    if (mobileCategoryLabel) mobileCategoryLabel.textContent = displayValue;

    if (categoryBtn) updateCategoryIcon(categoryBtn, false, isSelected);
    if (mobileCategoryBtn) updateCategoryIcon(mobileCategoryBtn, false, isSelected);
}

function initCategoryDropdown() {
    const categoryBtn = document.getElementById('categoryDropdownBtn');
    const categoryMenu = document.getElementById('categoryDropdownMenu');
    const categoryLabel = document.getElementById('selectedCategory');

    const mobileCategoryBtn = document.getElementById('mobileCategoryBtn');
    const mobileCategoryMenu = document.getElementById('mobileCategoryMenu');
    const mobileCategoryLabel = document.getElementById('mobileSelectedCategory');

    const defaultLabel = 'Filter Kategori';

    function toggleMenu(menu, button) {
        if (!menu) return;
        const { filters } = getState();
        const isHidden = menu.classList.contains('hidden');
        const isSelected = filters.categories && filters.categories.length > 0;
        
        if (isHidden) {
            menu.classList.remove('hidden');
            updateCategoryIcon(button, true, isSelected);
        } else {
            menu.classList.add('hidden');
            updateCategoryIcon(button, false, isSelected);
        }
    }

    function closeMenu(menu, button) {
        if (!menu) return;
        const { filters } = getState();
        const isSelected = filters.categories && filters.categories.length > 0;
        menu.classList.add('hidden');
        updateCategoryIcon(button, false, isSelected);
    }

    function syncCheckboxes(checkboxes, selectAllCheckbox, selectedCategories) {
        if (!checkboxes.length) return;
        
        let allChecked = true;
        let anyChecked = false;
        
        checkboxes.forEach(cb => {
            const isChecked = selectedCategories.includes(cb.value);
            cb.checked = isChecked;
            if (!isChecked) allChecked = false;
            if (isChecked) anyChecked = true;
        });
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allChecked && checkboxes.length > 0;
            selectAllCheckbox.indeterminate = anyChecked && !allChecked;
        }
    }

    function handleCheckboxChange(e) {
        const isMobile = e.target.classList.contains('mobile-category-checkbox') || e.target.classList.contains('mobile-category-checkbox-all');
        const checkboxesClass = isMobile ? '.mobile-category-checkbox' : '.category-checkbox';
        const checkboxes = Array.from(document.querySelectorAll(checkboxesClass));
        
        const selectedCategories = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
        
        const currentFilters = getState().filters;
        updateState({
            filters: { ...currentFilters, categories: selectedCategories }
        });
        
        updateCategoryUI(categoryLabel, mobileCategoryLabel, categoryBtn, mobileCategoryBtn, defaultLabel);
        applyAllFilters();
        
        const otherCheckboxesClass = isMobile ? '.category-checkbox' : '.mobile-category-checkbox';
        const otherCheckboxes = Array.from(document.querySelectorAll(otherCheckboxesClass));
        const otherSelectAll = document.querySelector(isMobile ? '.category-checkbox-all' : '.mobile-category-checkbox-all');
        const thisSelectAll = document.querySelector(isMobile ? '.mobile-category-checkbox-all' : '.category-checkbox-all');
        
        syncCheckboxes(otherCheckboxes, otherSelectAll, selectedCategories);
        syncCheckboxes(checkboxes, thisSelectAll, selectedCategories);
    }

    function setupCheckboxes(menu, checkboxClass, selectAllClass) {
        if (!menu) return;
        const checkboxes = menu.querySelectorAll(`.${checkboxClass}`);
        const selectAll = menu.querySelector(`.${selectAllClass}`);
        
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                checkboxes.forEach(cb => cb.checked = isChecked);
                handleCheckboxChange({ target: selectAll });
            });
        }
        
        checkboxes.forEach(cb => {
            cb.addEventListener('change', handleCheckboxChange);
        });
    }

    setupCheckboxes(categoryMenu, 'category-checkbox', 'category-checkbox-all');
    setupCheckboxes(mobileCategoryMenu, 'mobile-category-checkbox', 'mobile-category-checkbox-all');

    function clearAllCategories() {
        const checkboxes = Array.from(document.querySelectorAll('.category-checkbox, .mobile-category-checkbox'));
        checkboxes.forEach(cb => cb.checked = false);
        
        const currentFilters = getState().filters;
        updateState({
            filters: { ...currentFilters, categories: [] }
        });
        
        updateCategoryUI(categoryLabel, mobileCategoryLabel, categoryBtn, mobileCategoryBtn, defaultLabel);
        applyAllFilters();
        
        const selectAlls = document.querySelectorAll('.category-checkbox-all, .mobile-category-checkbox-all');
        selectAlls.forEach(sa => {
            sa.checked = false;
            sa.indeterminate = false;
        });
    }

    if (categoryBtn) {
        categoryBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (e.target.closest('.clear-filter-btn')) {
                clearAllCategories();
                return;
            }
            toggleMenu(categoryMenu, categoryBtn);
        });
    }

    if (mobileCategoryBtn) {
        mobileCategoryBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (e.target.closest('.clear-filter-btn')) {
                clearAllCategories();
                return;
            }
            toggleMenu(mobileCategoryMenu, mobileCategoryBtn);
        });
    }

    if (categoryMenu) {
        categoryMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
    
    if (mobileCategoryMenu) {
        mobileCategoryMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    document.addEventListener('click', (e) => {
        if (categoryBtn && categoryMenu && !categoryBtn.contains(e.target) && !categoryMenu.contains(e.target)) {
            closeMenu(categoryMenu, categoryBtn);
        }
        if (mobileCategoryBtn && mobileCategoryMenu && !mobileCategoryBtn.contains(e.target) && !mobileCategoryMenu.contains(e.target)) {
            closeMenu(mobileCategoryMenu, mobileCategoryBtn);
        }
    });
}

function initMobileActionMenu() {
    const mobileActionBtn = document.getElementById('mobileActionBtn');
    const mobileActionMenu = document.getElementById('mobileActionMenu');
    const mobileCategoryMenu = document.getElementById('mobileCategoryMenu');
    const mobileCategoryBtn = document.getElementById('mobileCategoryBtn');

    if (mobileActionBtn && mobileActionMenu) {
        mobileActionBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            mobileActionMenu.classList.toggle('hidden');
            
            if (mobileCategoryMenu && !mobileCategoryMenu.classList.contains('hidden')) {
                mobileCategoryMenu.classList.add('hidden');
                const isSelected = getState().filters.categories && getState().filters.categories.length > 0;
                updateCategoryIcon(mobileCategoryBtn, false, isSelected);
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (mobileActionBtn && mobileActionMenu && !mobileActionBtn.contains(e.target) && !mobileActionMenu.contains(e.target)) {
            mobileActionMenu.classList.add('hidden');
        }
    });
}

function initDateSync() {
    const filterStartDate = document.getElementById('filterStartDate');
    const filterEndDate = document.getElementById('filterEndDate');
    const mobileFilterStartDate = document.getElementById('mobileFilterStartDate');
    const mobileFilterEndDate = document.getElementById('mobileFilterEndDate');

    function updateDateState(type, value) {
        const currentFilters = getState().filters;
        
        if (type === 'start') {
            updateState({
                filters: { ...currentFilters, startDate: value }
            });
            if (filterStartDate && filterStartDate.value !== value) filterStartDate.value = value;
            if (mobileFilterStartDate && mobileFilterStartDate.value !== value) mobileFilterStartDate.value = value;
        } else if (type === 'end') {
            updateState({
                filters: { ...currentFilters, endDate: value }
            });
            if (filterEndDate && filterEndDate.value !== value) filterEndDate.value = value;
            if (mobileFilterEndDate && mobileFilterEndDate.value !== value) mobileFilterEndDate.value = value;
        }
        
        document.dispatchEvent(new CustomEvent('scheduleDateFilterChanged', { 
            detail: { type, value: getState().filters } 
        }));

        applyAllFilters();
        
        const clearBtn = document.getElementById('schedule-filter-clear');
        if (clearBtn) {
            if (getState().filters.startDate || getState().filters.endDate) {
                clearBtn.classList.remove('hidden');
                clearBtn.classList.add('flex');
            } else {
                clearBtn.classList.add('hidden');
                clearBtn.classList.remove('flex');
            }
        }
    }

    const clearBtn = document.getElementById('schedule-filter-clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (filterStartDate) filterStartDate.value = '';
            if (filterEndDate) filterEndDate.value = '';
            if (mobileFilterStartDate) mobileFilterStartDate.value = '';
            if (mobileFilterEndDate) mobileFilterEndDate.value = '';
            
            const currentFilters = getState().filters;
            updateState({
                filters: { ...currentFilters, startDate: '', endDate: '' }
            });
            
            document.dispatchEvent(new CustomEvent('scheduleDateFilterChanged', { 
                detail: { type: 'clear', value: getState().filters } 
            }));

            applyAllFilters();
            
            clearBtn.classList.add('hidden');
            clearBtn.classList.remove('flex');
        });
    }

    if (filterStartDate) {
        filterStartDate.addEventListener('change', (e) => updateDateState('start', e.target.value));
    }
    if (mobileFilterStartDate) {
        mobileFilterStartDate.addEventListener('change', (e) => updateDateState('start', e.target.value));
    }
    
    if (filterEndDate) {
        filterEndDate.addEventListener('change', (e) => updateDateState('end', e.target.value));
    }
    if (mobileFilterEndDate) {
        mobileFilterEndDate.addEventListener('change', (e) => updateDateState('end', e.target.value));
    }
}

function initViewSwitcher() {
    const desktopBtn = document.getElementById('viewModeDropdownBtn');
    const desktopMenu = document.getElementById('viewModeDropdownMenu');
    const desktopLabel = document.getElementById('selectedViewMode');

    const mobileBtn = document.getElementById('mobileViewModeBtn');
    const mobileMenu = document.getElementById('mobileViewModeMenu');
    const mobileLabel = document.getElementById('mobileSelectedViewMode');

    const mobileActionMenu = document.getElementById('mobileActionMenu');

    function toggleMenu(menu, btn, iconSelector) {
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (btn && btn.querySelector(iconSelector)) {
                btn.querySelector(iconSelector).innerHTML = `<svg class="w-4 h-4 opacity-70 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
            }
        } else {
            closeMenu(menu, btn, iconSelector);
        }
    }

    function closeMenu(menu, btn, iconSelector) {
        if (!menu) return;
        menu.classList.add('hidden');
        if (btn && btn.querySelector(iconSelector)) {
            btn.querySelector(iconSelector).innerHTML = `<svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
        }
    }

    function setupDropdown(btn, menu, iconSelector, isMobile) {
        if (!btn || !menu) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu(menu, btn, iconSelector);
            
            if (isMobile) {
                const mobileCategoryMenu = document.getElementById('mobileCategoryMenu');
                const mobileCategoryBtn = document.getElementById('mobileCategoryBtn');
                if (mobileCategoryMenu && !mobileCategoryMenu.classList.contains('hidden')) {
                    mobileCategoryMenu.classList.add('hidden');
                    if (mobileCategoryBtn && mobileCategoryBtn.querySelector('.dropdown-icon')) {
                        const { filters } = getState();
                        const isSelected = filters.categories && filters.categories.length > 0;
                        updateCategoryIcon(mobileCategoryBtn, false, isSelected);
                    }
                }
            }
        });

        menu.addEventListener('click', (e) => {
            const option = e.target.closest('button[data-value]');
            if (!option) return;
            
            e.stopPropagation();
            const newMode = option.dataset.value;
            const text = option.textContent.trim();
            
            if (desktopLabel) desktopLabel.textContent = text;
            if (mobileLabel) mobileLabel.textContent = text;
            
            document.querySelectorAll('.viewmode-option, .mobile-viewmode-option').forEach(opt => {
                if (opt.dataset.value === newMode) {
                    opt.classList.remove('text-slate-600');
                    opt.classList.add('font-semibold', 'text-slate-800');
                } else {
                    opt.classList.remove('font-semibold', 'text-slate-800');
                    opt.classList.add('text-slate-600');
                }
            });

            updateState({ viewMode: newMode });
            renderBody();

            if (newMode === 's-curve') {
                requestAnimationFrame(() => renderSCurveChart());
            } else {
                hideSCurveChart();
            }
            
            closeMenu(menu, btn, iconSelector);

            if (isMobile && mobileActionMenu) {
                mobileActionMenu.classList.add('hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                closeMenu(menu, btn, iconSelector);
            }
        });
    }

    setupDropdown(desktopBtn, desktopMenu, '.viewmode-dropdown-icon', false);
    setupDropdown(mobileBtn, mobileMenu, '.mobile-viewmode-dropdown-icon', true);
}

export function initFilterControls() {
    initCategoryDropdown();
    initMobileActionMenu();
    initDateSync();
    initViewSwitcher();
}