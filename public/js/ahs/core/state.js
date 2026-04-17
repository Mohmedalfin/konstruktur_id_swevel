/**
 * ahs/core/state.js
 * Shared mutable state and all DOM element references for the AHS feature.
 */

// ── Main table ─────────────────────────────────────────────────────────────
export const tbody         = document.getElementById('ahs-tbody');
export const itemLabel     = document.getElementById('ahs-item-label');
export const addBahanBtn   = document.getElementById('ahs-add-bahan-btn');
export const addAlatBtn    = document.getElementById('ahs-add-alat-btn');
export const addUpahBtn    = document.getElementById('ahs-add-upah-btn');
export const simpanBtn     = document.getElementById('ahs-simpan-btn');
export const tableSearch   = document.getElementById('ahs-table-search');
export const sourceLabel    = document.getElementById('ahs-source-label');
export const totalKeselEl  = document.getElementById('ahs-total-keseluruhan');

// ── Modal ──────────────────────────────────────────────────────────────────
export const modalOverlay  = document.getElementById('ahs-modal-overlay');
export const modalClose    = document.getElementById('ahs-modal-close');
export const modalCancel   = document.getElementById('ahs-modal-cancel');
export const modalConfirm  = document.getElementById('ahs-modal-confirm');
export const modalSearch   = document.getElementById('ahs-modal-search');
export const modalTbody    = document.getElementById('ahs-modal-tbody');
export const modalCheckAll = document.getElementById('ahs-modal-check-all');
export const modalCountEl  = document.getElementById('ahs-modal-selected-count');
export const fromDbBtn     = document.getElementById('ahs-from-db-btn');
export const filterBtns    = document.querySelectorAll('.ahs-modal-filter-btn');

// ── Mutable state ──────────────────────────────────────────────────────────
export const state = {
    rowCounter:         0,
    activeFilter:       'all',
    modalSelected:      new Map(),
    autocompleteActive: null,
    ahsDatabase:        [],
    currentPage:        1,
    hasMoreData:        true,
    isFetching:         false,
};
