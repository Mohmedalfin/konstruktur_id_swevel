import {
    fallbackValue,
    formatDateIndo,
    normalizeWebsiteUrl,
} from "../core/helpers.js";
import { getState, setSnapshot, setState } from "../core/state.js";

function getTextBindings() {
    return Array.from(document.querySelectorAll("[data-profile-text]"));
}

function getInputBindings() {
    return Array.from(document.querySelectorAll("[data-profile-input]"));
}

function setNodeVisibility(node, isVisible) {
    if (!node) return;
    node.hidden = !isVisible;
    if (isVisible) {
        node.classList.remove("!hidden");
    } else {
        node.classList.add("!hidden");
    }
}

export function renderProfileFields(profile) {
    getTextBindings().forEach((node) => {
        const key = node.dataset.profileText;
        let value = profile?.[key];

        if (key === "tgl_daftar_formatted") {
            value = formatDateIndo(profile?.tgl_daftar);
        }

        if (key === "jam_daftar_formatted") {
            value = fallbackValue(profile?.jam_daftar);
        }

        if (key === "website_label") {
            value = fallbackValue(profile?.website);
        }

        if (key === "wilayah_label") {
            value = fallbackValue(profile?.wilayah_label || profile?.id_wilayah);
        }

        node.textContent = fallbackValue(value);
    });

    getInputBindings().forEach((input) => {
        const key = input.dataset.profileInput;
        const value = profile?.[key] ?? "";
        input.value = value;
    });

    const websiteLink = document.getElementById("profile-website-link");
    if (websiteLink) {
        websiteLink.href = normalizeWebsiteUrl(profile?.website);
        websiteLink.classList.toggle("pointer-events-none", !profile?.website);
        websiteLink.classList.toggle("opacity-60", !profile?.website);
    }

    setSnapshot(createSnapshot());
}

export function createSnapshot() {
    return getInputBindings().reduce((snapshot, input) => {
        snapshot[input.name] = input.value;
        return snapshot;
    }, {});
}

export function restoreSnapshot() {
    const { snapshot } = getState();
    getInputBindings().forEach((input) => {
        if (Object.prototype.hasOwnProperty.call(snapshot, input.name)) {
            input.value = snapshot[input.name];
        }
    });
}

export function clearPasswordFields() {
    const passwordInput = document.getElementById("profile-password-input");
    const passwordConfirmation = document.getElementById("profile-password-confirmation");

    if (passwordInput) passwordInput.value = "";
    if (passwordConfirmation) passwordConfirmation.value = "";
}

export function setPasswordEditMode(isPasswordEditMode) {
    setState({ isPasswordEditMode });

    document.querySelectorAll(".profile-password-view").forEach((node) => {
        setNodeVisibility(node, !isPasswordEditMode);
    });

    document.querySelectorAll(".profile-password-edit").forEach((node) => {
        setNodeVisibility(node, isPasswordEditMode);
    });
}

export function setEditMode(isEditMode) {
    setState({ isEditMode });

    document.querySelectorAll(".profile-view").forEach((node) => {
        setNodeVisibility(node, !isEditMode);
    });

    document.querySelectorAll(".profile-edit").forEach((node) => {
        setNodeVisibility(node, isEditMode);
    });

    document.querySelectorAll("[data-profile-edit-only]").forEach((node) => {
        setNodeVisibility(node, isEditMode);
    });

    const editButton = document.getElementById("profile-edit-toggle");
    const cancelButton = document.getElementById("profile-cancel-edit");
    const saveButton = document.getElementById("profile-save");

    setNodeVisibility(editButton, !isEditMode);
    setNodeVisibility(cancelButton, isEditMode);
    setNodeVisibility(saveButton, isEditMode);
}
