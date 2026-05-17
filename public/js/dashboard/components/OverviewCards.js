import { getState } from '../core/state.js';
import { formatRupiah, formatPercent, calculateDaysLeft, formatDateIndo } from '../core/helpers.js';

/**
 * Merender 4 kartu utama di bagian atas dashboard
 */
export function renderOverviewCards() {
    const { data } = getState();
    
    if (!data || !data.overview) return;

    const overview = data.overview;

    const elProjectName = document.getElementById('dash-project-name');
    if (elProjectName && overview.project_name) {
        elProjectName.innerHTML = overview.project_name;
    }

    const elKontrak = document.getElementById('val-nilai-kontrak');
    if (elKontrak) {
        elKontrak.innerHTML = formatRupiah(overview.nilai_kontrak);
    }

    const elRap = document.getElementById('val-nilai-rap');
    const containerMargin = document.getElementById('container-margin');
    const textMarginPct = document.getElementById('text-margin-pct');
    const badgeMargin = document.getElementById('val-margin-pct');
    
    if (elRap) {
        elRap.innerHTML = formatRupiah(overview.nilai_rap);
    }
    
    if (containerMargin && textMarginPct) {
        containerMargin.classList.remove('opacity-0');
        textMarginPct.textContent = formatPercent(overview.margin_pct);
        
        const iconHTML = overview.margin_pct < 0 
            ? '<i class="fas fa-caret-down"></i>' 
            : '<i class="fas fa-caret-up"></i>';
            
        const badgeClass = overview.margin_pct < 0
            ? 'inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-red-50 text-red-600'
            : 'inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-600';
            
        badgeMargin.className = badgeClass;
        badgeMargin.innerHTML = `${iconHTML} <span id="text-margin-pct">${formatPercent(Math.abs(overview.margin_pct))}</span>`;
    }

    const elRealisasi = document.getElementById('val-realisasi');
    const elSerapanPct = document.getElementById('val-serapan-pct');
    const barSerapan = document.getElementById('bar-serapan');

    if (elRealisasi) {
        elRealisasi.innerHTML = formatRupiah(overview.realisasi);
    }
    
    if (elSerapanPct && barSerapan) {
        elSerapanPct.textContent = formatPercent(overview.serapan_pct);
        
        setTimeout(() => {
            const width = Math.min(overview.serapan_pct, 100); 
            barSerapan.style.width = `${width}%`;
            
            if (overview.serapan_pct > 100) {
                barSerapan.className = 'bg-gradient-to-r from-red-400 via-rose-400 to-red-500 h-1.5 rounded-full transition-all duration-1000 w-0 relative';
                setTimeout(() => barSerapan.style.width = '100%', 50); 
            }
        }, 100);
    }
    const elTargetDate = document.getElementById('val-target-date');
    const elHariLagi = document.getElementById('val-hari-lagi');

    if (elTargetDate) {
        elTargetDate.innerHTML = overview.target_date ? formatDateIndo(overview.target_date) : '-';
    }

    if (elHariLagi && overview.hari_lagi !== null) {
        let hariLagiText = '';
        let colorClass = '';

        if (overview.hari_lagi > 0) {
            hariLagiText = `${overview.hari_lagi} hari lagi`;
            colorClass = 'text-slate-500'; 
        } else if (overview.hari_lagi === 0) {
            hariLagiText = 'Hari ini Deadline!';
            colorClass = 'text-amber-500 font-bold';
        } else {
            hariLagiText = `Terlambat ${Math.abs(overview.hari_lagi)} hari`;
            colorClass = 'text-red-500 font-bold';
        }

        elHariLagi.innerHTML = `<span class="${colorClass}">${hariLagiText}</span>`;
    }
}
