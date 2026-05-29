import { AppSwal, confirmAction } from "../shared/ui/confirm.js";
import { toast } from "../shared/ui/toast.js";

function fireAlert(options) {
    return AppSwal.fire({
        heightAuto: false,
        ...options,
    });
}

function notify(type, message) {
    if (type === "success") {
        toast.show(message, "success");
        return;
    }

    fireAlert({
        icon: type,
        title: type === "success" ? "Berhasil" : "Terjadi Kesalahan",
        text: message,
        confirmButtonText: "OK",
    });
}

function safe(value) {
    if (value === null || value === undefined) return "-";
    const text = String(value).trim();
    return text === "" ? "-" : text;
}

async function loadList() {
    const list = document.getElementById("subaccount-list");
    if (!list) return;

    try {
        list.innerHTML = `<tr><td colspan="5" class="px-3 py-4 text-center text-slate-400">Memuat data...</td></tr>`;
        const resp = await fetch(window.TEAM_ACCOUNTS_INIT?.listUrl || "/kelola-akun/data", {
            headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
        });
        const json = await resp.json();
        if (!resp.ok || !json?.success) throw new Error(json?.message || "Gagal memuat daftar akun");

        const items = Array.isArray(json.data) ? json.data : [];
        if (items.length === 0) {
            list.innerHTML = `<tr><td colspan="5" class="px-3 py-4 text-center text-slate-400">Belum ada akun tim.</td></tr>`;
            return;
        }

        list.innerHTML = items
            .map((item) => {
                return `<tr class="text-slate-700 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-2.5 font-semibold">${safe(item.kategori_akun)}</td>
                    <td class="px-3 py-2.5">${safe(item.nama_pengguna)}</td>
                    <td class="px-3 py-2.5">${safe(item.username)}</td>
                    <td class="px-3 py-2.5">${safe(item.email)}</td>
                    <td class="px-3 py-2.5 text-right">
                        <button type="button" class="btn-delete-subaccount inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200" data-id="${item.id_pengguna}" title="Hapus akun tim">
                            <i class="fas fa-trash-can text-[11px]"></i>
                        </button>
                    </td>
                </tr>`;
            })
            .join("");
    } catch (e) {
        list.innerHTML = `<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">Gagal memuat data</td></tr>`;
        notify("error", e.message || "Gagal memuat daftar akun");
    }
}

async function createAccount() {
    const role = document.getElementById("subaccount-role");
    const name = document.getElementById("subaccount-name");
    const username = document.getElementById("subaccount-username");
    const email = document.getElementById("subaccount-email");
    const password = document.getElementById("subaccount-password");

    if (!role || !name || !username || !password) return;

    const roleValue = role.value.trim();
    const nameValue = name.value.trim();
    const usernameValue = username.value.trim();
    const passwordValue = password.value;
    const emailValue = (email?.value || "").trim();

    if (!roleValue || !nameValue || !usernameValue || !passwordValue) {
        notify("error", "Role, nama, username, dan password wajib diisi.");
        return;
    }

    if (!/^[a-zA-Z0-9 ]+$/.test(usernameValue)) {
        notify("error", "Username hanya boleh huruf, angka, dan spasi.");
        return;
    }

    try {
        const payload = new FormData();
        payload.set("kategori_akun", roleValue);
        payload.set("nama_pengguna", nameValue);
        payload.set("username", usernameValue);
        payload.set("email", emailValue);
        payload.set("password", passwordValue);

        const resp = await fetch(window.TEAM_ACCOUNTS_INIT?.createUrl || "/kelola-akun/create", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: payload,
        });
        const json = await resp.json();
        if (!resp.ok || !json?.success) {
            const errors = json?.errors ? Object.values(json.errors).filter(Boolean).join(" ") : "";
            throw new Error(errors || json?.message || "Gagal menambahkan akun");
        }

        role.value = "";
        name.value = "";
        username.value = "";
        if (email) email.value = "";
        password.value = "";

        notify("success", "Akun tim berhasil ditambahkan.");
        await loadList();
    } catch (e) {
        notify("error", e.message || "Gagal menambahkan akun");
    }
}

async function deleteAccount(id) {
    if (!id) return;

    const confirmed = await confirmAction(
        "Apakah Anda yakin?",
        "Akun tim ini akan dihapus permanen dari sistem!",
        "Ya, Hapus!"
    );
    if (!confirmed) return;

    try {
        const baseUrl = window.TEAM_ACCOUNTS_INIT?.deleteUrl || "/kelola-akun/delete";
        const resp = await fetch(`${baseUrl}/${id}`, {
            method: "DELETE",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
        });
        const json = await resp.json();
        if (!resp.ok || !json?.success) throw new Error(json?.message || "Gagal menghapus akun");

        notify("success", "Akun tim berhasil dihapus.");
        await loadList();
    } catch (e) {
        notify("error", e.message || "Gagal menghapus akun");
    }
}

function initTeamAccounts() {
    const submit = document.getElementById("subaccount-submit");
    const refresh = document.getElementById("subaccount-refresh");
    const list = document.getElementById("subaccount-list");

    refresh?.addEventListener("click", loadList);
    submit?.addEventListener("click", createAccount);

    list?.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-delete-subaccount");
        if (!btn) return;
        const id = btn.getAttribute("data-id");
        await deleteAccount(id);
    });

    loadList();
}

document.addEventListener("DOMContentLoaded", initTeamAccounts);
