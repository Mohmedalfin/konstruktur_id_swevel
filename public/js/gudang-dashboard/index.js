import { getState, updateState } from './core/state.js';
import { fetchDashboardData } from './core/data.js';
import { renderWelcome, renderDateTime, renderStats, renderKritisTable, renderActivities, renderHealthChart } from './components/render.js';
import { initDashboardEvents } from './hooks/dashboard-events.js';

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Initial Static Render
    const userName = window.DASHBOARD_INIT?.userName || 'Pengguna';
    renderWelcome(userName);
    renderDateTime();
    initDashboardEvents();

    // 2. Fetch Data
    const data = await fetchDashboardData();
    
    if (data) {
        // 3. Update State
        updateState({
            stats: data.stats,
            itemsKritis: data.items_kritis,
            activities: data.activities,
            chartHealth: data.chart_health
        });

        // 4. Render Dynamic Content
        const state = getState();
        renderStats(state.stats);
        renderKritisTable(state.itemsKritis);
        renderActivities(state.activities);
        
        // Ensure chart renders after a small delay if Chart.js is still parsing (safety net)
        setTimeout(() => {
            renderHealthChart(state.chartHealth);
        }, 100);
    } else {
        console.error('Failed to load dashboard components data.');
        document.getElementById('kritis-table-body').innerHTML = `
            <tr><td colspan="5" class="px-5 py-8 text-center text-red-500 font-semibold">Gagal memuat data stok kritis.</td></tr>
        `;
        document.getElementById('activity-timeline').innerHTML = `
            <div class="text-center py-8 text-red-500 font-semibold">Gagal memuat riwayat aktivitas.</div>
        `;
        document.getElementById('health-chart-container').innerHTML = `
            <div class="text-center py-8 text-red-500 font-semibold w-full">Gagal memuat chart kesehatan stok.</div>
        `;
    }
});
