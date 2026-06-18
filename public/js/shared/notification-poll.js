const NotificationPoller = {
    pollInterval: 30000, // 30 detik
    apiEndpoint: '/api/notifications/unread',
    timerId: null,

    init() {
        if (!document.getElementById('hs-header-notification-dropdown')) return; // Jika tidak ada bel notif, skip

        this.fetchUnread();
        this.timerId = setInterval(() => this.fetchUnread(), this.pollInterval);

        // Bind event mark as read jika ada tombol
        document.addEventListener('click', (e) => {
            const markReadBtn = e.target.closest('.mark-notif-read');
            if (markReadBtn) {
                e.preventDefault();
                this.markAsRead(markReadBtn.dataset.id);
            }
        });
    },

    async fetchUnread() {
        try {
            const response = await fetch(this.apiEndpoint);
            if (!response.ok) return;

            const data = await response.json();
            this.updateBadge(data.unread_count);
            this.updateDropdown(data.recent);
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        }
    },

    updateBadge(count) {
        const badges = document.querySelectorAll('.notif-badge-count'); // Akan kita tambahkan class ini ke span badge lonceng
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.textContent = '0';
                badge.classList.add('hidden');
            }
        });
        
        // Update teks di header dropdown
        const headerCounts = document.querySelectorAll('.notif-header-count');
        headerCounts.forEach(el => el.textContent = `${count} Baru`);
    },

    updateDropdown(recentNotifs) {
        const containerList = document.querySelectorAll('.notif-dropdown-list'); // Akan kita tambahkan class ini ke div max-h-72
        
        containerList.forEach(container => {
            if (!recentNotifs || recentNotifs.length === 0) {
                container.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                        <p class="text-xs">Tidak ada notifikasi baru.</p>
                    </div>`;
                return;
            }

            let html = '';
            recentNotifs.forEach(notif => {
                const bgClass = notif.is_read ? 'bg-white' : `bg-${notif.color}-50/20`;
                const iconBgClass = `bg-${notif.color}-100 text-${notif.color}-600`;

                html += `
                <a class="p-3 flex items-start gap-3 hover:bg-gray-50 ${bgClass} border-b border-gray-100 transition-colors" href="${notif.link || '#'}">
                    <div class="shrink-0 p-2 ${iconBgClass} rounded-full mt-0.5">
                        <i class="${notif.icon || 'fa-solid fa-bell'} w-3.5 h-3.5 text-center flex items-center justify-center"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800 font-bold">${notif.title}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">${notif.message}</p>
                        <p class="text-[10px] text-${notif.color}-600 font-semibold mt-1">${notif.waktu}</p>
                    </div>
                    ${!notif.is_read ? `<div class="shrink-0 w-2 h-2 bg-${notif.color}-600 rounded-full mt-2" title="Belum dibaca"></div>` : ''}
                </a>`;
            });
            container.innerHTML = html;
        });
    },

    async markAsRead(id) {
        try {
            await fetch(`/api/notifications/mark-read/${id}`, { method: 'POST' });
            this.fetchUnread(); // Refresh count
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    NotificationPoller.init();
});
