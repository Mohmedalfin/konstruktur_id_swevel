/**
 * ui.js – shared UI utilities for gudang-lapangan module
 */

/**
 * Render a toast notification
 * @param {'success'|'error'|'warning'|'info'} type
 * @param {string} message
 * @param {number} duration ms
 */
export function showToast(type = 'info', message = '', duration = 3500) {
    const container = document.getElementById('toast-gl');
    if (!container) return;

    const icons = {
        success: 'fa-circle-check',
        error:   'fa-circle-xmark',
        warning: 'fa-triangle-exclamation',
        info:    'fa-circle-info',
    };
    const colors = {
        success: 'bg-emerald-600 border-emerald-700',
        error:   'bg-red-600 border-red-700',
        warning: 'bg-amber-500 border-amber-600',
        info:    'bg-slate-800 border-slate-900',
    };

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg text-white text-xs font-semibold max-w-xs animate-slide-up ${colors[type] || colors.info}`;
    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.info} text-sm shrink-0"></i>
        <span class="flex-1 leading-snug">${message}</span>
        <button class="ml-2 opacity-70 hover:opacity-100 focus:outline-none shrink-0" onclick="this.parentElement.remove()">
            <i class="fas fa-times text-[10px]"></i>
        </button>
    `;

    container.appendChild(toast);
    setTimeout(() => toast.remove(), duration);
}

/**
 * Format number to locale Indonesian
 */
export function formatNum(val, dec = 4) {
    const n = parseFloat(val) || 0;
    // Remove trailing zeros after decimal
    return n % 1 === 0 ? n.toLocaleString('id-ID') : parseFloat(n.toFixed(dec)).toLocaleString('id-ID');
}

/**
 * Format datetime to readable Indonesian format
 */
export function formatDate(dtStr) {
    if (!dtStr) return '–';
    const d = new Date(dtStr);
    if (isNaN(d)) return dtStr;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

/**
 * Get badge HTML for stok status
 */
export function stokStatusBadge(stok) {
    if (stok <= 0) {
        return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-600 text-[10px] font-bold border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>Habis
                </span>`;
    }
    if (stok < 5) {
        return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>Menipis
                </span>`;
    }
    return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Cukup
            </span>`;
}

/**
 * Get badge HTML for mutasi tipe
 */
export function tipeBadge(tipe) {
    if (tipe === 'masuk') {
        return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100">
                    <i class="fas fa-arrow-down text-[8px]"></i> Masuk
                </span>`;
    }
    return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-[10px] font-bold border border-red-100">
                <i class="fas fa-arrow-up text-[8px]"></i> Keluar
            </span>`;
}

/**
 * Get human-readable label for sumber
 */
export function sumberLabel(sumber) {
    const labels = {
        'permintaan':     'Penerimaan Permintaan',
        'pemakaian':      'Pemakaian Realisasi',
        'retur_ke_central': 'Retur ke Central',
        'mutasi_masuk':   'Mutasi Masuk',
        'mutasi_keluar':  'Mutasi Keluar',
        'batal_permintaan': 'Batal Permintaan',
    };
    return labels[sumber] || sumber;
}
