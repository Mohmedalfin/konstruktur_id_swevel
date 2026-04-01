/**
 * shared/utils.js
 * Utility functions shared across multiple modules.
 */

export const fmt = n =>
    'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2 });

export function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
