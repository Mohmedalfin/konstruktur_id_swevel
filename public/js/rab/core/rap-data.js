/**
 * public/js/rab/core/rap-data.js
 * Fungsi fetch + CRUD untuk tabel RAP via API.
 */

function getSlug() {
    const params = new URLSearchParams(window.location.search);
    return params.get('slug') || '';
}

/**
 * GET /api/rap?slug=...
 * Mengembalikan data terkelompok per kategori dengan harga teragregasi.
 */
export async function fetchRapItems(slug) {
    const s = slug || getSlug();
    const res = await fetch(`/api/rap?slug=${encodeURIComponent(s)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) throw new Error('Gagal memuat data RAP');
    return res.json(); // { status, data: [ { id_kategori, nama_kategori, items: [] } ] }
}

/**
 * POST /api/rap
 * Simpan batch pekerjaan baru ke RAP.
 * @param {string} slug
 * @param {Array}  items  [{ id_pekerjaan, id_kategori, sumber, nama, satuan }]
 */
export async function batchSaveRap(slug, items) {
    const res = await fetch('/api/rap', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body:    JSON.stringify({ slug, items }),
    });
    if (!res.ok) throw new Error('Gagal menyimpan ke RAP');
    return res.json();
}

/**
 * PUT /api/rap/{id}
 * Update volume suatu baris RAP.
 */
export async function updateRapVolume(idRap, volume) {
    const res = await fetch(`/api/rap/${idRap}`, {
        method:  'PUT',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body:    JSON.stringify({ volume }),
    });
    if (!res.ok) throw new Error('Gagal memperbarui volume');
    return res.json();
}

/**
 * DELETE /api/rap/{id}
 * Hapus satu baris pekerjaan dari RAP (+ cascade rap_detail).
 */
export async function deleteRapItem(idRap) {
    const res = await fetch(`/api/rap/${idRap}`, {
        method:  'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) throw new Error('Gagal menghapus item RAP');
    return res.json();
}
