import { getState, updateState } from '../core/state.js';
import { renderSDMTable } from '../components/render.js';

export function getFilteredSDMData() {
    const { sdmData, sdmFilterStart, sdmFilterEnd } = getState();
    
    if (!sdmFilterStart && !sdmFilterEnd) {
        return sdmData;
    }

    return sdmData.filter(item => {
        const itemDate = new Date(item.tanggal);
        itemDate.setHours(0, 0, 0, 0);

        let isAfterStart = true;
        let isBeforeEnd = true;

        if (sdmFilterStart) {
            const start = new Date(sdmFilterStart);
            start.setHours(0, 0, 0, 0);
            isAfterStart = itemDate >= start;
        }

        if (sdmFilterEnd) {
            const end = new Date(sdmFilterEnd);
            end.setHours(0, 0, 0, 0);
            isBeforeEnd = itemDate <= end;
        }

        return isAfterStart && isBeforeEnd;
    });
}

export function initSDMFilter() {
    const startInput = document.getElementById('sdm-filter-start');
    const endInput = document.getElementById('sdm-filter-end');
    const clearBtn = document.getElementById('sdm-filter-clear');
    
    const mobileStartInput = document.getElementById('mobile-sdm-start');
    const mobileEndInput = document.getElementById('mobile-sdm-end');
    const mobileClearBtn = document.getElementById('mobile-sdm-clear');

    const tbody = document.getElementById('realisasi-sdm-tbody');
    
    if (!startInput || !endInput || !clearBtn || !tbody) return;

    const updateFilter = (e) => {
        const start = e && (e.target.id.includes('mobile') || e.target.id === 'sdm-filter-start' || e.target.id === 'sdm-filter-end') ? 
                      (e.target.id.includes('mobile') ? mobileStartInput.value : startInput.value) : startInput.value;
        
        const valStart = e ? (e.target.id.includes('start') ? e.target.value : null) : null;
        const valEnd = e ? (e.target.id.includes('end') ? e.target.value : null) : null;

        if (e) {
            if (e.target.id === 'sdm-filter-start' && mobileStartInput) mobileStartInput.value = e.target.value;
            if (e.target.id === 'mobile-sdm-start' && startInput) startInput.value = e.target.value;
            if (e.target.id === 'sdm-filter-end' && mobileEndInput) mobileEndInput.value = e.target.value;
            if (e.target.id === 'mobile-sdm-end' && endInput) endInput.value = e.target.value;
        }

        const currentStart = startInput.value;
        const currentEnd = endInput.value;
        
        updateState({ sdmFilterStart: currentStart || null, sdmFilterEnd: currentEnd || null });
        
        const hasFilter = currentStart || currentEnd;
        if (hasFilter) {
            clearBtn.classList.remove('hidden');
            clearBtn.classList.add('flex');
            if (mobileClearBtn) mobileClearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
            clearBtn.classList.remove('flex');
            if (mobileClearBtn) mobileClearBtn.classList.add('hidden');
        }

        renderSDMTable(getFilteredSDMData(), tbody);
    };

    startInput.addEventListener('change', updateFilter);
    endInput.addEventListener('change', updateFilter);
    if (mobileStartInput) mobileStartInput.addEventListener('change', updateFilter);
    if (mobileEndInput) mobileEndInput.addEventListener('change', updateFilter);

    const clearAll = (e) => {
        if (e) e.stopPropagation();
        startInput.value = '';
        endInput.value = '';
        if (mobileStartInput) mobileStartInput.value = '';
        if (mobileEndInput) mobileEndInput.value = '';
        updateFilter();
    };

    clearBtn.addEventListener('click', clearAll);
    if (mobileClearBtn) mobileClearBtn.addEventListener('click', clearAll);

    _setupMobileActionMenuSDM();
}

function _setupMobileActionMenuSDM() {
    const mobileBtn = document.getElementById('mobileActionBtnSDM');
    const mobileMenu = document.getElementById('mobileActionMenuSDM');

    if (!mobileBtn || !mobileMenu) return;

    mobileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileMenu.classList.toggle('hidden');
    });

    // Close menu when any action inside it is clicked
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
