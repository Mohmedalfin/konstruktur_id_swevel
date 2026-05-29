import {
    fallbackValue,
    formatJoinedAt,
    getStatusClasses,
} from "../core/helpers.js";

export function renderProfileHeader(profile) {
    const companyName = document.getElementById("profile-company-name");
    const categoryBadge = document.getElementById("profile-category-badge");
    const statusBadge = document.getElementById("profile-status-badge");
    const statusInline = document.getElementById("profile-status-inline");
    const joinedAt = document.getElementById("profile-joined-at");
    const photoPreview = document.getElementById("profile-photo-preview");
    const photoFallback = document.getElementById("profile-photo-fallback");

    if (companyName) {
        companyName.textContent = fallbackValue(profile?.perusahaan || profile?.nama_pengguna, "Tanpa Nama");
    }

    if (categoryBadge) {
        categoryBadge.textContent = fallbackValue(profile?.kategori_akun);
    }

    if (statusBadge) {
        statusBadge.textContent = fallbackValue(profile?.status);
    }

    if (joinedAt) {
        joinedAt.textContent = formatJoinedAt(profile);
    }

    const statusClasses = getStatusClasses(profile?.status);

    if (statusBadge) {
        statusBadge.className = `px-2 py-0.5 rounded-full font-semibold ${statusClasses.badge}`;
    }

    if (statusInline) {
        statusInline.className = `inline-flex items-center gap-2 px-2 py-0.5 rounded-full font-semibold ${statusClasses.badge}`;
        const dot = statusInline.querySelector("span:first-child");
        if (dot) {
            dot.className = `w-1.5 h-1.5 rounded-full ${statusClasses.dot}`;
        }
    }

    if (photoPreview && photoFallback) {
        const hasPhoto = Boolean(profile?.foto_url);
        photoPreview.src = hasPhoto ? profile.foto_url : "";
        photoPreview.hidden = !hasPhoto;
        photoFallback.hidden = hasPhoto;
        photoPreview.classList.toggle("hidden", !hasPhoto);
        photoFallback.classList.toggle("hidden", hasPhoto);
    }
}
