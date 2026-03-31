/**
 * pekerjaan/core/data.js
 * Fetches AHS item data (estimator bawaan + kustom proyek) with pagination + filters.
 */

import { PAGE_SIZE } from './state.js';

// ── Helper: ambil slug dari URL parameter ──────────────────────────────────
function getProjectSlug() {
    const params = new URLSearchParams(window.location.search);
    return params.get('slug') || '';
}

// ── Fetch: gabungan data bawaan + kustom ───────────────────────────────────
export async function fetchTambahAhsData(query, sources, page) {
    try {
        const url  = new URL('/api/pekerjaan', window.location.origin);
        const slug = getProjectSlug();

        url.searchParams.append('page',  page);
        url.searchParams.append('limit', PAGE_SIZE);
        if (slug)                          url.searchParams.append('slug', slug);
        if (query)                         url.searchParams.append('q', query);
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

// ── CRUD: Simpan pekerjaan kustom ke DB lokal ─────────────────────────────
export async function savePekerjaanKustom({ nama_pekerjaan, satuan, id_kategori_pekerjaan }) {
    const slug = getProjectSlug();
    if (!slug) throw new Error('Slug proyek tidak ditemukan di URL');

    const res = await fetch('/api/pekerjaan/kustom', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body:    JSON.stringify({ slug, nama_pekerjaan, satuan, id_kategori_pekerjaan }),
    });
    if (!res.ok) throw new Error('Gagal menyimpan pekerjaan kustom');
    return res.json();
}

export async function updatePekerjaanKustom(id, { nama_pekerjaan, satuan }) {
    const res = await fetch(`/api/pekerjaan/kustom/${id}`, {
        method:  'PUT',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body:    JSON.stringify({ nama_pekerjaan, satuan }),
    });
    if (!res.ok) throw new Error('Gagal memperbarui pekerjaan');
    return res.json();
}

export async function deletePekerjaanKustom(id) {
    const res = await fetch(`/api/pekerjaan/kustom/${id}`, {
        method:  'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) throw new Error('Gagal menghapus pekerjaan');
    return res.json();
}
