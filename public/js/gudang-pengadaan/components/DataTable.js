export class DataTable {
    constructor(containerId, onRowClick, onDeleteClick) {
        this.container = document.getElementById(containerId);
        this.onRowClick = onRowClick;
        this.onDeleteClick = onDeleteClick;
    }

    render(state) {
        if (!this.container) return;

        if (state.loading.data) {
            this.container.innerHTML = `
                <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                    <p class="text-sm font-semibold">Memuat riwayat pengajuan...</p>
                </div>
            `;
            return;
        }

        const data = state.data;

        if (!data || data.length === 0) {
            this.container.innerHTML = `
                <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan</p>
                    <p class="text-xs mt-1">Tidak ada data yang sesuai dengan filter saat ini.</p>
                </div>
            `;
            return;
        }

        let html = '';

        data.forEach(item => {
            const badgeData = this.getStatusBadgeData(item.status);
            
            let formattedDate = item.request_date;
            if (formattedDate) {
                const parts = formattedDate.split('-');
                if (parts.length === 3) {
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    formattedDate = `${parseInt(parts[2])} ${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
                }
            }

            html += `
                <div class="pengadaan-card bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-4 transition-all hover:shadow-md cursor-pointer group" data-id="${item.id}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        
                        <!-- Left Side: Icon & Info -->
                        <div class="flex items-start sm:items-center gap-4 flex-1">
                            <div class="w-12 h-12 rounded-full ${badgeData.iconBg} flex items-center justify-center shrink-0">
                                <i class="fas ${badgeData.iconName} text-xl"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center flex-wrap gap-3">
                                    <span class="font-bold text-blue-700 text-base sm:text-lg group-hover:text-blue-800 transition-colors">${item.pr_number || '-'}</span>
                                    <span class="hidden sm:inline-block w-px h-4 bg-slate-200"></span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide ${badgeData.statusBadgeClass}">
                                        <span class="w-1 h-1 rounded-full ${badgeData.borderLeftColor}"></span>
                                        ${badgeData.statusLabel}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 font-medium">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-slate-400"></i> ${formattedDate || '-'}</span>
                                    <span class="hidden sm:inline-block w-px h-3 bg-slate-200"></span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-user text-slate-400"></i> ${item.created_by_name || '-'}</span>
                                    <span class="hidden sm:inline-block w-px h-3 bg-slate-200"></span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-box text-slate-400"></i> ${item.total_items || 0} Item</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            ${(item.status === 'draft' || item.status === 'pending') ? `
                            <button type="button" class="btn-delete w-9 h-9 flex items-center justify-center bg-white border border-rose-200 text-rose-600 rounded-lg hover:bg-rose-50 hover:border-rose-300 transition-colors focus:outline-none shadow-sm" data-id="${item.id}" title="Hapus / Batal Pengajuan">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            ` : ''}
                            <button type="button" class="btn-detail px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-colors flex items-center gap-2 focus:outline-none shadow-sm" data-id="${item.id}">
                                Lihat Detail <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                            </button>
                        </div>

                    </div>

                    <!-- Bottom Side: Note -->
                    ${item.keterangan ? `
                        <div class="h-px bg-slate-100 my-4"></div>
                        <div class="flex items-center">
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 w-full truncate" title="${item.keterangan}">
                                <i class="fas fa-comment-dots text-slate-400 shrink-0"></i>
                                <span class="truncate italic">${item.keterangan}</span>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        });

        this.container.innerHTML = html;

        // Attach event listeners
        const cards = this.container.querySelectorAll('.pengadaan-card');
        cards.forEach(card => {
            card.addEventListener('click', (e) => {
                // Prevent duplicate click if clicking the detail button directly
                if (e.target.closest('.btn-detail')) return;
                
                const id = card.getAttribute('data-id');
                if (this.onRowClick && id) {
                    this.onRowClick(id);
                }
            });
        });

        const detailBtns = this.container.querySelectorAll('.btn-detail');
        detailBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation(); // Stop event bubbling
                const id = btn.getAttribute('data-id');
                if (this.onRowClick && id) {
                    this.onRowClick(id);
                }
            });
        });

        const deleteBtns = this.container.querySelectorAll('.btn-delete');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.getAttribute('data-id');
                if (this.onDeleteClick && id) {
                    this.onDeleteClick(id);
                }
            });
        });
    }

    getStatusBadgeData(status) {
        let statusBadgeClass = '';
        let statusLabel = '';
        let borderLeftColor = '';
        let iconBg = '';
        let iconName = '';

        switch (status) {
            case 'draft':
            case 'pending':
                statusBadgeClass = 'bg-amber-50 text-amber-600 border-transparent';
                statusLabel = 'Pending';
                borderLeftColor = 'bg-amber-500';
                iconBg = 'bg-amber-50 text-amber-500';
                iconName = 'fa-clock';
                break;
            case 'diproses':
                statusBadgeClass = 'bg-slate-50 text-slate-600 border-transparent';
                statusLabel = 'Diproses';
                borderLeftColor = 'bg-slate-400';
                iconBg = 'bg-slate-50 text-slate-500';
                iconName = 'fa-file-alt';
                break;
            case 'approved':
                statusBadgeClass = 'bg-blue-50 text-blue-600 border-transparent';
                statusLabel = 'Disetujui';
                borderLeftColor = 'bg-blue-500';
                iconBg = 'bg-blue-50 text-blue-500';
                iconName = 'fa-check';
                break;
            case 'ordered':
                statusBadgeClass = 'bg-indigo-50 text-indigo-600 border-transparent';
                statusLabel = 'Diproses PO';
                borderLeftColor = 'bg-indigo-500';
                iconBg = 'bg-indigo-50 text-indigo-500';
                iconName = 'fa-shopping-cart';
                break;
            case 'completed':
                statusBadgeClass = 'bg-emerald-50 text-emerald-600 border-transparent';
                statusLabel = 'Selesai';
                borderLeftColor = 'bg-emerald-500';
                iconBg = 'bg-emerald-50 text-emerald-500';
                iconName = 'fa-check-circle';
                break;
            case 'rejected':
                statusBadgeClass = 'bg-rose-50 text-rose-600 border-transparent';
                statusLabel = 'Ditolak';
                borderLeftColor = 'bg-rose-500';
                iconBg = 'bg-rose-50 text-rose-500';
                iconName = 'fa-times-circle';
                break;
            default:
                statusBadgeClass = 'bg-slate-50 text-slate-600 border-transparent';
                statusLabel = status;
                borderLeftColor = 'bg-slate-500';
                iconBg = 'bg-slate-50 text-slate-500';
                iconName = 'fa-file-alt';
        }

        return { statusBadgeClass, statusLabel, borderLeftColor, iconBg, iconName };
    }
}
