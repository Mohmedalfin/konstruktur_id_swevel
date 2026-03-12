/**
 * tambah-ahs/core/state.js
 * State, config, and DOM references for the Tambah AHS item picker.
 */

export const PAGE_SIZE = 20;

export const tbody         = document.getElementById('tambah-ahs-tbody');
export const namaInput     = document.getElementById('tambah-ahs-nama');
export const countEl       = document.getElementById('tambah-ahs-count');
export const paginationEl  = document.getElementById('tambah-ahs-pagination-btns');
export const paginationInfo= document.getElementById('tambah-ahs-pagination-info');
export const submitBtn     = document.getElementById('tambah-ahs-submit-btn');
export const selectedCount = document.getElementById('tambah-ahs-selected-count');
export const customBtn     = document.getElementById('tambah-ahs-custom-btn');
export const sourceBoxes   = document.querySelectorAll('.tambah-ahs-source');

export const sumberColor = {
    'Proyek Terkini': 'bg-blue-100 text-blue-700',
    'SNI':            'bg-emerald-100 text-emerald-700',
    'Empiris':        'bg-amber-100 text-amber-700',
    'PUPR':           'bg-violet-100 text-violet-700',
    'Estimator.id':   'bg-rose-100 text-rose-700',
};

export const state = {
    page:     1,
    query:    '',
    sources:  [],
    selected: {},
};
