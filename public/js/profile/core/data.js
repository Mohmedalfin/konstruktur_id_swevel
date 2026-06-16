import { setProfileData, setState } from "./state.js";

export async function fetchProfileData() {
    try {
        setState({ isLoading: true, error: null });
        const fetchUrl = window.PROFILE_INIT?.fetchUrl || "/profile/data";

        const response = await fetch(fetchUrl, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const jsonResponse = await response.json();

        if (!jsonResponse?.success) {
            throw new Error(jsonResponse?.message || "Gagal mengambil data profile");
        }

        setProfileData(jsonResponse.data);
        setState({ isLoading: false });

        return jsonResponse.data;
    } catch (error) {
        setState({
            isLoading: false,
            error: error.message || "Terjadi kesalahan saat memuat profile",
        });
        throw error;
    }
}

export async function updateProfileData(formData) {
    try {
        setState({ isSaving: true, error: null });
        const updateUrl = window.PROFILE_INIT?.updateUrl || "/profile/update";

        const response = await fetch(updateUrl, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        });

        const jsonResponse = await response.json();

        if (!response.ok || !jsonResponse?.success) {
            throw new Error(jsonResponse?.message || "Gagal menyimpan profile");
        }

        return jsonResponse;
    } finally {
        setState({ isSaving: false });
    }
}
