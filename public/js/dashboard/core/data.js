import { setState, setDashboardData } from './state.js';

export async function fetchDashboardData() {
    try {
        const slug = window.DASHBOARD_INIT?.slug;
        const idProject = window.DASHBOARD_INIT?.idProject;
        
        if (!slug || !idProject) {
            throw new Error("Project slug atau ID tidak ditemukan di window.DASHBOARD_INIT");
        }

        setState({ isLoading: true, error: null });

        const url = `/proyek/${slug}/dashboard/getData?id_project=${idProject}`; 

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const jsonResponse = await response.json();

        // Mengecek struktur respons standar { status: 'success', data: {...} }
        if (jsonResponse && jsonResponse.status === 'success') {            
            setDashboardData(jsonResponse.data);
            setState({ isLoading: false });
            
            return jsonResponse.data;
        } else {
            throw new Error(jsonResponse.message || 'Gagal mengambil data dashboard');
        }

    } catch (error) {
        console.error("Dashboard Data Fetch Error:", error);
        setState({ 
            isLoading: false, 
            error: error.message || 'Terjadi kesalahan saat memuat data' 
        });
        
        throw error;
    }
}
