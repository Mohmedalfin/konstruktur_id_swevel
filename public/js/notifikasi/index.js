const NotifikasiPage = {
    apiEndpoint: '/api/notifications',
    notifications: [],
    currentFilter: 'all',
    isLoading: false,

    init() {
        this.fetchData();
        this.bindStaticEvents();
    },

    // =============================================
    //  DATA
    // =============================================
    async fetchData() {
        this.showLoading();
        try {
            const res = await fetch(this.apiEndpoint);
            if (!res.ok) throw new Error('Network response was not ok');
            this.notifications = await res.json();
            this.render();
        } catch (error) {
            console.error('Error fetching notifications:', error);
            document.getElementById('notif-list').innerHTML = `
                <div class="text-center text-red-500 py-16 flex flex-col items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-4xl text-red-300"></i>
                    <p class="text-sm font-medium">Gagal memuat notifikasi. Coba refresh halaman.</p>
                </div>`;
        }
    },

    showLoading() {
        const list = document.getElementById('notif-list');
        if (list) {
            list.innerHTML = `
                <div class="text-center text-gray-500 py-16 flex flex-col items-center gap-3">
                    <i class="fa-solid fa-circle-notch fa-spin text-4xl text-gray-300"></i>
                    <p class="text-sm">Memuat notifikasi...</p>
                </div>`;
        }
    },

    // =============================================
    //  EVENTS  
    // =============================================
    bindStaticEvents() {
        // -- Tab Filters
        document.querySelectorAll('.notif-filter').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.currentTarget;

                document.querySelectorAll('.notif-filter').forEach(f => {
                    f.classList.remove('active-filter', 'text-primary', 'bg-primary/10', 'font-semibold');
                    f.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
                    const span = f.querySelector('span[id^="count-"]');
                    if (span) {
                        span.classList.remove('bg-primary', 'text-white');
                        span.classList.add('bg-gray-200', 'text-gray-600');
                    }
                });

                target.classList.add('active-filter', 'text-primary', 'bg-primary/10', 'font-semibold');
                target.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
                const activeSpan = target.querySelector('span[id^="count-"]');
                if (activeSpan) {
                    activeSpan.classList.add('bg-primary', 'text-white');
                    activeSpan.classList.remove('bg-gray-200', 'text-gray-600');
                }

                this.currentFilter = target.dataset.filter;
                this.render();
            });
        });

        // -- Tombol "Tandai Semua Dibaca" — by ID agar reliable
        const markAllBtn = document.getElementById('btn-mark-all-read');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllAsRead());
        }

        // -- Tombol "Hapus Semua" — by ID
        const clearAllBtn = document.getElementById('btn-clear-all');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => this.clearAllNotifications());
        }

        // -- Delegasi klik untuk tombol aksi per-item (mark read & delete)
        //    Delegasi ke #notif-list sehingga tetap bekerja setelah innerHTML diganti
        const list = document.getElementById('notif-list');
        if (list) {
            list.addEventListener('click', (e) => {
                const markReadBtn = e.target.closest('.action-mark-read');
                if (markReadBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.markAsRead(markReadBtn.dataset.id);
                    return;
                }

                const deleteBtn = e.target.closest('.action-delete');
                if (deleteBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.deleteNotification(deleteBtn.dataset.id);
                }
            });
        }
    },

    // =============================================
    //  RENDER
    // =============================================
    render() {
        const listContainer = document.getElementById('notif-list');
        if (!listContainer) return;

        let filtered = this.notifications;
        if (this.currentFilter !== 'all') {
            filtered = this.notifications.filter(n => {
                const catLower = n.source_module ? n.source_module.toLowerCase() : '';
                if (this.currentFilter === 'sistem') {
                    return catLower !== 'gudang' && catLower !== 'proyek' && catLower !== 'purchasing';
                }
                return catLower === this.currentFilter;
            });
        }

        this.updateCounts();
        this.updateHeaderButtons();

        if (filtered.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center text-gray-400 py-16 flex flex-col items-center gap-3">
                    <i class="fa-regular fa-bell-slash text-5xl text-gray-200"></i>
                    <p class="text-sm font-medium">Tidak ada notifikasi di sini.</p>
                </div>`;
            return;
        }

        let html = '';
        filtered.forEach(notif => {
            const isRead = parseInt(notif.is_read) === 1;
            const colorMap = {
                blue: { bg: '#eff6ff', icon: '#dbeafe', text: '#1d4ed8' },
                green: { bg: '#f0fdf4', icon: '#dcfce7', text: '#16a34a' },
                red: { bg: '#fef2f2', icon: '#fee2e2', text: '#dc2626' },
                orange: { bg: '#fff7ed', icon: '#ffedd5', text: '#ea580c' },
                purple: { bg: '#faf5ff', icon: '#f3e8ff', text: '#9333ea' },
                yellow: { bg: '#fefce8', icon: '#fef9c3', text: '#ca8a04' },
            };
            const color = colorMap[notif.color] || colorMap.blue;
            const label = notif.source_module ? notif.source_module.toUpperCase() : 'SISTEM';
            const rowBg = isRead ? '#ffffff' : color.bg;

            html += `
            <div class="notif-item flex gap-4 px-5 py-4 transition-colors border-b border-gray-100 last:border-0 hover:bg-gray-50/70"
                 style="background-color: ${rowBg};"
                 data-id="${notif.id}">

                <!-- Ikon -->
                <div class="shrink-0 mt-0.5">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         style="background-color: ${color.icon}; color: ${color.text};">
                        <i class="${notif.icon || 'fa-solid fa-bell'} text-sm"></i>
                    </div>
                </div>

                <!-- Konten -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 mb-1">
                        <h3 class="text-sm font-bold text-gray-800 leading-snug flex items-center gap-2">
                            ${notif.title}
                            ${!isRead ? `<span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background-color: ${color.text};" title="Belum dibaca"></span>` : ''}
                        </h3>
                        <span class="text-[11px] text-gray-400 whitespace-nowrap flex-shrink-0">
                            <i class="fa-regular fa-clock mr-0.5"></i> ${notif.waktu || '-'}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-3 leading-relaxed">${notif.message}</p>

                    <!-- Footer aksi -->
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full"
                                  style="background-color: ${color.icon}; color: ${color.text};">
                                ${label}
                            </span>
                        </div>

                        <!-- Tombol Aksi inline — tidak bergantung Preline dropdown -->
                        <div class="flex items-center gap-1">
                            ${!isRead ? `
                            <button type="button"
                                    class="action-mark-read inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-green-50 hover:text-green-700 hover:border-green-200 transition-colors"
                                    data-id="${notif.id}"
                                    title="Tandai sudah dibaca">
                                <i class="fa-solid fa-check text-[10px]"></i> Dibaca
                            </button>` : `
                            <span class="inline-flex items-center gap-1 text-xs text-gray-400 px-2 py-1">
                                <i class="fa-solid fa-check-double text-[10px]"></i> Sudah dibaca
                            </span>`}
                            <button type="button"
                                    class="action-delete inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors"
                                    data-id="${notif.id}"
                                    title="Hapus notifikasi ini">
                                <i class="fa-regular fa-trash-can text-[10px]"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        listContainer.innerHTML = html;
    },

    // =============================================
    //  UPDATE UI
    // =============================================
    updateCounts() {
        const counts = { all: 0, gudang: 0, proyek: 0, purchasing: 0, sistem: 0 };
        counts.all = this.notifications.length;

        this.notifications.forEach(n => {
            const cat = n.source_module ? n.source_module.toLowerCase() : '';
            if (cat === 'gudang') counts.gudang++;
            else if (cat === 'proyek') counts.proyek++;
            else if (cat === 'purchasing') counts.purchasing++;
            else counts.sistem++;
        });

        Object.keys(counts).forEach(key => {
            const span = document.getElementById(`count-${key}`);
            if (span) {
                span.textContent = counts[key];
                if (counts[key] > 0) span.classList.remove('hidden');
                else if (key !== 'all') span.classList.add('hidden');
            }
        });
    },

    updateHeaderButtons() {
        const unreadCount = this.notifications.filter(n => parseInt(n.is_read) === 0).length;

        const markAllBtn = document.getElementById('btn-mark-all-read');
        if (markAllBtn) {
            markAllBtn.disabled = unreadCount === 0;
            markAllBtn.style.opacity = unreadCount === 0 ? '0.5' : '1';
            markAllBtn.style.cursor = unreadCount === 0 ? 'not-allowed' : 'pointer';
        }

        const clearAllBtn = document.getElementById('btn-clear-all');
        if (clearAllBtn) {
            clearAllBtn.disabled = this.notifications.length === 0;
            clearAllBtn.style.opacity = this.notifications.length === 0 ? '0.5' : '1';
            clearAllBtn.style.cursor = this.notifications.length === 0 ? 'not-allowed' : 'pointer';
        }
    },

    showToast(message, type = 'success') {
        const colors = {
            success: { bg: '#f0fdf4', border: '#86efac', text: '#16a34a', icon: 'fa-circle-check' },
            error:   { bg: '#fef2f2', border: '#fca5a5', text: '#dc2626', icon: 'fa-circle-xmark' },
            info:    { bg: '#eff6ff', border: '#93c5fd', text: '#1d4ed8', icon: 'fa-circle-info' },
        };
        const c = colors[type] || colors.success;

        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            display: flex; align-items: center; gap: 10px;
            background: ${c.bg}; border: 1px solid ${c.border}; color: ${c.text};
            padding: 12px 18px; border-radius: 10px;
            font-size: 13px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: slideIn 0.25s ease-out;
        `;
        toast.innerHTML = `<i class="fa-solid ${c.icon}"></i> ${message}`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.25s ease-in forwards';
            setTimeout(() => toast.remove(), 250);
        }, 2500);
    },

    // =============================================
    //  AKSI API
    // =============================================
    async markAsRead(id) {
        // Optimistic update dulu biar terasa cepat
        const notif = this.notifications.find(n => String(n.id) === String(id));
        if (!notif || parseInt(notif.is_read) === 1) return;

        notif.is_read = 1;
        this.render();

        try {
            const res = await fetch(`/api/notifications/mark-read/${id}`, { method: 'POST' });
            if (!res.ok) throw new Error('Gagal');
            if (window.NotificationPoller) window.NotificationPoller.fetchUnread();
            this.showToast('Notifikasi ditandai sudah dibaca.', 'success');
        } catch (error) {
            // Rollback jika gagal
            notif.is_read = 0;
            this.render();
            this.showToast('Gagal memperbarui status notifikasi.', 'error');
            console.error(error);
        }
    },

    async markAllAsRead() {
        const unread = this.notifications.filter(n => parseInt(n.is_read) === 0);
        if (unread.length === 0) return;

        // Optimistic update
        this.notifications.forEach(n => n.is_read = 1);
        this.render();

        try {
            const res = await fetch(`/api/notifications/mark-all-read`, { method: 'POST' });
            if (!res.ok) throw new Error('Gagal');
            if (window.NotificationPoller) window.NotificationPoller.fetchUnread();
            this.showToast(`${unread.length} notifikasi ditandai sudah dibaca.`, 'success');
        } catch (error) {
            // Rollback
            unread.forEach(n => n.is_read = 0);
            this.render();
            this.showToast('Gagal menandai semua notifikasi.', 'error');
            console.error(error);
        }
    },

    async deleteNotification(id) {
        const notif = this.notifications.find(n => String(n.id) === String(id));
        if (!notif) return;

        // Optimistic: hapus dari list dulu
        const idx = this.notifications.indexOf(notif);
        this.notifications.splice(idx, 1);
        this.render();

        try {
            const res = await fetch(`/api/notifications/delete/${id}`, { method: 'DELETE' });
            if (!res.ok) throw new Error('Gagal');
            if (window.NotificationPoller) window.NotificationPoller.fetchUnread();
            this.showToast('Notifikasi dihapus.', 'info');
        } catch (error) {
            // Rollback
            this.notifications.splice(idx, 0, notif);
            this.render();
            this.showToast('Gagal menghapus notifikasi.', 'error');
            console.error(error);
        }
    },

    async clearAllNotifications() {
        if (this.notifications.length === 0) return;

        const count = this.notifications.length;
        if (!confirm(`Hapus semua ${count} notifikasi? Tindakan ini tidak bisa dibatalkan.`)) return;

        const backup = [...this.notifications];
        this.notifications = [];
        this.render();

        try {
            // Hapus satu per satu via DELETE endpoint
            const deletePromises = backup.map(n =>
                fetch(`/api/notifications/delete/${n.id}`, { method: 'DELETE' })
            );
            await Promise.all(deletePromises);
            if (window.NotificationPoller) window.NotificationPoller.fetchUnread();
            this.showToast(`${count} notifikasi berhasil dibersihkan.`, 'info');
        } catch (error) {
            this.notifications = backup;
            this.render();
            this.showToast('Gagal menghapus semua notifikasi.', 'error');
            console.error(error);
        }
    },
};

// Inject keyframe animations untuk toast
(function injectStyles() {
    if (document.getElementById('notif-page-styles')) return;
    const style = document.createElement('style');
    style.id = 'notif-page-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateY(10px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to   { opacity: 0; }
        }
    `;
    document.head.appendChild(style);
})();

document.addEventListener('DOMContentLoaded', () => {
    NotifikasiPage.init();
});
