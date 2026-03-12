/**
 * tambah-ahs/core/data.js
 * Fetches AHS item data from /api/pekerjaan with pagination + filters.
 */

import { PAGE_SIZE } from './state.js';

export async function fetchTambahAhsData(query, sources, page) {
    try {
        const url = new URL('/api/pekerjaan', window.location.origin);
        url.searchParams.append('page',  page);
        url.searchParams.append('limit', PAGE_SIZE);
        if (query)                  url.searchParams.append('q', query);
        if (sources && sources.length > 0) sources.forEach(s => url.searchParams.append('sumber[]', s));

        const res = await fetch(url.toString());
        if (!res.ok) throw new Error('Gagal mengambil data dari server');
        const result = await res.json();
        return { total: result.total || 0, page: result.page || 1, data: result.data || [] };
    } catch (err) {
        console.error('API Fetch Error:', err);
        return { total: 0, page: 1, data: [] };
    }
}
