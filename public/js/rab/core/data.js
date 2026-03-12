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
    try {
        const baseUrl = (window.RAB_INIT && window.RAB_INIT.apiRapUrl)
            ? window.RAB_INIT.apiRapUrl
            : '/api/rap';

        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('id_project', idProject);

        const res = await fetch(url.toString());
        if (!res.ok) throw new Error('Gagal mengambil data RAP');

        const json = await res.json();
        return json.data || { categories: [] };
    } catch (err) {
        console.error(err);
        return { categories: [] };
    }
}