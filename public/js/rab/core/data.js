/**
 * rab/core/data.js
 * Data layer for RAP/RAB feature.
 */

export const dummyKategoriMaster = [
    { id: 'persiapan', nama: 'Pekerjaan Persiapan' },
    { id: 'struktur', nama: 'Pekerjaan Struktur' },
    { id: 'arsitektur', nama: 'Pekerjaan Arsitektur' },
    { id: 'mep', nama: 'Pekerjaan MEP' },
    { id: 'finishing', nama: 'Pekerjaan Finishing' },
];

export async function fetchRabData(idProject) {
    const url = new URL('/api/rap', window.location.origin);
    url.searchParams.set('id_project', idProject);

    const res = await fetch(url.toString());
    const json = await res.json();

    if (!res.ok || json.status !== 'success') {
        throw new Error(json.message || 'Gagal mengambil data RAP');
    }

    return json.data;
}

export async function fetchKategoriMaster(idProject) {
    const url = new URL('/api/rap/kategori-master', window.location.origin);
    url.searchParams.set('id_project', idProject);

    const res = await fetch(url.toString());
    const json = await res.json();

    if (!res.ok || json.status !== 'success') {
        throw new Error(json.message || 'Gagal mengambil kategori master');
    }

    return json.data || [];
}