export async function fetchStats() {
    try {
        const response = await fetch('/api/stok/stats');
        if (!response.ok) throw new Error('Failed to fetch stok stats');
        const res = await response.json();
        return res.status === 'success' ? res.data : null;
    } catch (error) {
        console.error('Error fetching stok stats:', error);
        return null;
    }
}

export async function fetchStokData(kategori = 'all', status = 'all', search = '') {
    try {
        let url = `/api/stok/data?kategori=${kategori}&status=${status}`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }
        const response = await fetch(url);
        if (!response.ok) throw new Error('Failed to fetch stok data');
        const res = await response.json();
        return res.status === 'success' ? res.data : [];
    } catch (error) {
        console.error('Error fetching stok data:', error);
        return [];
    }
}
