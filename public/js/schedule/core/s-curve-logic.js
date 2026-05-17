
function toDateStr(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function distributeItemWeight(item, globalStartDate, totalDays, dailyWeights) {
    const weight = Number(item.weight ?? 0);

    if (Number.isFinite(weight) && weight > 0 && item.start_date && item.finish_date) {
        const startDate  = new Date(item.start_date);
        const finishDate = new Date(item.finish_date);

        if (!isNaN(startDate) && !isNaN(finishDate) && finishDate >= startDate) {
            const startIdx  = Math.round((startDate  - globalStartDate) / 86400000);
            const finishIdx = Math.round((finishDate - globalStartDate) / 86400000);

            const from = Math.max(0, Math.min(startIdx,  totalDays - 1));
            const to   = Math.max(0, Math.min(finishIdx, totalDays - 1));

            if (from <= to) {
                const duration       = to - from + 1;
                const weightPerDay   = weight / duration;
                for (let i = from; i <= to; i++) {
                    dailyWeights[i] += weightPerDay;
                }
            }
        }
    }

    if (item.children && item.children.length > 0) {
        item.children.forEach(child =>
            distributeItemWeight(child, globalStartDate, totalDays, dailyWeights)
        );
    }
}

export function generateSCurveData(schedules, timelineConfig, globalStartDate, globalEndDate) {
    if (
        !schedules || !timelineConfig || timelineConfig.length === 0 ||
        !globalStartDate || !globalEndDate
    ) {
        return { labels: [], data: [] };
    }

    const totalDays = Math.round((globalEndDate - globalStartDate) / 86400000) + 1;
    if (totalDays <= 0) return { labels: [], data: [] };

    const weekStartDays = new Set();
    timelineConfig.forEach(monthConfig => {
        monthConfig.weeks.forEach((_, weekIndex) => {
            const dayOfMonth = weekIndex * 7 + 1;
            const weekStart  = new Date(monthConfig.year, monthConfig.month, dayOfMonth);
            const dayIdx     = Math.round((weekStart - globalStartDate) / 86400000);
            if (dayIdx >= 0 && dayIdx < totalDays) weekStartDays.add(dayIdx);
        });
    });

    const dailyWeights = new Array(totalDays).fill(0);

    schedules.forEach(category => {
        if (category.items && category.items.length > 0) {
            category.items.forEach(item =>
                distributeItemWeight(item, globalStartDate, totalDays, dailyWeights)
            );
        }
    });

    let cumulative = 0;
    const cumulativeData = dailyWeights.map(w => {
        cumulative += w;
        return cumulative;
    });

    const finalValue = cumulativeData[cumulativeData.length - 1] || 0;

    let data;
    if (finalValue > 0) {
        data = cumulativeData.map(v => Number(((v / finalValue) * 100).toFixed(3)));
        data[data.length - 1] = 100;
    } else {
        data = cumulativeData.map(() => 0);
    }

    const labels = [];
    let weekLabelIdx = 0;

    const weekLabels = [];
    timelineConfig.forEach(monthConfig => {
        monthConfig.weeks.forEach(w => weekLabels.push(`${monthConfig.monthName} ${w}`));
    });

    for (let i = 0; i < totalDays; i++) {
        if (weekStartDays.has(i)) {
            labels.push(weekLabels[weekLabelIdx] ?? '');
            weekLabelIdx++;
        } else {
            labels.push('');
        }
    }

    return { labels, data };
}

function parseLocalDate(str) {
    if (!str) return null;
    const parts = str.split('-');
    if (parts.length !== 3) return null;
    const d = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    return isNaN(d) ? null : d;
}

export function computeWeeklyPlanWeights(schedules, timelineConfig, globalStartDate, globalEndDate) {
    const empty = { weeklyWeights: [], cumulativeWeights: [] };

    if (!schedules?.length || !timelineConfig?.length || !globalStartDate || !globalEndDate) {
        return empty;
    }

    const leafItems = [];
    function collectLeaves(items) {
        items?.forEach(item => {
            const weight = Number(item.weight || 0);
            const start  = parseLocalDate(item.start_date);
            const end    = parseLocalDate(item.finish_date);
            if (weight > 0 && start && end && end >= start) {
                leafItems.push({ start, end, weight });
            }
            if (item.children?.length) collectLeaves(item.children);
        });
    }
    schedules.forEach(cat => collectLeaves(cat.items));
    if (!leafItems.length) return empty;

    const totalWeight = leafItems.reduce((s, it) => s + it.weight, 0);
    if (totalWeight <= 0) return empty;

    const tlStart  = new Date(globalStartDate.getFullYear(), globalStartDate.getMonth(), globalStartDate.getDate());
    const tlEnd    = new Date(globalEndDate.getFullYear(),   globalEndDate.getMonth(),   globalEndDate.getDate());
    const totalDays = Math.round((tlEnd - tlStart) / 86400000) + 1;

    const dailyWeights = new Array(totalDays).fill(0);

    leafItems.forEach(({ start, end, weight }) => {
        const duration    = Math.round((end - start) / 86400000) + 1;
        const weightPerDay = weight / duration;

        const fromIdx = Math.round((start - tlStart) / 86400000);
        const toIdx   = Math.round((end   - tlStart) / 86400000);
        const safeFrom = Math.max(0, fromIdx);
        const safeTo   = Math.min(totalDays - 1, toIdx);

        for (let d = safeFrom; d <= safeTo; d++) {
            dailyWeights[d] += weightPerDay;
        }
    });

    const weeklyWeights = [];

    timelineConfig.forEach(monthConfig => {
        monthConfig.weeks.forEach((_, wIdx) => {
            const wStartDay = wIdx * 7 + 1;
            const lastDay   = new Date(monthConfig.year, monthConfig.month + 1, 0).getDate();
            const isLastWeek = (wIdx === monthConfig.weeks.length - 1);
            const wEndDay   = isLastWeek ? lastDay : wStartDay + 6;

            const weekStart = new Date(monthConfig.year, monthConfig.month, wStartDay);
            const weekEnd   = new Date(monthConfig.year, monthConfig.month, wEndDay, 23, 59, 59);

            const fromIdx = Math.round((weekStart - tlStart) / 86400000);
            const toIdx   = Math.round((weekEnd   - tlStart) / 86400000);

            if (toIdx < 0 || fromIdx >= totalDays) {
                weeklyWeights.push(0);
                return;
            }

            let weekSum = 0;
            const safeFrom = Math.max(0, fromIdx);
            const safeTo   = Math.min(totalDays - 1, toIdx);
            for (let d = safeFrom; d <= safeTo; d++) {
                weekSum += dailyWeights[d];
            }

            weeklyWeights.push(Number(((weekSum / totalWeight) * 100).toFixed(2)));
        });
    });

    let acc = 0;
    const lastIdx = weeklyWeights.length - 1;
    const cumulativeWeights = weeklyWeights.map((w, i) => {
        acc += w;
        return i === lastIdx ? 100 : Number(acc.toFixed(2));
    });

    return { weeklyWeights, cumulativeWeights };
}
