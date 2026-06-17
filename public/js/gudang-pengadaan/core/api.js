const { baseUrl } = window.PENGADAAN_INIT;

export const api = {
    /**
     * Get dashboard stats
     */
    getStats: async () => {
        const response = await fetch(`${baseUrl}api/pengadaan/stats`);
        if (!response.ok) throw new Error('Gagal mengambil statistik pengadaan');
        return response.json();
    },

    /**
     * Get paginated and filtered purchase requests
     */
    getData: async (params = {}) => {
        const queryParams = new URLSearchParams(params).toString();
        const response = await fetch(`${baseUrl}api/pengadaan/data?${queryParams}`);
        if (!response.ok) throw new Error('Gagal mengambil data pengadaan');
        return response.json();
    },

    /**
     * Get detail of a specific PR
     */
    getDetail: async (prId) => {
        const response = await fetch(`${baseUrl}api/pengadaan/detail/${prId}`);
        if (!response.ok) throw new Error('Gagal mengambil detail pengadaan');
        return response.json();
    },

    /**
     * Get list of critical items (stok aktual <= stok minimum)
     */
    getItemsKritis: async () => {
        const response = await fetch(`${baseUrl}api/pengadaan/items-kritis`);
        if (!response.ok) throw new Error('Gagal mengambil data stok kritis');
        return response.json();
    },

    /**
     * Store new purchase request
     */
    store: async (payload) => {
        const response = await fetch(`${baseUrl}api/pengadaan/store`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Gagal menyimpan pengajuan pengadaan');
        }
        return data;
    },

    /**
     * Search master barang for manual input
     */
    searchBarang: async (keyword) => {
        const response = await fetch(`${baseUrl}api/pengadaan/search-barang?q=${encodeURIComponent(keyword)}`);
        if (!response.ok) throw new Error('Gagal mencari barang');
        return response.json();
    },

    /**
     * Hapus pengajuan (Refund ke purchasing / batal draft)
     */
    destroy: async (prId) => {
        const response = await fetch(`${baseUrl}api/pengadaan/delete/${prId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Gagal menghapus pengajuan pengadaan');
        }
        return data;
    }
};
