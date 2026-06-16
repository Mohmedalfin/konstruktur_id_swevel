export async function fetchStats(month = '') {
    try {
        const url = month ? `/api/permintaan/stats?month=${month}` : '/api/permintaan/stats';
        const response = await fetch(url);
        if (!response.ok) throw new Error('Failed to fetch stats');
        const res = await response.json();
        return res.status === 'success' ? res.data : null;
    } catch (error) {
        console.error('Error fetching stats:', error);
        return null;
    }
}

export async function fetchRequests(status = 'all', month = '') {
    try {
        let url = `/api/permintaan/data?status=${status}`;
        if (month) {
            url += `&month=${month}`;
        }
        const response = await fetch(url);
        if (!response.ok) throw new Error('Failed to fetch requests');
        const res = await response.json();
        return res.status === 'success' ? res.data : [];
    } catch (error) {
        console.error('Error fetching requests:', error);
        return [];
    }
}

export async function fetchRequestDetail(id) {
    try {
        const response = await fetch(`/api/permintaan/detail/${id}`);
        if (!response.ok) throw new Error('Failed to fetch request detail');
        const res = await response.json();
        return res.status === 'success' ? res.data : null;
    } catch (error) {
        console.error('Error fetching request detail:', error);
        return null;
    }
}

export async function saveRequest(payload) {
    try {
        const response = await fetch('/api/permintaan/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const res = await response.json();
        return res;
    } catch (error) {
        console.error('Error saving request:', error);
        return { status: 'error', message: error.message || 'Terjadi kesalahan jaringan' };
    }
}

export async function updateRequestStatus(id, status) {
    try {
        const response = await fetch(`/api/permintaan/status/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status })
        });
        const res = await response.json();
        return res;
    } catch (error) {
        console.error('Error updating status:', error);
        return { status: 'error', message: error.message || 'Terjadi kesalahan jaringan' };
    }
}

export async function deleteRequest(id) {
    try {
        const response = await fetch(`/api/permintaan/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const res = await response.json();
        return res;
    } catch (error) {
        console.error('Error deleting request:', error);
        return { status: 'error', message: error.message || 'Terjadi kesalahan jaringan' };
    }
}

export async function fetchProjectRapItems(projectId) {
    try {
        const response = await fetch(`/api/permintaan/rap-items/${projectId}`);
        if (!response.ok) throw new Error('Failed to fetch project RAP items');
        const res = await response.json();
        return res.status === 'success' ? res.data : [];
    } catch (error) {
        console.error('Error fetching project RAP items:', error);
        return [];
    }
}
