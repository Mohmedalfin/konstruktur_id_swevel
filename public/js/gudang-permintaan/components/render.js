export function renderStats(stats) {
    if (!stats) return;
    document.getElementById('stat-total').textContent = stats.total ?? 0;
    document.getElementById('stat-pending').textContent = stats.pending ?? 0;
    document.getElementById('stat-proses').textContent = stats.disetujui ?? 0;
    document.getElementById('stat-kirim').textContent = stats.selesai ?? 0;
}

export function renderRequestsList(requests) {
    const container = document.getElementById('history-container');
    if (!container) return;

    if (!Array.isArray(requests) || requests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm text-slate-400">
                <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                <p class="text-sm font-semibold">Belum ada riwayat permintaan item.</p>
            </div>
        `;
        return;
    }

    let html = '';
    requests.forEach(req => {
        // Status Badge Styling
        let statusBadgeClass = '';
        let statusLabel = '';
        let borderLeftColor = 'bg-slate-200';
        let iconBg = 'bg-slate-100 text-slate-500';
        let iconName = 'fa-file-alt';

        switch (req.status) {
            case 'pending':
                statusBadgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                statusLabel = 'Menunggu';
                borderLeftColor = 'bg-amber-400';
                iconBg = 'bg-amber-100 text-amber-600';
                iconName = 'fa-clock';
                break;
            case 'disetujui':
                statusBadgeClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                statusLabel = 'Diterima';
                borderLeftColor = 'bg-indigo-500';
                iconBg = 'bg-indigo-100 text-indigo-600';
                iconName = 'fa-check-double';
                break;
            case 'diproses':
                statusBadgeClass = 'bg-blue-100 text-blue-800 border-blue-200';
                statusLabel = 'Diproses';
                borderLeftColor = 'bg-blue-500';
                iconBg = 'bg-blue-100 text-blue-600';
                iconName = 'fa-cog fa-spin-hover';
                break;
            case 'selesai':
                statusBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                statusLabel = 'Terkirim';
                borderLeftColor = 'bg-emerald-500';
                iconBg = 'bg-emerald-100 text-emerald-600';
                iconName = 'fa-check-circle';
                break;
            case 'ditolak':
                statusBadgeClass = 'bg-rose-100 text-rose-800 border-rose-200';
                statusLabel = 'Ditolak';
                borderLeftColor = 'bg-rose-500';
                iconBg = 'bg-rose-100 text-rose-600';
                iconName = 'fa-times-circle';
                break;
            default:
                statusBadgeClass = 'bg-slate-100 text-slate-800 border-slate-200';
                statusLabel = req.status;
        }

        let projectBadgesHtml = '';
        if (req.projects && req.projects.length > 0) {
            req.projects.forEach(proj => {
                projectBadgesHtml += `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 rounded text-[11px] font-bold text-emerald-700 transition-colors hover:bg-emerald-100 shadow-sm">
                        <i class="fas fa-building text-[10px] text-emerald-500"></i>
                        ${proj.nama_proyek}
                    </span>
                `;
            });
        }

        let formattedDate = req.tanggal_permintaan;
        if (formattedDate) {
            const parts = formattedDate.split('-');
            if (parts.length === 3) {
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                formattedDate = `${parseInt(parts[2])} ${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
            }
        }

        html += `
            <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 hover:border-slate-300 transition-all duration-300 hover:shadow-md overflow-hidden flex flex-col mb-4">
                <div class="absolute left-0 top-0 bottom-0 w-1 ${borderLeftColor}"></div>
                
                <div class="p-5 pl-6">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <!-- Icon -->
                            <div class="w-10 h-10 rounded-xl ${iconBg} flex items-center justify-center shrink-0 border border-slate-100 shadow-sm">
                                <i class="fas ${iconName} text-lg"></i>
                            </div>
                            
                            <!-- Info -->
                            <div class="space-y-1.5 w-full">
                                <div class="flex items-center justify-between sm:justify-start gap-3 flex-wrap">
                                    <h4 class="font-bold text-slate-800 text-base group-hover:text-blue-600 transition-colors cursor-pointer btn-detail-ajax" data-id="${req.id}">${req.nomor_permintaan}</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider ${statusBadgeClass}">
                                        ${statusLabel}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 font-medium">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-slate-400"></i> ${formattedDate}</span>
                                    <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-user text-slate-400"></i> ${req.pemohon_nama || 'Pemohon'}</span>
                                    <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-box text-slate-400"></i> ${req.item_count} Item</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Content (Action) -->
                        <div class="shrink-0 flex items-center justify-end mt-2 sm:mt-0 gap-2">
                            <button type="button" data-id="${req.id}" class="btn-detail-ajax w-8 h-8 flex items-center justify-center text-emerald-600 hover:text-white bg-emerald-50 hover:bg-emerald-500 border border-emerald-200 rounded-lg transition-all shadow-sm focus:outline-none" title="Ubah/Lihat Detail">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100 my-4"></div>

                    <div class="flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
                        <div class="flex flex-wrap gap-2 flex-1">
                            ${projectBadgesHtml}
                        </div>
                        ${req.keterangan ? `
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 max-w-full sm:max-w-sm truncate" title="${req.keterangan}">
                                <i class="fas fa-comment-dots text-slate-400 shrink-0"></i>
                                <span class="truncate italic">${req.keterangan}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

export function renderDetailModal(req, userRole) {
    const titleText = document.getElementById('detail-nomor-tanggal');
    const badgeContainer = document.getElementById('detail-status-badge-container');
    const modalBody = document.getElementById('detail-modal-body');
    const timelineContainer = document.getElementById('detail-modal-timeline');
    const actionsContainer = document.getElementById('detail-modal-actions');
    const contractorNameEl = document.getElementById('detail-contractor-name');
    const requestDateEl = document.getElementById('detail-request-date');
    const projectAddressesEl = document.getElementById('detail-project-addresses');
    const changeStatusContainer = document.getElementById('detail-change-status-container');
    const statusDescEl = document.getElementById('detail-status-description');

    if (!titleText || !modalBody) return;

    let formattedDate = req.tanggal_permintaan;
    if (formattedDate) {
        const parts = formattedDate.split('-');
        if (parts.length === 3) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            formattedDate = `${parseInt(parts[2])} ${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
        }
    }

    titleText.textContent = `${req.nomor_permintaan} | ${formattedDate}`;

    let badgeClass = '';
    let statusLabel = '';
    switch (req.status) {
        case 'pending':
            badgeClass = 'bg-amber-500/20 border-amber-400/30 text-amber-500';
            statusLabel = 'Menunggu';
            break;
        case 'disetujui':
            badgeClass = 'bg-indigo-500/20 border-indigo-400/30 text-indigo-400';
            statusLabel = 'Diterima';
            break;
        case 'diproses':
            badgeClass = 'bg-cyan-500/20 border-cyan-400/30 text-cyan-400';
            statusLabel = 'Diproses';
            break;
        case 'selesai':
            badgeClass = 'bg-emerald-500/20 border-emerald-400/30 text-emerald-400';
            statusLabel = 'Selesai';
            break;
        case 'ditolak':
            badgeClass = 'bg-rose-500/20 border-rose-400/30 text-rose-400';
            statusLabel = 'Ditolak';
            break;
        default:
            badgeClass = 'bg-slate-500/20 border-slate-400/30 text-slate-300';
            statusLabel = req.status;
    }
    if (badgeContainer) {
        badgeContainer.innerHTML = `<span class="px-3 py-1 ${badgeClass} border rounded-full text-xs font-bold shadow-sm inline-block tracking-wide">${statusLabel}</span>`;
    }

    // Populate Contractor Info
    if (contractorNameEl) {
        contractorNameEl.textContent = req.pemohon_nama || 'Pemohon';
    }
    if (requestDateEl) {
        requestDateEl.textContent = formattedDate || '-';
    }

    // Populate Project Addresses
    if (projectAddressesEl) {
        let addressesHtml = '';
        if (req.projects && req.projects.length > 0) {
            req.projects.forEach(proj => {
                const lokasi = proj.lokasi_proyek || '-';
                addressesHtml += `
                    <div class="space-y-1.5 pl-3 border-l-4 border-emerald-500 bg-emerald-50/50 py-2 rounded-r-lg">
                        <div class="font-bold text-emerald-700 text-xs"><i class="fas fa-building mr-1.5 text-emerald-500"></i>${proj.nama_proyek}</div>
                        <div class="text-[11px] text-slate-600 font-medium leading-relaxed flex items-start gap-1.5">
                            <i class="fas fa-map-marker-alt text-slate-400 mt-0.5 shrink-0"></i>
                            <span>${lokasi}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            addressesHtml = '<p class="text-xs text-slate-400 font-medium">Tidak ada proyek terkait.</p>';
        }
        projectAddressesEl.innerHTML = addressesHtml;
    }

    let bodyHtml = '';
    if (req.projects && req.projects.length > 0) {
        req.projects.forEach(proj => {
            const groupedItems = { 'Bahan': [], 'Alat': [] };
            proj.items.forEach(item => {
                const cat = item.kategori === 'Alat' ? 'Alat' : 'Bahan';
                groupedItems[cat].push(item);
            });

            let itemsHtml = '';
            
            ['Bahan', 'Alat'].forEach(cat => {
                if (groupedItems[cat].length > 0) {
                    const catIcon = cat === 'Bahan' ? 'fa-layer-group' : 'fa-wrench';
                    itemsHtml += `
                        <div class="mt-2.5 mb-2 flex items-center gap-2 px-1">
                            <i class="fas ${catIcon} text-slate-600 text-xs"></i>
                            <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider">${cat}</h4>
                        </div>
                    `;
                    groupedItems[cat].forEach(item => {
                        const isKurang = parseFloat(item.stok_aktual) < parseFloat(item.jumlah);
                        const warningHtml = isKurang ? `<span class="px-2 py-0.5 text-[9px] font-bold bg-rose-100 text-rose-800 border border-rose-200 rounded uppercase ml-2">Stok Kurang</span>` : '';
                        const stokColor = isKurang ? 'text-rose-600' : 'text-emerald-600';
                        
                        itemsHtml += `
                            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 px-1 hover:bg-slate-50/50 transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-slate-600">${item.nama_barang}</span>
                                    <span class="text-[10px] font-medium text-slate-500 mt-0.5">Stok Gudang: <span class="font-bold ${stokColor}">${parseFloat(item.stok_aktual || 0)} ${item.satuan}</span> ${warningHtml}</span>
                                </div>
                                <span class="text-xs font-bold text-slate-700">${parseFloat(item.jumlah)} <span class="text-[10px] text-slate-400 font-medium ml-1">${item.satuan}</span></span>
                            </div>
                        `;
                    });
                }
            });

            bodyHtml += `
                <div class="border border-emerald-200/60 rounded-xl overflow-hidden bg-white shadow-sm mb-4 last:mb-0 transition-all hover:shadow-md">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2.5 border-b border-emerald-700/50 flex items-center gap-2">
                        <i class="fas fa-building text-emerald-200 text-xs"></i>
                        <h3 class="text-white font-bold text-sm tracking-wide">${proj.nama_proyek}</h3>
                    </div>
                    <div class="p-3 px-4">
                        ${itemsHtml}
                    </div>
                </div>
            `;
        });
    }

    if (req.keterangan) {
        bodyHtml += `
            <div class="mt-4 p-4 bg-white rounded-lg border border-slate-200 shadow-sm">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2"><i class="fas fa-comment-dots mr-1"></i> Catatan Pemohon</h4>
                <p class="text-sm text-slate-700 italic font-medium">${req.keterangan}</p>
            </div>
        `;
    }

    modalBody.innerHTML = bodyHtml || '<p class="text-sm text-slate-500 text-center py-4">Tidak ada detail item.</p>';

    if (timelineContainer) {
        const formatTimelineDate = (dateStr) => {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const pad = (n) => n.toString().padStart(2, '0');
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} | ${pad(d.getHours())}.${pad(d.getMinutes())}`;
        };

        const logsMap = {};
        if (req.status_logs) {
            req.status_logs.forEach(log => {
                logsMap[log.status] = {
                    date: formatTimelineDate(log.created_at),
                    user: log.nama_pengubah || '',
                    note: log.keterangan || ''
                };
            });
        }

        const isAuthorized = userRole && ['gudang', 'admin', 'purchasing'].includes(userRole.toLowerCase());

        let timelineSteps = [];

        // 1. Dibuat
        timelineSteps.push({
            id: 'created',
            label: 'Permintaan Dibuat',
            date: logsMap['pending'] ? logsMap['pending'].date : formatTimelineDate(req.created_at),
            user: logsMap['pending'] ? logsMap['pending'].user : (req.pemohon_nama || 'Pemohon'),
            active: true,
            color: 'text-slate-800',
            dot: 'bg-slate-800',
            actions: ''
        });

        // If rejected
        if (req.status === 'ditolak') {
            timelineSteps.push({
                id: 'rejected',
                label: 'Permintaan Ditolak',
                date: logsMap['ditolak'] ? logsMap['ditolak'].date : formatTimelineDate(req.updated_at),
                user: logsMap['ditolak'] ? logsMap['ditolak'].user : '',
                note: logsMap['ditolak'] ? logsMap['ditolak'].note : '',
                active: true,
                color: 'text-rose-600',
                dot: 'bg-rose-500',
                actions: ''
            });
        } else {
            // 2. Diterima Gudang
            const isApproved = req.status === 'disetujui' || req.status === 'diproses' || req.status === 'selesai';
            let actionsDiterima = '';
            if (req.status === 'pending' && isAuthorized) {
                actionsDiterima = `
                    <div class="mt-2.5 flex items-center gap-2">
                        <button type="button" data-id="${req.id}" data-action="disetujui" class="btn-change-status px-3 py-1.5 text-[10px] font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded shadow-sm focus:outline-none transition-colors">Terima</button>
                        <button type="button" data-id="${req.id}" data-action="ditolak" class="btn-change-status px-3 py-1.5 text-[10px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded shadow-sm focus:outline-none transition-colors">Tolak</button>
                    </div>
                `;
            }
            timelineSteps.push({
                id: 'approved',
                label: 'Permintaan diterima oleh gudang',
                date: isApproved ? (logsMap['disetujui'] ? logsMap['disetujui'].date : formatTimelineDate(req.updated_at)) : '',
                user: isApproved && logsMap['disetujui'] ? logsMap['disetujui'].user : '',
                active: isApproved,
                color: isApproved ? 'text-slate-800' : 'text-slate-400',
                dot: isApproved ? 'bg-slate-800' : 'bg-slate-200',
                actions: actionsDiterima
            });

            // 3. Diproses
            const isProcessed = req.status === 'diproses' || req.status === 'selesai';
            let actionsDiproses = '';
            if (req.status === 'disetujui' && isAuthorized) {
                actionsDiproses = `
                    <div class="mt-2.5">
                        <button type="button" data-id="${req.id}" data-action="diproses" class="btn-change-status px-3 py-1.5 text-[10px] font-bold text-white bg-cyan-500 hover:bg-cyan-600 rounded shadow-sm focus:outline-none transition-colors w-full sm:w-auto">Proses Sekarang</button>
                    </div>
                `;
            }
            timelineSteps.push({
                id: 'processed',
                label: 'Permintaan sedang diproses oleh gudang',
                date: isProcessed ? (logsMap['diproses'] ? logsMap['diproses'].date : formatTimelineDate(req.updated_at)) : '',
                user: isProcessed && logsMap['diproses'] ? logsMap['diproses'].user : '',
                active: isProcessed,
                color: isProcessed ? 'text-slate-800' : 'text-slate-400',
                dot: isProcessed ? 'bg-slate-800' : 'bg-slate-200',
                actions: actionsDiproses
            });

            // 4. Selesai
            const isCompleted = req.status === 'selesai';
            let actionsSelesai = '';
            if (req.status === 'diproses' && isAuthorized) {
                actionsSelesai = `
                    <div class="mt-2.5">
                        <button type="button" data-id="${req.id}" data-action="selesai" class="btn-change-status px-3 py-1.5 text-[10px] font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded shadow-sm focus:outline-none transition-colors w-full sm:w-auto">Tandai Diterima di Lapangan</button>
                    </div>
                `;
            }
            timelineSteps.push({
                id: 'completed',
                label: 'Permintaan selesai (Telah diterima di lapangan)',
                date: isCompleted ? (logsMap['selesai'] ? logsMap['selesai'].date : formatTimelineDate(req.updated_at)) : '',
                user: isCompleted && logsMap['selesai'] ? logsMap['selesai'].user : '',
                active: isCompleted,
                color: isCompleted ? 'text-emerald-600' : 'text-slate-400',
                dot: isCompleted ? 'bg-emerald-500' : 'bg-slate-200',
                actions: actionsSelesai
            });
        }

        let timelineHtml = '';
        timelineSteps.forEach((step, idx) => {
            const isLast = idx === timelineSteps.length - 1;
            const titleColor = step.color || 'text-slate-400';
            const dotColor = step.dot || 'bg-slate-200';
            const titleFont = step.active ? 'font-bold' : 'font-medium';
            
            timelineHtml += `
                <div class="relative pl-6 pb-6 last:pb-0">
                    <!-- Line -->
                    ${!isLast ? `<div class="absolute left-[3px] top-2 bottom-[-14px] w-0.5 bg-slate-200"></div>` : ''}
                    <!-- Dot -->
                    <div class="absolute -left-[4.5px] top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white shadow-sm z-10 ${dotColor}"></div>
                    <!-- Content -->
                    <div class="pt-0.5">
                        <h4 class="text-xs ${titleFont} ${titleColor}">${step.label}</h4>
                        ${step.date ? `<p class="text-[10px] text-slate-500 font-semibold mt-1">${step.date}</p>` : ''}
                        ${step.user ? `<p class="text-[10px] text-slate-400 mt-0.5"><i class="fas fa-user mr-1"></i>${step.user}</p>` : ''}
                        ${step.note && step.id === 'rejected' ? `<p class="text-[10px] text-rose-500 italic mt-0.5">${step.note}</p>` : ''}
                        ${step.actions || ''}
                    </div>
                </div>
            `;
        });
        timelineContainer.innerHTML = timelineHtml;

        // Auto-Procure Button Logic
        if (isAuthorized && (req.status === 'pending' || req.status === 'disetujui')) {
            let hasKurang = false;
            req.projects.forEach(proj => {
                proj.items.forEach(item => {
                    if (parseFloat(item.stok_aktual || 0) < parseFloat(item.jumlah)) hasKurang = true;
                });
            });

            if (hasKurang) {
                // Remove existing auto procure container if any
                const existingBtn = document.getElementById('auto-procure-container');
                if (existingBtn) existingBtn.remove();
                
                const btnHtml = `
                    <div id="auto-procure-container" class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                        <button type="button" data-id="${req.id}" class="btn-auto-procure px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Ajukan Pengadaan Otomatis</span>
                        </button>
                    </div>
                `;
                timelineContainer.parentElement.insertAdjacentHTML('beforeend', btnHtml);
            } else {
                const existingBtn = document.getElementById('auto-procure-container');
                if (existingBtn) existingBtn.remove();
            }
        } else {
            const existingBtn = document.getElementById('auto-procure-container');
            if (existingBtn) existingBtn.remove();
        }
    }
}

export function renderFormProjectBlocks(projectRows, projects) {
    const container = document.getElementById('project-blocks-container');
    if (!container) return;

    let html = '';
    projectRows.forEach((row, index) => {
        let optionsHtml = '<option value="">--Pilih Proyek--</option>';
        projects.forEach(p => {
            const selected = p.id_project == row.selectedProjectId ? 'selected' : '';
            optionsHtml += `<option value="${p.id_project}" ${selected}>${p.nama_proyek}</option>`;
        });

        let bahanOptions = '';
        if (row.rapItems && row.rapItems.length > 0) {
            let optBahan = '';
            let optAlat = '';
            row.rapItems.forEach(item => {
                const label = `${item.nama} (${item.satuan}) ${item.merk !== '-' && item.merk ? ' • ' + item.merk : ''}`;
                const optionStr = `<option value="${item.id_rap_detail_item}" data-stok="${item.stok_aktual || 0}">${label}</option>`;
                if ((item.kategori || '').toLowerCase() === 'bahan') {
                    optBahan += optionStr;
                } else {
                    optAlat += optionStr;
                }
            });
            if (optBahan) bahanOptions += `<optgroup label="Bahan">${optBahan}</optgroup>`;
            if (optAlat) bahanOptions += `<optgroup label="Alat">${optAlat}</optgroup>`;
        } else {
            bahanOptions = `<option value="" disabled>Tidak ada data di RAP</option>`;
        }

        let itemsRowsHtml = '';
        if (!row.items || row.items.length === 0) {
            itemsRowsHtml = `
                <tr class="empty-items-row">
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400 italic text-xs">
                        Belum ada item terpilih. Pilih item dari RAP atau tambah item kustom di atas.
                    </td>
                </tr>
            `;
        } else {
            row.items.forEach((item, itemIdx) => {
                const categoryBadge = (item.kategori || '').toLowerCase() === 'bahan'
                    ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase">Bahan</span>'
                    : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-600 border border-purple-100 uppercase">Alat</span>';

                itemsRowsHtml += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-center text-xs font-semibold text-slate-500">${itemIdx + 1}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-800 text-sm">${item.nama_barang}</span>
                            </div>
                            <p class="text-[9px] text-slate-400 font-semibold mt-0.5">Merk: ${item.merk || '-'} • Spek: ${item.spesifikasi || '-'}</p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-1 bg-emerald-50 inline-block px-1.5 py-0.5 rounded border border-emerald-100">Stok Gudang: ${item.stok_aktual || 0} ${item.satuan}</p>
                        </td>
                        <td class="px-4 py-3 text-center w-24">
                            ${categoryBadge}
                        </td>
                        <td class="px-4 py-3 w-28 text-center">
                            <input type="number" step="0.01" min="0.01" value="${item.jumlah}" data-block-id="${row.id}" data-idx="${itemIdx}" class="input-qty w-20 px-2 py-1 text-center font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded focus:outline-none focus:border-blue-500">
                        </td>
                        <td class="px-4 py-3 w-28 text-center flex flex-col items-center justify-center">
                            <span class="text-sm font-bold ${(item.sisa_volume < 0) ? 'text-red-600' : 'text-slate-500'}">${item.sisa_volume ?? '0'}</span>
                            <span class="text-[10px] font-semibold text-slate-400 lowercase">${item.satuan}</span>
                        </td>
                        <td class="px-4 py-3 w-20 text-center">
                            <input type="text" value="${item.satuan}" data-block-id="${row.id}" data-idx="${itemIdx}" readonly class="input-satuan w-14 px-1 py-1 text-center text-xs font-bold text-slate-600 border bg-slate-100 border-slate-200 cursor-not-allowed rounded">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" placeholder="Catatan item..." value="${item.keterangan || ''}" data-block-id="${row.id}" data-idx="${itemIdx}" class="input-item-keterangan w-full px-2 py-1 text-xs text-slate-700 bg-white border border-slate-300 rounded focus:outline-none focus:border-blue-500">
                        </td>
                        <td class="px-4 py-3 w-12 text-center">
                            <button type="button" data-block-id="${row.id}" data-idx="${itemIdx}" class="btn-remove-item w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors focus:outline-none">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        html += `
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4 relative" data-row-id="${row.id}">
                
                <!-- Title Badge and Remove Block Button -->
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#1e293b] text-white">
                        Proyek ${index + 1}
                    </span>
                    ${projectRows.length > 1 ? `
                        <button type="button" data-block-id="${row.id}" class="btn-remove-project-block text-slate-400 hover:text-red-600 w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors focus:outline-none" title="Hapus Blok Proyek">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    ` : ''}
                </div>

                <!-- Select Project Dropdown -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Proyek</label>
                    <div class="relative w-full">
                        <select data-block-id="${row.id}" class="select-project w-full pl-3 pr-10 h-10 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 appearance-none cursor-pointer">
                            ${optionsHtml}
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Show items fields only if a project is selected -->
                ${row.selectedProjectId ? `
                    <div class="space-y-4 pt-2">
                        
                        <!-- Dropdown Select & Add Button -->
                        <div class="flex flex-col sm:flex-row gap-3 items-end bg-slate-50 p-4 border border-slate-200 rounded-xl">
                            <div class="flex-1 space-y-1 w-full">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Item dari RAP</label>
                                <div class="relative w-full">
                                    <select data-block-id="${row.id}" class="select-rap-item w-full pl-3 pr-10 h-10 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 appearance-none cursor-pointer">
                                        <option value="">-- Pilih Item --</option>
                                        ${bahanOptions}
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-block-id="${row.id}" class="btn-add-rap-item px-4 h-10 bg-[#1e293b] hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all focus:outline-none flex items-center justify-center gap-1.5 shrink-0 shadow-sm w-full sm:w-auto">
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Tambah Item</span>
                            </button>
                        </div>

                        <!-- Combined Items Table List -->
                        <div class="w-full overflow-hidden border border-slate-200 rounded-xl bg-white shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse text-left">
                                    <thead>
                                        <tr class="bg-[#1e293b] text-white border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider">
                                            <th class="px-4 py-3 text-center w-12">No</th>
                                            <th class="px-4 py-3">Nama Item / Spesifikasi</th>
                                            <th class="px-4 py-3 text-center w-24">Kategori</th>
                                            <th class="px-4 py-3 text-center w-28">Jumlah Diminta</th>
                                            <th class="px-4 py-3 text-center w-28">Jumlah Tersisa</th>
                                            <th class="px-4 py-3 text-center w-20">Satuan</th>
                                            <th class="px-4 py-3">Catatan Detail</th>
                                            <th class="px-4 py-3 text-center w-12">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${itemsRowsHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                ` : `
                    <div class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-400">
                        <i class="fas fa-info-circle text-lg mb-1"></i>
                        <p class="text-xs font-semibold">Silakan pilih proyek terlebih dahulu untuk menentukan item.</p>
                    </div>
                `}

            </div>
        `;
    });
    container.innerHTML = html;
}
