/**
 * Gudang Lapangan – index.js
 * Entry point untuk modul Gudang Lapangan (Site Inventory)
 */

import { StokTab }  from './StokTab.js';
import { KartuTab } from './KartuTab.js';
import { ReturModal } from './ReturModal.js';
import { KartuBarangModal } from './KartuBarangModal.js';
import { showToast } from './ui.js';
import { initCustomDropdowns } from './custom-dropdown.js';

const CFG = window.GUDANG_LAPANGAN;

document.addEventListener('DOMContentLoaded', () => {
    const tabStok  = document.getElementById('tab-stok');
    const tabKartu = document.getElementById('tab-kartu');
    const secStok  = document.getElementById('section-stok');
    const secKartu = document.getElementById('section-kartu');

    // -------- Tab Switch --------
    function activateTab(tab) {
        const isStok = (tab === 'stok');

        // Buttons
        tabStok.className  = isStok
            ? 'flex-1 sm:flex-none px-3 sm:px-5 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold bg-white text-[#1e293b] rounded-lg shadow-sm focus:outline-none transition-all whitespace-nowrap'
            : 'flex-1 sm:flex-none px-3 sm:px-5 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold text-slate-500 hover:text-[#1e293b] rounded-lg focus:outline-none transition-all whitespace-nowrap';
        tabKartu.className = !isStok
            ? 'flex-1 sm:flex-none px-3 sm:px-5 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold bg-white text-[#1e293b] rounded-lg shadow-sm focus:outline-none transition-all whitespace-nowrap'
            : 'flex-1 sm:flex-none px-3 sm:px-5 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold text-slate-500 hover:text-[#1e293b] rounded-lg focus:outline-none transition-all whitespace-nowrap';

        // Sections
        secStok.classList.toggle('hidden', !isStok);
        secStok.classList.toggle('block', isStok);
        secKartu.classList.toggle('hidden', isStok);
        secKartu.classList.toggle('block', !isStok);

        // Load data on first view
        if (!isStok && !kartuTab.loaded) {
            kartuTab.load();
        }
    }

    // -------- Inisialisasi Modul --------
    const returModal       = new ReturModal(CFG, showToast);
    const kartuBarangModal = new KartuBarangModal(CFG, showToast);

    const stokTab  = new StokTab(CFG, returModal, kartuBarangModal, showToast);
    const kartuTab = new KartuTab(CFG, showToast);

    // -------- Load initial data --------
    stokTab.load().finally(() => {
        if (window.hideLoader) window.hideLoader();
    });

    // -------- Event: Tab buttons --------
    tabStok.addEventListener('click',  () => activateTab('stok'));
    tabKartu.addEventListener('click', () => activateTab('kartu'));

    // -------- Refresh buttons --------
    document.getElementById('btn-refresh-stok')?.addEventListener('click', () => stokTab.load());
    document.getElementById('btn-refresh-kartu')?.addEventListener('click', () => kartuTab.load());

    // -------- Initialize Custom UI --------
    initCustomDropdowns();
});
