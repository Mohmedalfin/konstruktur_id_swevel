import { renderProfileHeader } from "./components/ProfileHeader.js";
import {
    clearPasswordFields,
    createSnapshot,
    renderProfileFields,
    restoreSnapshot,
    setEditMode,
    setPasswordEditMode,
} from "./components/ProfileForm.js";
import { fetchProfileData, updateProfileData } from "./core/data.js";
import { getState, setSnapshot } from "./core/state.js";
import { toast } from "../shared/ui/toast.js";

function hideGlobalLoader() {
    if (window.GlobalLoader) {
        window.GlobalLoader.hide();
        return;
    }

    const loader = document.getElementById("global-page-loader");
    if (loader) {
        loader.classList.add("opacity-0");
        setTimeout(() => loader.classList.add("pointer-events-none"), 500);
    }
}

function notify(type, message) {
    const toastType = type === "success" ? "success" : "error";
    toast.show(message, toastType);
}

async function refreshProfileView() {
    const profile = await fetchProfileData();
    renderProfileHeader(profile);
    renderProfileFields(profile);
    return profile;
}

async function submitProfileForm(form) {
    const { isSaving } = getState();
    if (isSaving) return;

    const formData = new FormData(form);
    formData.delete("password_confirmation");
    formData.delete("password");

    try {
        await updateProfileData(formData);
        await refreshProfileView();
        setEditMode(false);
        notify("success", "Profile berhasil diperbarui.");
    } catch (error) {
        notify("error", error.message || "Gagal menyimpan profile.");
    }
}

async function submitPasswordForm(form) {
    const { isSaving } = getState();
    if (isSaving) return;

    const passwordInput = document.getElementById("profile-password-input");
    const passwordConfirmation = document.getElementById("profile-password-confirmation");

    if (!passwordInput?.value) {
        notify("error", "Password baru masih kosong.");
        return;
    }

    if (passwordInput.value !== passwordConfirmation?.value) {
        notify("error", "Konfirmasi password belum sama.");
        return;
    }

    try {
        const formData = new FormData();
        formData.set("password", passwordInput.value);

        await updateProfileData(formData);
        clearPasswordFields();
        setPasswordEditMode(false);
        notify("success", "Password berhasil diperbarui.");
    } catch (error) {
        notify("error", error.message || "Gagal menyimpan password.");
    }
}

function bindEvents() {
    const form = document.getElementById("profile-form");
    const editButton = document.getElementById("profile-edit-toggle");
    const cancelButton = document.getElementById("profile-cancel-edit");
    const passwordButton = document.getElementById("profile-password-toggle");
    const passwordCancelButton = document.getElementById("profile-password-cancel");
    const passwordSaveButton = document.getElementById("profile-password-save");
    const photoInput = document.getElementById("profile-photo-input");

    editButton?.addEventListener("click", () => {
        setSnapshot(createSnapshot());
        clearPasswordFields();
        setPasswordEditMode(false);
        setEditMode(true);
    });

    passwordButton?.addEventListener("click", () => {
        setEditMode(false);
        clearPasswordFields();
        setPasswordEditMode(true);
        document.getElementById("profile-password-input")?.focus();
    });

    cancelButton?.addEventListener("click", () => {
        restoreSnapshot();
        setEditMode(false);
    });

    passwordCancelButton?.addEventListener("click", () => {
        clearPasswordFields();
        setPasswordEditMode(false);
    });

    photoInput?.addEventListener("change", () => {
        const [file] = photoInput.files || [];
        if (!file) return;

        const preview = document.getElementById("profile-photo-preview");
        const fallback = document.getElementById("profile-photo-fallback");
        const objectUrl = URL.createObjectURL(file);

        if (preview && fallback) {
            preview.src = objectUrl;
            preview.hidden = false;
            fallback.hidden = true;
            preview.classList.remove("hidden");
            fallback.classList.add("hidden");
        }
    });

    form?.addEventListener("submit", async (event) => {
        event.preventDefault();
        await submitProfileForm(form);
    });

    passwordSaveButton?.addEventListener("click", async () => {
        await submitPasswordForm(form);
    });
}

async function initProfilePage() {
    bindEvents();
    setEditMode(false);
    setPasswordEditMode(false);

    try {
        await refreshProfileView();
        setEditMode(false);
        setPasswordEditMode(false);
    } catch (error) {
        notify("error", error.message || "Gagal memuat data profile.");
    } finally {
        hideGlobalLoader();
    }
}

document.addEventListener("DOMContentLoaded", initProfilePage);
