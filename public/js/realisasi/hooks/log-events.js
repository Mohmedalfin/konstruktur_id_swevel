import { getState, updateState } from '../core/state.js';
import { getAllLogsRecursive, groupLogsByDate } from '../core/helpers.js';
import { renderLogTimeline } from '../components/render.js';

export function initLogEvents() {
    const filterStart = document.getElementById('log-filter-start');
    const filterEnd = document.getElementById('log-filter-end');
    const filterClear = document.getElementById('log-filter-clear');
    const openBtns = document.querySelectorAll('[data-hs-overlay="#modal-log-dokumentasi"]');
    
    openBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            _refreshTimeline();
        });
    });

    const updateFilterState = () => {
        updateState({
            logFilterStart: filterStart ? filterStart.value : '',
            logFilterEnd: filterEnd ? filterEnd.value : ''
        });

        if (filterClear) {
            if (getState().logFilterStart || getState().logFilterEnd) {
                filterClear.classList.remove('hidden');
                filterClear.classList.add('flex');
            } else {
                filterClear.classList.add('hidden');
                filterClear.classList.remove('flex');
            }
        }

        _refreshTimeline();
    };

    if (filterStart) {
        filterStart.addEventListener('change', updateFilterState);
    }

    if (filterEnd) {
        filterEnd.addEventListener('change', updateFilterState);
    }

    if (filterClear) {
        filterClear.addEventListener('click', () => {
            if (filterStart) filterStart.value = '';
            if (filterEnd) filterEnd.value = '';
            updateFilterState();
        });
    }
}

function _refreshTimeline() {
    const container = document.getElementById('log-timeline-container');
    if (!container) return;

    const { realisasiData, logFilterStart, logFilterEnd } = getState();
    const flatLogs = getAllLogsRecursive(realisasiData);

    let filteredLogs = flatLogs;

    if (logFilterStart || logFilterEnd) {
        const start = logFilterStart ? new Date(logFilterStart) : new Date('1900-01-01');
        const end = logFilterEnd ? new Date(logFilterEnd) : new Date('2100-01-01');
        
        start.setHours(0, 0, 0, 0);
        end.setHours(23, 59, 59, 999);

        filteredLogs = flatLogs.filter(log => {
            let logDateStr = log.tanggal;
            let logDate;
            
            if (logDateStr.includes('-')) {
                const parts = logDateStr.split('-');
                if (parts[0].length === 4) {
                    logDate = new Date(`${parts[0]}-${parts[1]}-${parts[2]}T00:00:00`);
                } else {
                    logDate = new Date(`${parts[2]}-${parts[1]}-${parts[0]}T00:00:00`);
                }
            } else {
                return true;
            }

            return logDate >= start && logDate <= end;
        });
    }

    const groupedLogs = groupLogsByDate(filteredLogs);
    renderLogTimeline(groupedLogs, container);
}
