import { updateState, getState } from './state.js';

// Dummy Data
const DUMMY_SCHEDULES = [
    {
        id: 'c1',
        kategori: 'PEKERJAAN PERSIAPAN',
        start_date: '2024-02-01',
        finish_date: '2024-02-15',
        items: [
            { id: 't1', nama: 'Mobilisasi Alat & Tenaga', start_date: '2024-02-01', finish_date: '2024-02-03', weight: 1.5 },
            { id: 't2', nama: 'Pembuatan Direksi Keet', start_date: '2024-02-01', finish_date: '2024-02-07', weight: 2.0 },
            { id: 't3', nama: 'Pemasangan Papan Nama Proyek', start_date: '2024-02-08', finish_date: '2024-02-09', weight: 0.5 },
            { id: 't4', nama: 'Pemasangan Bouwplank', start_date: '2024-02-10', finish_date: '2024-02-15', weight: 1.0 }
        ]
    },
    {
        id: 'c2',
        kategori: 'PEKERJAAN STRUKTURAL',
        start_date: '2024-02-16',
        finish_date: '2024-03-30',
        items: [
            { id: 't5', nama: 'Pekerjaan Pondasi', start_date: '2024-02-16', finish_date: '2024-02-28', weight: 15.0 },
            { id: 't6', nama: 'Pekerjaan Sloof', start_date: '2024-03-01', finish_date: '2024-03-10', weight: 10.0 },
            { id: 't7', nama: 'Pekerjaan Kolom', start_date: '2024-03-11', finish_date: '2024-03-20', weight: 15.0 },
            { id: 't8', nama: 'Pekerjaan Balok & Plat Lantai', start_date: '2024-03-21', finish_date: '2024-03-30', weight: 20.0 }
        ]
    },
    {
        id: 'c3',
        kategori: 'PEKERJAAN ARSITEKTURAL',
        start_date: '2024-04-01',
        finish_date: '2024-04-15',
        items: [
            { id: 't9', nama: 'Pekerjaan Pasangan Bata', start_date: '2024-04-01', finish_date: '2024-04-15', weight: 10.0 }
        ]
    },
    {
        id: 'c4',
        kategori: 'PEKERJAAN MEP',
        start_date: '2024-04-16',
        finish_date: '2024-05-15',
        items: [
            { id: 't10', nama: 'Instalasi Listrik', start_date: '2024-04-16', finish_date: '2024-04-30', weight: 5.0 },
            { id: 't11', nama: 'Instalasi Air Bersih & Kotor', start_date: '2024-05-01', finish_date: '2024-05-15', weight: 5.0 }
        ]
    }
];

const DUMMY_TIMELINE_CONFIG = [
    { monthName: 'Januari', month: 0, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Februari', month: 1, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Maret',    month: 2, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'April',    month: 3, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Mei',      month: 4, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Juni',      month: 5, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Juli',      month: 6, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Agustus',      month: 7, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'September',      month: 8, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Oktober',      month: 9, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'November',      month: 10, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
    { monthName: 'Desember',      month: 11, year: 2024, weeks: ['W1', 'W2', 'W3', 'W4'] },
];

export async function fetchScheduleData() {
    await new Promise(resolve => setTimeout(resolve, 500));
    
    const data = DUMMY_SCHEDULES;
    const timelineConfig = DUMMY_TIMELINE_CONFIG;

    const globalStartDate = new Date(timelineConfig[0].year, timelineConfig[0].month, 1);
    const lastTl = timelineConfig[timelineConfig.length - 1];
    const globalEndDate = new Date(lastTl.year, lastTl.month + 1, 0, 23, 59, 59);
    const totalTimelineDays = Math.ceil((globalEndDate - globalStartDate) / (1000 * 60 * 60 * 24));
    const totalWeeksCount = timelineConfig.reduce((acc, curr) => acc + curr.weeks.length, 0);

    updateState({
        schedules: JSON.parse(JSON.stringify(data)),
        timelineConfig,
        globalStartDate,
        globalEndDate,
        totalTimelineDays,
        totalWeeksCount
    });

    return data;
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
    
    schedules.forEach(cat => {
        cat.items.forEach(item => {
            item.duration = calculateDuration(item.start_date, item.finish_date);
        });

        const isCategoryEdit = (changedSourceId && changedSourceId === cat.id);
        
        let totalWeight = 0;
        cat.items.forEach(item => totalWeight += (item.weight || 0));
        cat.weight = totalWeight;

        if (!isCategoryEdit) {
            let earliest = null;
            let latest = null;
            cat.items.forEach(item => {
                if (item.start_date) {
                    const sd = new Date(item.start_date);
                    if (!earliest || sd < earliest) earliest = sd;
                }
                if (item.finish_date) {
                    const fd = new Date(item.finish_date);
                    if (!latest || fd > latest) latest = fd;
                }
            });

            if (earliest) cat.start_date = earliest.toISOString().split('T')[0];
            if (latest) cat.finish_date = latest.toISOString().split('T')[0];
        }
        cat.duration = calculateDuration(cat.start_date, cat.finish_date);
    });

    updateState({ schedules });
}
