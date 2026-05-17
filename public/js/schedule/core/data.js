import { updateState, getState } from './state.js';

function generateDynamicTimeline(earliestDate = null, latestDate = null) {
    const config = [];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    let startYear = earliestDate ? earliestDate.getFullYear() : new Date().getFullYear();
    let startMonth = earliestDate ? earliestDate.getMonth() : 0;
    
    let endYear = latestDate ? latestDate.getFullYear() : new Date().getFullYear();
    let endMonth = latestDate ? latestDate.getMonth() : 11;

    let currYear = startYear;
    let currMonth = startMonth;

    while (currYear < endYear || (currYear === endYear && currMonth <= endMonth)) {
        config.push({ 
            monthName: `${months[currMonth]} ${currYear}`, 
            month: currMonth, 
            year: currYear, 
            weeks: ['W1', 'W2', 'W3', 'W4'] 
        });
        
        currMonth++;
        if (currMonth > 11) {
            currMonth = 0;
            currYear++;
        }
    }

    return config;
}

export function initTimelineConfig() {
    const timelineConfig = generateDynamicTimeline(null, null);

    const globalStartDate = new Date(timelineConfig[0].year, timelineConfig[0].month, 1);
    const lastTl = timelineConfig[timelineConfig.length - 1];
    const globalEndDate = new Date(lastTl.year, lastTl.month + 1, 0, 23, 59, 59);
    const totalTimelineDays = Math.ceil((globalEndDate - globalStartDate) / (1000 * 60 * 60 * 24));
    const totalWeeksCount = timelineConfig.reduce((acc, curr) => acc + curr.weeks.length, 0);

    updateState({
        timelineConfig,
        globalStartDate,
        globalEndDate,
        totalTimelineDays,
        totalWeeksCount
    });
}

export async function fetchScheduleData() {
    if (!window.SCHEDULE_INIT || !window.SCHEDULE_INIT.idProject) {
        console.error('No project ID found');
        return [];
    }

    try {
        const url = new URL(window.SCHEDULE_INIT.apiScheduleDataUrl);
        url.searchParams.append('id_project', window.SCHEDULE_INIT.idProject);
        
        const response = await fetch(url);
        const json = await response.json();
        
        if (json.status !== 'success') {
            throw new Error(json.message || 'Failed to fetch Schedule');
        }

        const data = json.data.categories.map(cat => {
            return {
                id: cat.id,
                kategori: cat.name,
                start_date: null,
                finish_date: null, 
                items: cat.items.map(function mapItem(item) {
                    return {
                        id: item.id_rap_detail,
                        nama: item.uraian,
                        start_date: item.start_date,
                        finish_date: item.finish_date,
                        weight: item.weight || 0, 
                        children: item.children ? item.children.map(mapItem) : []
                    };
                })
            };
        });

        updateState({
            schedules: JSON.parse(JSON.stringify(data))
        });

        refreshCategoryMetadata();

        return getState().schedules;
    } catch (e) {
        console.error(e);
        return [];
    }
}

export function calculateDuration(startStr, finishStr) {
    if (!startStr || !finishStr) return 0;
    const d1 = new Date(startStr);
    const d2 = new Date(finishStr);
    if (isNaN(d1) || isNaN(d2)) return 0;
    const diffTime = d2.getTime() - d1.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays >= 0 ? diffDays + 1 : 0;
}

export function refreshCategoryMetadata(changedSourceId = null) {
    const { schedules } = getState();
    
    let globalEarliest = null;
    let globalLatest = null;

    schedules.forEach(cat => {
        function processItemStats(item, idx, prefix = "") {
            item.displayNumber = prefix ? `${prefix}${idx + 1}` : `${idx + 1}`;
            
            if (item.children && item.children.length > 0) {
                let childrenSum = 0;
                item.children.forEach((child, cIdx) => {
                    processItemStats(child, cIdx, item.displayNumber + ".");
                    childrenSum += (child.duration || 0);
                });
                item.duration = childrenSum;
            } else {
                item.duration = calculateDuration(item.start_date, item.finish_date);
            }
        }
        cat.items.forEach((item, idx) => processItemStats(item, idx, ""));

        const isCategoryEdit = (changedSourceId && changedSourceId === cat.id);
        
        let totalWeight = 0;
        cat.items.forEach(item => totalWeight += (item.weight || 0));
        cat.weight = totalWeight;

        if (!isCategoryEdit) {
            let earliest = null;
            let latest = null;

            function processItemDates(item) {
                if (item.start_date) {
                    const sd = new Date(item.start_date);
                    if (!earliest || sd < earliest) earliest = sd;
                }
                if (item.finish_date) {
                    const fd = new Date(item.finish_date);
                    if (!latest || fd > latest) latest = fd;
                }
                if (item.children && item.children.length > 0) {
                    item.children.forEach(processItemDates);
                }
            }

            cat.items.forEach(processItemDates);

            if (earliest) cat.start_date = earliest.toISOString().split('T')[0];
            if (latest) cat.finish_date = latest.toISOString().split('T')[0];
        }
        let totalCatDuration = 0;
        function sumDurations(items) {
            items.forEach(item => {
                if (item.children && item.children.length > 0) {
                    sumDurations(item.children);
                } else {
                    totalCatDuration += (item.duration || 0);
                }
            });
        }
        sumDurations(cat.items);
        cat.duration = totalCatDuration;
        
        if (cat.start_date) {
            let sd = new Date(cat.start_date);
            if (!globalEarliest || sd < globalEarliest) globalEarliest = sd;
        }
        if (cat.finish_date) {
            let fd = new Date(cat.finish_date);
            if (!globalLatest || fd > globalLatest) globalLatest = fd;
        }
    });

    const timelineConfig = generateDynamicTimeline(globalEarliest, globalLatest);

    const globalStartDate = new Date(timelineConfig[0].year, timelineConfig[0].month, 1);
    const lastTl = timelineConfig[timelineConfig.length - 1];
    const globalEndDate = new Date(lastTl.year, lastTl.month + 1, 0, 23, 59, 59);

    const totalTimelineDays = Math.ceil((globalEndDate - globalStartDate) / (1000 * 60 * 60 * 24));
    const totalWeeksCount = timelineConfig.reduce((acc, curr) => acc + curr.weeks.length, 0);

    updateState({ schedules, timelineConfig, globalStartDate, globalEndDate, totalTimelineDays, totalWeeksCount });
}
