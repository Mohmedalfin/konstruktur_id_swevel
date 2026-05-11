/**
 * tambah-ahs/index.js
 * Entry point for the Tambah AHS item picker page.
 * Structure:
 *   core/       ← config, state, DOM refs, API fetch
 *   components/ ← table render (render.js) + custom inline row (custom-row.js)
 *   hooks/      ← submit to sessionStorage (submit.js)
 */

import { state, namaInput, sourceBoxes, tbody } from './core/state.js';
import { fetchTambahAhsData }                   from './core/data.js';
import { renderLoading, renderRows }             from './components/render.js';
import { bindCustomRow }                         from './components/custom-row.js';
import { bindSubmit }                            from './hooks/submit.js';

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
    
    if (returnUrl) {
        window.location.href = returnUrl;
        return;
    }
    
    // Fallback if no return URL in session
    let slug = '';
    try { slug = localStorage.getItem('lastProjectSlug'); } catch (_) {}
    window.location.href = slug ? `/proyek/${slug}` : '/proyek';
};
