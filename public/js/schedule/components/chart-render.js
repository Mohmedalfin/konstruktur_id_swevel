import { generateSCurveData } from '../core/s-curve-logic.js';
import { getState } from '../core/state.js';

let sCurveChartInstance = null;

export function hideSCurveChart() {
    const overlay = document.getElementById('sCurveOverlay');
    if (overlay) overlay.style.display = 'none';

    if (sCurveChartInstance) {
        sCurveChartInstance.destroy();
        sCurveChartInstance = null;
    }
}

export function renderSCurveChart() {
    const canvas  = document.getElementById('sCurveChartOverlay');
    const overlay = document.getElementById('sCurveOverlay');

    if (!canvas || !overlay) return;

    if (typeof Chart === 'undefined') {
        console.error('[chart-render] Chart.js is not loaded.');
        return;
    }

    const { schedules, timelineConfig, viewMode, globalStartDate, globalEndDate } = getState();

    if (viewMode !== 's-curve') {
        hideSCurveChart();
        return;
    }

    const { labels, data } = generateSCurveData(schedules, timelineConfig, globalStartDate, globalEndDate);

    if (!labels.length || !data.length) {
        hideSCurveChart();
        return;
    }

    const tbody    = document.getElementById('schedule-tbody');
    const firstRow = tbody ? tbody.querySelector('tr') : null;
    const ganttCell = firstRow ? firstRow.querySelector('td[colspan]') : null;

    if (!tbody || !ganttCell) {
        hideSCurveChart();
        return;
    }

    overlay.style.display = 'block';
    overlay.style.position = 'absolute';
    overlay.style.top    = `${tbody.offsetTop}px`;
    overlay.style.left   = `${ganttCell.offsetLeft}px`;
    overlay.style.width  = `${ganttCell.offsetWidth}px`;
    overlay.style.height = `${tbody.offsetHeight}px`;
    overlay.style.pointerEvents = 'none';

    canvas.style.width  = '100%';
    canvas.style.height = '100%';
    canvas.width  = ganttCell.offsetWidth;
    canvas.height = tbody.offsetHeight;

    if (sCurveChartInstance) {
        sCurveChartInstance.destroy();
        sCurveChartInstance = null;
    }

    const ctx = canvas.getContext('2d');

    sCurveChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Bobot Kumulatif',
                data,
                borderColor: '#2563eb',
                backgroundColor: 'transparent',
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#2563eb',
                pointHoverBorderWidth: 2,
                fill: false,
                tension: 0.2,
                spanGaps: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            layout: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0,
                    right: 0
                }
            },

            animation: {
                duration: 800,
                easing: 'easeOutQuart'
            },

            scales: {
                y: {
                    min: 0,
                    max: 100,
                    display: false,
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                x: {
                    display: false,
                    offset: false,
                    grid: {
                        offset: false,
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        padding: 0
                    }
                }
            },

            plugins: {
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont:  { size: 12 },
                    displayColors: false,
                    callbacks: {
                        title(items) {
                            const idx = items[0]?.dataIndex ?? 0;
                            const allLabels = items[0]?.chart?.data?.labels ?? [];
                            for (let i = idx; i >= 0; i--) {
                                if (allLabels[i]) return allLabels[i];
                            }
                            return '';
                        },
                        label(context) {
                            const value = Number(context.parsed.y || 0);
                            return `Target: ${value.toFixed(2)}%`;
                        }
                    }
                },
                legend: {
                    display: false
                }
            },

            interaction: {
                intersect: false,
                mode: 'index'
            },

            elements: {
                line: {
                    borderJoinStyle: 'round',
                    borderCapStyle: 'round'
                }
            }
        }
    });
}
