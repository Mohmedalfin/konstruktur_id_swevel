/**
 * ahs/core/data.js
 * Data layer for AHS — fetches from /api/ahs.
 * Replace or extend fetchAhsDatabase() when the API shape changes.
 */

import { state } from './state.js';

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
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (!res.ok) throw new Error('Save Error');
        return await res.json();
    } catch (err) {
        console.error('Gagal menyimpan rincian:', err);
        throw err;
    }
}
