export async function fetchDashboardData() {
    try {
        const response = await fetch('/api/gudang/dashboard/data');
        if (!response.ok) throw new Error('Failed to fetch dashboard data');
        const res = await response.json();
        return res.status === 'success' ? res.data : null;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        return null;
    }
}
