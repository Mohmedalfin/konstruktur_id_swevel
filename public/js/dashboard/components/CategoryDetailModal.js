import { formatCurrency, formatPercent } from '../core/helpers.js';

export function initCategoryDetailModal() {
    const tableBody = document.getElementById('table-summary-body');
    if (!tableBody) return;

    tableBody.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-category-detail');
        if (!btn) return;

        const idKategori = btn.getAttribute('data-id');
        if (!idKategori) return;

        openModal();
        setLoadingState(true);

        try {
            const { idProject, slug } = window.DASHBOARD_INIT;
            const response = await fetch(`/proyek/${slug}/dashboard/getCategoryDetail/${idKategori}?id_project=${idProject}`);
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const result = await response.json();
            if (result.status === 'success') {
                populateModalData(result.data.category_detail);
            } else {
                throw new Error(result.message || 'Failed to fetch data');
            }
        } catch (error) {
            console.error('Error fetching category detail:', error);
            // Optionally show error state in modal
            document.getElementById('modal-cat-title').innerHTML = `Detail Kategori: <span class="text-red-500">Error memuat data</span>`;
        } finally {
            setLoadingState(false);
        }
    });
}

function openModal() {
    const modalEl = document.getElementById('modal-category-detail');
    if (window.HSOverlay && modalEl) {
        HSOverlay.open(modalEl);
    }
}

function setLoadingState(isLoading) {
    const skeletonRows = Array(3).fill(0).map(() => `
        <tr>
            <td class="px-4 py-3 text-center"><div class="h-4 w-4 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-32 bg-slate-100 animate-pulse rounded"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-12 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-12 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-8 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-16 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
            <td class="px-4 py-3"><div class="h-5 w-16 bg-slate-100 animate-pulse rounded-full mx-auto"></div></td>
        </tr>
    `).join('');

    if (isLoading) {
        document.getElementById('modal-cat-title').innerHTML = `Detail Kategori: <span class="animate-pulse bg-slate-100 text-transparent rounded inline-block w-40">Loading</span>`;
        document.getElementById('modal-cat-table-body').innerHTML = skeletonRows;
        
        document.getElementById('modal-cat-spi-pill').classList.add('hidden');
        document.getElementById('modal-cat-bar-target').style.width = '0%';
        document.getElementById('modal-cat-text-target').textContent = '0%';
        document.getElementById('modal-cat-bar-actual').style.width = '0%';
        document.getElementById('modal-cat-text-actual').textContent = '0%';
        document.getElementById('modal-cat-ac').textContent = 'Rp 0';
        document.getElementById('modal-cat-time-bar').style.width = '0%';
        document.getElementById('modal-cat-start-date').textContent = '-';
        document.getElementById('modal-cat-finish-date').textContent = '-';
        document.getElementById('modal-cat-deviasi-text').textContent = 'Deviasi 0';
    }
}

function populateModalData(data) {
    const activeBtn = document.querySelector(`.btn-category-detail[data-id="${data.id_kategori}"]`);
    const catName = activeBtn ? activeBtn.closest('tr').querySelector('span').textContent : 'Kategori';

    document.getElementById('modal-cat-title').textContent = `Detail Kategori: ${catName}`;

    const spiPill = document.getElementById('modal-cat-spi-pill');
    const spiDot = document.getElementById('modal-cat-spi-dot');
    const spiPing = document.getElementById('modal-cat-spi-ping');
    const spiText = document.getElementById('modal-cat-spi-text');
    
    spiPill.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'bg-amber-50', 'text-amber-700', 'border-amber-200', 'bg-rose-50', 'text-rose-700', 'border-rose-200');
    if (spiDot) spiDot.classList.remove('bg-emerald-500', 'bg-amber-500', 'bg-rose-500');
    if (spiPing) spiPing.classList.remove('bg-emerald-500', 'bg-amber-500', 'bg-rose-500');

    let spiColor = 'emerald';
    if (data.spi_value < 0.9) spiColor = 'rose';
    else if (data.spi_value < 1.0) spiColor = 'amber';

    spiPill.classList.add(`bg-${spiColor}-50`, `text-${spiColor}-700`, `border-${spiColor}-200`);
    if (spiDot) spiDot.classList.add(`bg-${spiColor}-500`);
    if (spiPing) spiPing.classList.add(`bg-${spiColor}-500`);
    
    spiText.textContent = `${data.schedule_status} (SPI ${data.spi_value})`;
    spiPill.classList.remove('hidden');

    const targetPct = data.planned_pct;
    const actualPct = data.actual_pct;
    document.getElementById('modal-cat-bar-target').style.width = `${Math.min(targetPct, 100)}%`;
    document.getElementById('modal-cat-text-target').textContent = formatPercent(targetPct);
    document.getElementById('modal-cat-bar-actual').style.width = `${Math.min(actualPct, 100)}%`;
    document.getElementById('modal-cat-text-actual').textContent = formatPercent(actualPct);

    const deviasi = actualPct - targetPct;
    const devPill = document.getElementById('modal-cat-deviasi-pill');
    devPill.className = `inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-black ${deviasi >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}`;
    document.getElementById('modal-cat-deviasi-text').textContent = `Deviasi ${deviasi > 0 ? '+' : ''}${deviasi.toFixed(2)}`;

    document.getElementById('modal-cat-ac').textContent = formatCurrency(data.ac_value);
    const cpiPill = document.getElementById('modal-cat-cpi-pill');
    const cpiIcon = cpiPill.querySelector('i');
    
    let cpiColor = 'emerald';
    if (data.cpi_value < 0.9) cpiColor = 'rose';
    else if (data.cpi_value < 1.0) cpiColor = 'amber';
    
    cpiPill.className = `inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-black bg-${cpiColor}-50 text-${cpiColor}-600`;
    cpiIcon.className = `fas ${data.cpi_value >= 1 ? 'fa-check-circle' : 'fa-exclamation-triangle'}`;
    document.getElementById('modal-cat-cpi-text').textContent = `${data.cost_status} (CPI ${data.cpi_value})`;

    const startDate = data.start_date || '-';
    const finishDate = data.finish_date || '-';
    document.getElementById('modal-cat-start-date').textContent = startDate;
    document.getElementById('modal-cat-finish-date').textContent = finishDate;
    
    if (data.start_date && data.finish_date) {
        const start = new Date(data.start_date);
        const finish = new Date(data.finish_date);
        const now = new Date();
        const totalMs = finish - start;
        const elapsedMs = now - start;
        const totalWeeks = Math.ceil(totalMs / (1000 * 60 * 60 * 24 * 7));
        const currentWeek = Math.max(1, Math.ceil(elapsedMs / (1000 * 60 * 60 * 24 * 7)));
        
        document.getElementById('modal-cat-week-text').textContent = `Minggu ${currentWeek > totalWeeks ? totalWeeks : currentWeek} dari ${totalWeeks}`;
        
        const timePct = Math.min(100, Math.max(0, (elapsedMs / totalMs) * 100));
        document.getElementById('modal-cat-time-bar').style.width = `${timePct}%`;
    } else {
        document.getElementById('modal-cat-week-text').textContent = `Timeline Belum Diatur`;
    }

    const tableBody = document.getElementById('modal-cat-table-body');
    tableBody.innerHTML = '';

    if (!data.items || data.items.length === 0) {
        tableBody.innerHTML = `
            <tr class="h-[200px]">
                <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-medium italic bg-slate-50/20">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <i class="fas fa-folder-open text-2xl text-slate-200"></i>
                        <span>Belum ada item pekerjaan di kategori ini.</span>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    data.items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50/50 transition-colors h-[50px] group';

        let statusHtml = '';
        switch(item.status) {
            case 'Selesai':
                statusHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 whitespace-nowrap"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>Selesai</span>';
                break;
            case 'Berjalan':
                statusHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black bg-blue-100 text-blue-700 whitespace-nowrap"><div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>Berjalan</span>';
                break;
            case 'Terlambat':
                statusHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black bg-rose-100 text-rose-700 whitespace-nowrap"><div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div>Terlambat</span>';
                break;
            default:
                statusHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black bg-slate-100 text-slate-500 whitespace-nowrap"><div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>Belum Mulai</span>';
        }

        row.innerHTML = `
            <td class="px-4 py-2 text-center text-[10px] font-black text-slate-400 border-b border-slate-100/50">${index + 1}</td>
            <td class="px-4 py-2 text-xs font-bold text-slate-700 border-b border-slate-100/50">${item.pekerjaan}</td>
            <td class="px-4 py-2 text-center text-xs font-black text-slate-600 border-b border-slate-100/50">${item.volume} ${item.satuan}</td>
            <td class="px-4 py-2 text-center text-xs font-black text-slate-600 border-b border-slate-100/50">${item.total_tercapai} ${item.satuan}</td>
            <td class="px-4 py-2 text-center text-xs font-bold text-slate-400 border-b border-slate-100/50">${formatPercent(item.bobot_pct)}</td>
            <td class="px-4 py-2 text-center border-b border-slate-100/50">
                <div class="flex items-center gap-2 justify-center">
                    <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden border border-slate-200/50">
                        <div class="h-full bg-blue-500" style="width: ${Math.min(item.actual_pct, 100)}%"></div>
                    </div>
                    <span class="text-[9px] font-black text-slate-500 w-6">${formatPercent(item.actual_pct)}</span>
                </div>
            </td>
            <td class="px-4 py-2 text-center border-b border-slate-100/50">
                ${statusHtml}
            </td>
        `;
        tableBody.appendChild(row);
    });
}
