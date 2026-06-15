// public/js/team-accounts/index.js
import { toast } from '../shared/ui/toast.js';
import { confirmAction } from '../shared/ui/confirm.js';

document.addEventListener('DOMContentLoaded', () => {
    // Config URLs from layout
    const urls = window.TEAM_ACCOUNTS_INIT || {
        listUrl: '/kelola-akun/data',
        createUrl: '/kelola-akun/create',
        deleteUrl: '/kelola-akun/delete'
    };

    // Tab switcher elements
    const tabActiveBtn = document.getElementById('tab-active');
    const tabPendingBtn = document.getElementById('tab-pending');
    const panelActive = document.getElementById('panel-active');
    const panelPending = document.getElementById('panel-pending');
    const pendingCountBadge = document.getElementById('pending-count');

    // Form elements
    const roleSelect = document.getElementById('subaccount-role');
    const emailInput = document.getElementById('subaccount-email');
    const submitBtn = document.getElementById('subaccount-submit');

    // List elements
    const activeListTbody = document.getElementById('subaccount-list');
    const inviteListTbody = document.getElementById('invite-list');

    // State
    let activeTab = 'active'; // 'active' or 'pending'

    // Toast Helper
    function showToast(message, type = 'success') {
        toast.show(message, type);
    }

    // Tab Switcher Logic
    function switchTab(tab) {
        activeTab = tab;
        if (tab === 'active') {
            tabActiveBtn.className = "py-2.5 px-4 text-xs font-bold border-b-2 border-primary text-primary focus:outline-none flex items-center gap-2";
            tabPendingBtn.className = "py-2.5 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none flex items-center gap-2";
            panelActive.classList.remove('hidden');
            panelPending.classList.add('hidden');
            loadActiveAccounts();
        } else {
            tabPendingBtn.className = "py-2.5 px-4 text-xs font-bold border-b-2 border-primary text-primary focus:outline-none flex items-center gap-2";
            tabActiveBtn.className = "py-2.5 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none flex items-center gap-2";
            panelPending.classList.remove('hidden');
            panelActive.classList.add('hidden');
            loadPendingInvitations();
        }
    }

    tabActiveBtn.addEventListener('click', () => switchTab('active'));
    tabPendingBtn.addEventListener('click', () => switchTab('pending'));

    // Fetch and Render Active Accounts
    async function loadActiveAccounts() {
        activeListTbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-slate-400 font-medium">Memuat data tim...</td></tr>`;
        
        try {
            const response = await fetch(urls.listUrl);
            const res = await response.json();
            
            if (res.success && res.data.length > 0) {
                activeListTbody.innerHTML = res.data.map(user => `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-slate-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                user.kategori_akun.toLowerCase() === 'gudang' 
                                    ? 'bg-blue-50 text-blue-700 border border-blue-200/50' 
                                    : 'bg-indigo-50 text-indigo-700 border border-indigo-200/50'
                            }">
                                <i class="fas ${user.kategori_akun.toLowerCase() === 'gudang' ? 'fa-warehouse' : 'fa-wallet'} text-[9px]"></i>
                                ${user.kategori_akun.toUpperCase()}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800">${escapeHtml(user.nama_pengguna)}</td>
                        <td class="px-4 py-3 font-mono text-slate-600">@${escapeHtml(user.username)}</td>
                        <td class="px-4 py-3 text-slate-500">${escapeHtml(user.email || '-')}</td>
                        <td class="px-4 py-3 text-right">
                            <button data-id="${user.id_pengguna}" class="btn-delete-user inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition-colors focus:outline-none">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
                
                // Add event listeners for delete
                document.querySelectorAll('.btn-delete-user').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        const id = btn.getAttribute('data-id');
                        const row = btn.closest('tr');
                        const nama = row ? row.querySelector('td:nth-child(2)').textContent.trim() : 'akun tim ini';
                        
                        const ok = await confirmAction(
                            'Hapus Akun Tim?',
                            `Apakah Anda yakin ingin menghapus akun tim <strong>${nama}</strong> secara permanen?`,
                            'Ya, Hapus'
                        );
                        if (ok) {
                            await deleteActiveAccount(id);
                        }
                    });
                });
            } else {
                activeListTbody.innerHTML = `<tr><td colspan="5" class="px-4 py-10 text-center text-slate-400 font-medium">Belum ada akun tim aktif.</td></tr>`;
            }
        } catch (err) {
            console.error(err);
            activeListTbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-rose-500 font-semibold">Gagal memuat data dari server.</td></tr>`;
        }
    }

    // Fetch and Render Pending Invitations
    async function loadPendingInvitations() {
        inviteListTbody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 font-medium">Memuat undangan...</td></tr>`;
        
        try {
            const response = await fetch('/kelola-akun/invitations');
            const res = await response.json();
            
            if (res.success) {
                // Update badge count
                pendingCountBadge.textContent = res.data.length;

                if (res.data.length > 0) {
                    inviteListTbody.innerHTML = res.data.map(invite => {
                        const inviteLink = `${window.location.origin}/accept-invite?token=${invite.token}`;
                        return `
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-800">${escapeHtml(invite.email)}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/50">
                                        ${invite.kategori_akun.toUpperCase()}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 font-mono text-[10px]">${formatDate(invite.expires_at)}</td>
                                <td class="px-4 py-3 text-right flex items-center justify-end gap-2">
                                    <button data-link="${inviteLink}" class="btn-copy-invite inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-[10px] font-bold text-slate-700 focus:outline-none transition-colors">
                                        <i class="fas fa-copy"></i>
                                        Link
                                    </button>
                                    <button data-id="${invite.id}" class="btn-delete-invite inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition-colors focus:outline-none">
                                        <i class="fas fa-xmark text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                    
                    // Add Copy Link Listeners
                    document.querySelectorAll('.btn-copy-invite').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const link = btn.getAttribute('data-link');
                            navigator.clipboard.writeText(link)
                                .then(() => {
                                    btn.innerHTML = '<i class="fas fa-check"></i> Disalin';
                                    btn.className = "inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-[10px] font-bold text-emerald-700 focus:outline-none transition-all";
                                    showToast('Link undangan disalin!', 'success');
                                    setTimeout(() => {
                                        btn.innerHTML = '<i class="fas fa-copy"></i> Link';
                                        btn.className = "inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-[10px] font-bold text-slate-700 focus:outline-none transition-colors";
                                    }, 2000);
                                })
                                .catch(() => showToast('Gagal menyalin link.', 'error'));
                        });
                    });

                    // Add Delete Invitation Listeners
                    document.querySelectorAll('.btn-delete-invite').forEach(btn => {
                        btn.addEventListener('click', async () => {
                            const id = btn.getAttribute('data-id');
                            const row = btn.closest('tr');
                            const email = row ? row.querySelector('td:first-child').textContent.trim() : 'undangan ini';
                            
                            const ok = await confirmAction(
                                'Batalkan Undangan?',
                                `Apakah Anda yakin ingin membatalkan undangan ke <strong>${email}</strong>? Tautan aktivasi akan dinonaktifkan.`,
                                'Ya, Batalkan'
                            );
                            if (ok) {
                                await deleteInvitation(id);
                            }
                        });
                    });
                } else {
                    inviteListTbody.innerHTML = `<tr><td colspan="4" class="px-4 py-10 text-center text-slate-400 font-medium">Tidak ada undangan pending.</td></tr>`;
                }
            }
        } catch (err) {
            console.error(err);
            inviteListTbody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-rose-500 font-semibold">Gagal memuat undangan.</td></tr>`;
        }
    }

    // Helper: update pending count on startup
    async function updatePendingCountOnStartup() {
        try {
            const response = await fetch('/kelola-akun/invitations');
            const res = await response.json();
            if (res.success) {
                pendingCountBadge.textContent = res.data.length;
            }
        } catch (e) {
            console.error(e);
        }
    }

    // Submit Invitation
    submitBtn.addEventListener('click', async () => {
        const role = roleSelect.value;
        const email = emailInput.value.trim();

        if (!role) {
            showToast('Pilih role akses tim terlebih dahulu!', 'error');
            return;
        }

        if (!email) {
            showToast('Alamat email wajib diisi!', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.querySelector('span').textContent = 'Mengirim...';
        
        const formData = new FormData();
        formData.append('kategori_akun', role);
        formData.append('email', email);

        try {
            const response = await fetch(urls.createUrl, {
                method: 'POST',
                body: formData
            });
            const res = await response.json();

            if (res.success) {
                showToast(res.message, 'success');
                emailInput.value = '';
                roleSelect.value = '';

                // Reload current lists
                if (activeTab === 'active') {
                    loadActiveAccounts();
                } else {
                    loadPendingInvitations();
                }
                updatePendingCountOnStartup();
            } else {
                showToast(res.message || 'Gagal mengirimkan undangan.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Kesalahan koneksi server.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.querySelector('span').textContent = 'Kirim Undangan';
        }
    });

    // Delete Active User
    async function deleteActiveAccount(id) {
        try {
            const response = await fetch(`${urls.deleteUrl}/${id}`, {
                method: 'DELETE'
            });
            const res = await response.json();
            
            if (res.success) {
                showToast(res.message, 'success');
                loadActiveAccounts();
            } else {
                showToast(res.message || 'Gagal menghapus akun.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Terjadi kesalahan koneksi.', 'error');
        }
    }

    // Cancel Invitation
    async function deleteInvitation(id) {
        try {
            const response = await fetch(`/kelola-akun/delete-invitation/${id}`, {
                method: 'DELETE'
            });
            const res = await response.json();
            
            if (res.success) {
                showToast(res.message, 'success');
                loadPendingInvitations();
            } else {
                showToast(res.message || 'Gagal membatalkan undangan.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Terjadi kesalahan koneksi.', 'error');
        }
    }

    // Utility: escaping HTML to prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
    }

    // Utility: Format date
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleString('id-ID', {
            day: 'numeric',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Setup global refreshes
    document.getElementById('subaccount-refresh')?.addEventListener('click', () => {
        if (activeTab === 'active') {
            loadActiveAccounts();
        } else {
            loadPendingInvitations();
        }
    });

    // Initial Load
    loadActiveAccounts();
    updatePendingCountOnStartup();
});
