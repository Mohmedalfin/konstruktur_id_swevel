/**
 * tambah-ahs/index.js
 * Entry point for the Tambah AHS item picker page.
 * Structure:
 *   core/       ← config, state, DOM refs, API fetch
 *   components/ ← table render (render.js) + custom inline row (custom-row.js)
 *   hooks/      ← submit to sessionStorage (submit.js)
 */

import { state, namaInput, sourceBoxes, tbody } from './core/state.js?v=3';
import { fetchTambahAhsData }                   from './core/data.js?v=3';
import { renderLoading, renderRows }             from './components/render.js?v=3';
import { bindCustomRow }                         from './components/custom-row.js?v=3';
import { bindSubmit }                            from './hooks/submit.js?v=3';

export async function load() {
    renderLoading();
    const result = await fetchTambahAhsData(state.query, state.sources, state.page);
    renderRows(result);
}

// Guard — exit silently if not on tambah-ahs page
if (tbody) {

    // ── Search (debounced) ────────────────────────────────────────────────────
    let debounceTimer = null;
    if (namaInput) {
        namaInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                state.query = namaInput.value.trim();
                state.page  = 1;
                load();
            }, 300);
        });
    }

    // ── Pagination page change (dispatched by render.js to avoid circular import) ──
    window.addEventListener('tambahAhsPageChange', function (e) {
        state.page = e.detail.page;
        load();
    });

    // ── Source filter ─────────────────────────────────────────────────────────
    sourceBoxes.forEach(cb => {
        cb.addEventListener('change', function () {
            state.sources = Array.from(sourceBoxes).filter(b => b.checked).map(b => b.value);
            state.page    = 1;
            load();
        });
    });

    // ── Components & hooks ────────────────────────────────────────────────────
    bindCustomRow();
    bindSubmit();

    // ── Auto-init ─────────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', load);
    } else {
        load();
    }
}

// Global helper for Back button
window.goBackToRab = function() {
    let returnUrl = '';
    try { returnUrl = sessionStorage.getItem('rab_return_url'); } catch (_) {}
    window.location.href = returnUrl ? returnUrl : '/menu-rap?mode=new';
};
