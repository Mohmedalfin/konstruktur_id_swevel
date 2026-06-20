export const ProyekAPI = {
    checkSisaStok: async (idProject) => {
        const res = await fetch(`${window.baseUrl}api/gudang-lapangan/sisa-stok/${idProject}`);
        const json = await res.json();
        if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal mengambil sisa stok');
        return json.data;
    },

    getActiveProjects: async () => {
        const res = await fetch(`${window.baseUrl}api/proyek/aktif`);
        const json = await res.json();
        if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal mengambil daftar proyek');
        return json.data;
    },

    selesaiReconcile: async (idProject, payload) => {
        const res = await fetch(`${window.baseUrl}api/proyek/selesai-reconcile/${idProject}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal menyelesaikan proyek');
        return json;
    },

    // Metode lama untuk proyek tanpa sisa (meskipun bisa dialihkan ke selesaiReconcile juga)
    selesaikanTanpaSisa: async (idProject) => {
        const res = await fetch(`${window.baseUrl}proyek/selesai/${idProject}`, { method: 'POST' });
        const json = await res.json();
        if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal menyelesaikan proyek');
        return json;
    },

    deleteProject: async (idProject) => {
        const res = await fetch(`${window.baseUrl}proyek/delete/${idProject}`, { method: 'DELETE' });
        const json = await res.json();
        if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Gagal menghapus proyek');
        return json;
    }
};
