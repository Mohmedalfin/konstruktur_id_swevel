/**
 * core/state.js
 * Shared mutable state and all DOM element references for the RAB feature.
 */

export const state = {
    mode:             null,
    currentId:        null,
    collapsed:        {},
    activeCategories: [],
    format_penomoran: null
};

export const wrapper              = document.getElementById('rab-table-wrapper');
export const tbody                = document.getElementById('rab-tbody');
export const totalJumlah          = document.getElementById('rab-total-jumlah');
export const totalPpn             = document.getElementById('rab-total-ppn');
export const totalFinal           = document.getElementById('rab-total-final');
export const addRabBtn            = document.getElementById('addRabBtn');
export const cards                = document.querySelectorAll('.rab-card');
export const boqDownloadTplBtn    = document.getElementById('boq-download-template-btn');
export const searchInput          = document.getElementById('rab-search');
export const tambahKategoriBtn    = document.getElementById('tambah-kategori-btn');
export const kategoriModalOverlay = document.getElementById('kategori-modal-overlay');
export const kategoriModalList    = document.getElementById('kategori-modal-list');
export const kategoriModalInfo    = document.getElementById('kategori-modal-info');
export const kategoriModalClose   = document.getElementById('kategori-modal-close');
export const kategoriModalCancel  = document.getElementById('kategori-modal-cancel');
export const kategoriModalConfirm = document.getElementById('kategori-modal-confirm');
export const kategoriManualInput  = document.getElementById('kategori-manual-input');
export const kategoriManualAdd    = document.getElementById('kategori-manual-add');
export const resetDataBtn         = document.getElementById('reset-rap-btn');
