export async function fetchKategoriMaster() {
    try {
        const url = (window.RAB_INIT && window.RAB_INIT.apiKategoriMaster)
            ? window.RAB_INIT.apiKategoriMaster
            : '/api/rap/kategori-master';

        const res = await fetch(url);
        if (!res.ok) throw new Error('Gagal mengambil kategori master');

        const json = await res.json();
        return json.data || [];
    } catch (err) {
        console.error(err);
        return [];
    }
}

export async function fetchRabData(idProject) {
    const res = await fetch(`/api/rap?id_project=${encodeURIComponent(idProject)}`, {
        headers: {
            Accept: 'application/json'
        }
    });

    const json = await res.json();

    if (!res.ok || json.status !== 'success') {
        throw new Error(json.message || 'Gagal mengambil data RAP');
    }

    return json.data;
}