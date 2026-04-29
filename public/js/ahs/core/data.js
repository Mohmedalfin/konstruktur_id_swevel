/**
 * ahs/core/data.js
 * Data layer for AHS — fetches from /api/ahs (estimator), /api/ahs/proyek, /api/ahs/shbj.
 */

import { state } from './state.js';

/**
 * Fetch dari estimator DB (master bahan/upah/alat).
 * Used by tabs: suplier, ikkbps, estimatorid, survey (fallback to same endpoint for now).
 */
export async function fetchAhsDatabase(page = 1, q = '', appendData = false) {
    if (state.isFetching || (!state.hasMoreData && page > 1)) return;
    state.isFetching = true;

    try {
        const params = new URLSearchParams({ page, q, tipe: state.activeFilter });
        const res    = await fetch('/api/ahs?' + params.toString());
        if (!res.ok) throw new Error('API Error');
        const json = await res.json();

        if (json.status === 'success' && Array.isArray(json.data)) {
            const processed = json.data.map((item, index) => {
                const safeUraian = (item.uraian || '').replace(/\W/g, '').substring(0, 15);
                item._uid = item.tipe + '_' + item.id + '_' + safeUraian + '_' + index;
                return item;
            });

            state.ahsDatabase  = appendData ? state.ahsDatabase.concat(processed) : processed;
            state.hasMoreData  = json.data.length >= 20;
            state.currentPage  = page;
        }
    } catch (err) {
        console.error('Gagal mengambil master AHS:', err);
        if (!appendData) state.ahsDatabase = [];
    } finally {
        state.isFetching = false;
    }
}

/**
 * Fetch bahan/upah/alat yang pernah diinput di proyek terkini.
 * Tab "Proyek Terkini" → GET /api/ahs/proyek
 *
 * @param {number|string|null} idProject   - ID proyek yang sedang aktif (bisa null, fallback ke idDetail)
 * @param {number}             idDetail    - id_rap_detail saat ini (fallback untuk derive id_project)
 * @param {number}             page
 * @param {string}             q           - keyword pencarian
 * @param {boolean}            appendData  - true = infinite scroll
 */
export async function fetchProyekItems(idProject, idDetail, page = 1, q = '', appendData = false) {
    if (state.isFetching || (!state.hasMoreData && page > 1)) return;
    // Need at least one of idProject or idDetail
    if (!idProject && !idDetail) {
        state.ahsDatabase = [];
        return;
    }
    state.isFetching = true;

    try {
        const params = new URLSearchParams({ page, q, tipe: state.activeFilter });
        if (idProject) params.set('id_project', idProject);
        if (idDetail)  params.set('id_rap_detail', idDetail);   // backend fallback

        const res  = await fetch('/api/ahs/proyek?' + params.toString());
        if (!res.ok) throw new Error('API Error');
        const json = await res.json();

        if (json.status === 'success' && Array.isArray(json.data)) {
            // Cache the resolved id_project back to state
            if (json.id_project && !state.idProject) {
                state.idProject = json.id_project;
            }
            state.ahsDatabase = appendData ? state.ahsDatabase.concat(json.data) : json.data;
            state.hasMoreData = json.data.length >= (json.limit ?? 50);
            state.currentPage = page;
        }
    } catch (err) {
        console.error('Gagal mengambil data proyek terkini:', err);
        if (!appendData) state.ahsDatabase = [];
    } finally {
        state.isFetching = false;
    }
}

/**
 * Fetch bahan/upah/alat yang bersumber dari ketentuan daerah (SHBJ).
 * Tab "SHBJ" → GET /api/ahs/shbj
 *
 * @param {number}  page
 * @param {string}  q
 * @param {boolean} appendData
 */
export async function fetchShbjItems(page = 1, q = '', appendData = false) {
    if (state.isFetching || (!state.hasMoreData && page > 1)) return;
    state.isFetching = true;

    try {
        const params = new URLSearchParams({
            page,
            q,
            tipe : state.activeFilter,
        });
        const res  = await fetch('/api/ahs/shbj?' + params.toString());
        if (!res.ok) throw new Error('API Error');
        const json = await res.json();

        if (json.status === 'success' && Array.isArray(json.data)) {
            state.ahsDatabase = appendData ? state.ahsDatabase.concat(json.data) : json.data;
            state.hasMoreData = json.data.length >= (json.limit ?? 50);
            state.currentPage = page;
        }
    } catch (err) {
        console.error('Gagal mengambil data SHBJ:', err);
        if (!appendData) state.ahsDatabase = [];
    } finally {
        state.isFetching = false;
    }
}

export async function fetchSurveyItems(page = 1, q = '', appendData = false) {
    if (state.isFetching || (!state.hasMoreData && page > 1)) return;
    state.isFetching = true;

    try {
        const params = new URLSearchParams({ page, q, tipe: state.activeFilter });
        const res  = await fetch('/api/ahs/survey?' + params.toString());
        if (!res.ok) throw new Error('API Error');
        const json = await res.json();

        if (json.status === 'success' && Array.isArray(json.data)) {
            state.ahsDatabase = appendData ? state.ahsDatabase.concat(json.data) : json.data;
            state.hasMoreData = json.data.length >= (json.limit ?? 50);
            state.currentPage = page;
        }
    } catch (err) {
        console.error('Gagal mengambil data Survey:', err);
        if (!appendData) state.ahsDatabase = [];
    } finally {
        state.isFetching = false;
    }
}

export async function fetchEstimatorIdItems(page = 1, q = '', appendData = false) {
    if (state.isFetching || (!state.hasMoreData && page > 1)) return;
    state.isFetching = true;

    try {
        const params = new URLSearchParams({ page, q, tipe: state.activeFilter });
        const res  = await fetch('/api/ahs/estimatorid?' + params.toString());
        if (!res.ok) throw new Error('API Error');
        const json = await res.json();

        if (json.status === 'success' && Array.isArray(json.data)) {
            state.ahsDatabase = appendData ? state.ahsDatabase.concat(json.data) : json.data;
            state.hasMoreData = json.data.length >= (json.limit ?? 50);
            state.currentPage = page;
        }
    } catch (err) {
        console.error('Gagal mengambil data Estimator.id:', err);
        if (!appendData) state.ahsDatabase = [];
    } finally {
        state.isFetching = false;
    }
}

export async function fetchRincianAHS(idDetail) {
    try {
        const res = await fetch(`/api/ahs/rincian/${idDetail}`);
        if (!res.ok) throw new Error('Fetch Error');
        const json = await res.json();
        return json.status === 'success' ? json.data : [];
    } catch (err) {
        console.error('Gagal memuat rincian:', err);
        return [];
    }
}

export async function saveRincianAHS(payload) {
    try {
        const res = await fetch('/api/ahs/rincian', {
            method  : 'POST',
            headers : { 'Content-Type': 'application/json' },
            body    : JSON.stringify(payload)
        });
        if (!res.ok) throw new Error('Save Error');
        return await res.json();
    } catch (err) {
        console.error('Gagal menyimpan rincian:', err);
        throw err;
    }
}
