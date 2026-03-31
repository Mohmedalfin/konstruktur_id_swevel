export function getProjectSlug() {
    const params = new URLSearchParams(window.location.search);
    return params.get('slug') || '';
}

export function fetchKategoriMaster() {
    const slug = getProjectSlug();
    if (!slug) return Promise.resolve([]);
    
    return fetch(`/api/kategori?slug=${slug}`)
        .then(res => res.json())
        .then(data => {
            // Map the API data {id_kategori_pekerjaan, kode_kategori, nama_kategori, sudah_digunakan} to {id, nama, db_id, sudah_digunakan}
            return data.map(item => ({
                db_id: item.id_kategori_pekerjaan,
                id: item.kode_kategori,
                nama: item.nama_kategori,
                sudah_digunakan: item.sudah_digunakan || false
            }));
        })
        .catch(err => {
            console.error('API Error (Kategori):', err);
            return [];
        });
}

export function fetchRabData(id) {
    return new Promise(resolve => {
        setTimeout(() => resolve(dummyDatabase[id] || { categories: [] }), 350);
    });
}
