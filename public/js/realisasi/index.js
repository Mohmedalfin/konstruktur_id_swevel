import { fetchRealisasiData, fetchSDMData } from './core/data.js';
import { renderTable, renderSDMTable } from './components/render.js';
import { initPekerjaanEvents } from './hooks/pekerjaan-events.js';
import { initFilter, getFilteredData } from './hooks/filter.js';
import { initPekerjaanModalEvents } from './hooks/pekerjaan-modal-events.js';
import { initLogEvents } from './hooks/log-events.js';
import { initSDMEvents } from './hooks/sdm-events.js';
import { initSDMModalEvents } from './hooks/sdm-modal-events.js';
import { initListSDMEvents } from './hooks/list-sdm-events.js';
import { initSDMFilter, getFilteredSDMData } from './hooks/sdm-filter.js';
import { updateState } from './core/state.js';
import { initPhotoLightbox } from './components/lightbox.js';

const yieldToMain = () => new Promise(resolve => setTimeout(resolve, 0));

document.addEventListener('DOMContentLoaded', async () => {
    try {
        if (window.showLoader) window.showLoader();

        const tbodyPekerjaan = document.getElementById('realisasi-tbody');
        const tbodySDM = document.getElementById('realisasi-sdm-tbody');

        const [data, sdmData, sdmResources] = await Promise.all([
            tbodyPekerjaan ? fetchRealisasiData() : Promise.resolve(null),
            tbodySDM ? fetchSDMData() : Promise.resolve(null),
            document.getElementById('modal-real-sdm') ? import('./core/data.js').then(m => m.fetchSDMResources()) : Promise.resolve([])
        ]);

        if (tbodyPekerjaan && data) {
            updateState({ realisasiData: data });
            await yieldToMain();
            renderTable(getFilteredData(), tbodyPekerjaan);
            initPekerjaanEvents(tbodyPekerjaan);
            initFilter();
        }
        
        if (tbodySDM && sdmData) {
            updateState({ 
                sdmResources: sdmResources || [],
                sdmData: sdmData || []
            });
            await yieldToMain();
            initSDMFilter();
            renderSDMTable(getFilteredSDMData(), tbodySDM);
            initSDMEvents(tbodySDM);
        }

        initPekerjaanModalEvents();
        initSDMModalEvents();
        initListSDMEvents();
        initLogEvents();
        initPhotoLightbox();

        const tabPekerjaan = document.getElementById('tab-pekerjaan');
        const tabSdm = document.getElementById('tab-sdm');
        const sectionPekerjaan = document.getElementById('section-pekerjaan');
        const sectionSdm = document.getElementById('section-sdm');

        if (tabPekerjaan && tabSdm && sectionPekerjaan && sectionSdm) {
            const activateTab = (activeTab, inactiveTab) => {
                activeTab.className = "flex-1 sm:flex-none px-3 sm:px-6 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold bg-white text-[#1e293b] rounded-lg shadow-sm focus:outline-none transition-all whitespace-nowrap";
                inactiveTab.className = "flex-1 sm:flex-none px-3 sm:px-6 py-2 md:py-2.5 text-[11px] sm:text-sm font-semibold text-slate-500 hover:text-[#1e293b] rounded-lg focus:outline-none transition-all whitespace-nowrap";
            };

            tabPekerjaan.addEventListener('click', () => {
                activateTab(tabPekerjaan, tabSdm);
                sectionPekerjaan.classList.remove('hidden');
                sectionPekerjaan.classList.add('block');
                sectionSdm.classList.remove('block');
                sectionSdm.classList.add('hidden');
            });

            tabSdm.addEventListener('click', () => {
                activateTab(tabSdm, tabPekerjaan);
                sectionSdm.classList.remove('hidden');
                sectionSdm.classList.add('block');
                sectionPekerjaan.classList.remove('block');
                sectionPekerjaan.classList.add('hidden');
            });
        }
    } catch (error) {
        console.error('Error during realisasi initialization:', error);
    } finally {
        if (window.hideLoader) window.hideLoader();
    }
});