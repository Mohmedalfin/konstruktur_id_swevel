export function renderStats(stats) {
    if (!stats) return;
    document.getElementById('stat-total').textContent = stats.total ?? 0;
    document.getElementById('stat-pending').textContent = stats.pending ?? 0;
    document.getElementById('stat-proses').textContent = (stats.disetujui || 0) + (stats.diproses || 0);
    document.getElementById('stat-kirim').textContent = stats.selesai ?? 0;
}

export function renderRequestsList(requests) {
    const container = document.getElementById('history-container');
    if (!container) return;

    if (!Array.isArray(requests) || requests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 bg-white rounded-xl border border-slate-100 shadow-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-inbox text-2xl text-slate-300"></i>
                </div>
                <p class="text-sm font-semibold text-slate-600">Belum ada permintaan</p>
                <p class="text-xs mt-1 text-slate-400">Tidak ada data yang sesuai dengan filter saat ini.</p>
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
                statusBadgeClass = 'bg-amber-50 text-amber-600 border-transparent';
                statusLabel = 'Menunggu';
                borderLeftColor = 'bg-amber-500';
                iconBg = 'bg-amber-50 text-amber-500';
                iconName = 'fa-clock';
                break;
            case 'disetujui':
                statusBadgeClass = 'bg-indigo-50 text-indigo-600 border-transparent';
                statusLabel = 'Diterima';
                borderLeftColor = 'bg-indigo-500';
                iconBg = 'bg-indigo-50 text-indigo-500';
                iconName = 'fa-check-double';
                break;
            case 'diproses':
                statusBadgeClass = 'bg-blue-50 text-blue-600 border-transparent';
                statusLabel = 'Diproses';
                borderLeftColor = 'bg-blue-500';
                iconBg = 'bg-blue-50 text-blue-500';
                iconName = 'fa-cog fa-spin-hover';
                break;
            case 'selesai':
                statusBadgeClass = 'bg-emerald-50 text-emerald-600 border-transparent';
                statusLabel = 'Terkirim';
                borderLeftColor = 'bg-emerald-500';
                iconBg = 'bg-emerald-50 text-emerald-500';
                iconName = 'fa-check-circle';
                break;
            case 'ditolak':
                statusBadgeClass = 'bg-rose-50 text-rose-600 border-transparent';
                statusLabel = 'Ditolak';
                borderLeftColor = 'bg-rose-500';
                iconBg = 'bg-rose-50 text-rose-500';
                iconName = 'fa-times-circle';
                break;
            default:
                statusBadgeClass = 'bg-slate-50 text-slate-600 border-transparent';
                statusLabel = req.status;
                borderLeftColor = 'bg-slate-500';
                iconBg = 'bg-slate-50 text-slate-500';
                iconName = 'fa-file-alt';
        }

        let projectBadgesHtml = '';
        if (req.projects && req.projects.length > 0) {
            req.projects.forEach(proj => {
                projectBadgesHtml += `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 rounded-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-100">
                        <i class="fas fa-map-marker-alt text-emerald-500"></i>
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
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-4 transition-all hover:shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    
                    <!-- Left Side: Icon & Info -->
                    <div class="flex items-start sm:items-center gap-4 flex-1">
                        <div class="w-12 h-12 rounded-full ${iconBg} flex items-center justify-center shrink-0">
                            <i class="fas ${iconName} text-xl"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center flex-wrap gap-3">
                                <span class="font-bold text-blue-700 text-base sm:text-lg cursor-pointer hover:text-blue-800 transition-colors btn-detail-ajax" data-id="${req.id}">${req.nomor_permintaan}</span>
                                <span class="hidden sm:inline-block w-px h-4 bg-slate-200"></span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider ${statusBadgeClass}">
                                    <span class="w-1.5 h-1.5 rounded-full ${borderLeftColor}"></span>
                                    ${statusLabel}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 font-medium">
                                <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-slate-400"></i> ${formattedDate}</span>
                                <span class="hidden sm:inline-block w-px h-3 bg-slate-200"></span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-user text-slate-400"></i> ${req.pemohon_nama || 'Pemohon'}</span>
                                <span class="hidden sm:inline-block w-px h-3 bg-slate-200"></span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-box text-slate-400"></i> ${req.item_count} Item</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" data-id="${req.id}" class="btn-detail-ajax w-10 h-10 flex items-center justify-center text-emerald-600 hover:text-white bg-emerald-50 hover:bg-emerald-500 border border-emerald-200 rounded-lg transition-all shadow-sm focus:outline-none" title="Ubah/Lihat Detail">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>

                </div>

                <!-- Bottom Side: Project -->
                ${projectBadgesHtml ? `
                    <div class="h-px bg-slate-100 my-4"></div>
                    <div class="flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
                        <div class="flex items-center gap-2 flex-wrap flex-1">
                            ${projectBadgesHtml}
                        </div>
                        ${req.keterangan ? `
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 max-w-full sm:max-w-sm truncate" title="${req.keterangan}">
                                <i class="fas fa-comment-dots text-slate-400 shrink-0"></i>
                                <span class="truncate italic">${req.keterangan}</span>
                            </div>
                        ` : ''}
                    </div>
                ` : ''}
            </div>
        `;
    });
    container.innerHTML = html;
}

export function renderDetailModal(req, userRole) {
    const elNomor = document.getElementById('detail-nomor-permintaan');
    const elTanggal = document.getElementById('detail-tanggal-permintaan');
    const elInfoGrid = document.getElementById('detail-info-grid');
    const elModalBody = document.getElementById('detail-modal-body');
    const elActions = document.getElementById('detail-modal-actions');
    const stepperProgress = document.getElementById('stepper-permintaan-progress');
    const stepperSteps = document.getElementById('stepper-permintaan-steps');
    const rejectedAlert = document.getElementById('detail-permintaan-rejected-alert');
    const rejectedNote = document.getElementById('detail-permintaan-rejected-note');

    if (!elNomor || !elModalBody) return;

    // 1. Format Date
    let formattedDate = req.tanggal_permintaan;
    if (formattedDate) {
        const parts = formattedDate.split('-');
        if (parts.length === 3) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            formattedDate = `${parseInt(parts[2])} ${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
        }
    }

    // 2. Set Header Info
    elNomor.textContent = req.nomor_permintaan || '-';
    elTanggal.textContent = `Tanggal: ${formattedDate || '-'}`;

    // 3. Info Grid (Pemohon, Alamat, Catatan)
    let addressesHtml = '';
    if (req.projects && req.projects.length > 0) {
        req.projects.forEach(proj => {
            const lokasi = proj.lokasi_proyek || '-';
            addressesHtml += `
                <div class="flex items-start gap-1.5 text-sm">
                    <i class="fas fa-map-marker-alt text-slate-400 mt-1 shrink-0 text-xs"></i>
                    <div>
                        <span class="font-bold text-slate-700 block">${proj.nama_proyek}</span>
                        <span class="text-xs text-slate-500">${lokasi}</span>
                    </div>
                </div>
            `;
        });
    } else {
        addressesHtml = '<p class="text-xs text-slate-400 font-medium">Tidak ada proyek terkait.</p>';
    }

    const catatanHtml = req.keterangan 
        ? `<p class="text-sm text-slate-700 italic">"${req.keterangan}"</p>` 
        : `<p class="text-sm text-slate-400 italic">Tidak ada catatan umum.</p>`;

    const justifikasiHtml = (req.is_over_limit === '1' || req.is_over_limit === 1)
        ? `<div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
               <h5 class="text-[10px] font-bold text-red-600 uppercase tracking-wider mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Justifikasi Over-Limit</h5>
               <p class="text-xs text-red-800 italic">"${req.justifikasi_over_limit || '-'}"</p>
           </div>`
        : '';

    if (elInfoGrid) {
        elInfoGrid.innerHTML = `
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex flex-col gap-3">
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Informasi Pemohon</h4>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">${req.pemohon_nama || 'Pemohon'}</p>
                            <p class="text-xs text-slate-500">Kontraktor</p>
                        </div>
                    </div>
                </div>
                <div class="h-px bg-slate-200 my-1"></div>
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tujuan Pengiriman / Proyek</h4>
                    <div class="space-y-2">
                        ${addressesHtml}
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex flex-col">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Keterangan / Catatan</h4>
                <div class="flex-1">
                    ${catatanHtml}
                    ${justifikasiHtml}
                </div>
            </div>
        `;
    }

    // 4. Stepper Timeline
    if (stepperProgress && stepperSteps) {
        const formatTimelineDate = (dateStr) => {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const pad = (n) => n.toString().padStart(2, '0');
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} ${pad(d.getHours())}.${pad(d.getMinutes())}`;
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

        const currentStatus = req.status;
        
        if (currentStatus === 'ditolak') {
            if (rejectedAlert) {
                rejectedAlert.classList.remove('hidden');
                if (rejectedNote) {
                    rejectedNote.textContent = logsMap['ditolak']?.note || 'Permintaan ini telah ditolak dan tidak akan diproses lebih lanjut.';
                }
            }
            stepperProgress.style.width = '0%';
            stepperProgress.style.height = '0%';
            
            stepperSteps.innerHTML = `
                <div class="flex sm:flex-col items-center gap-3 sm:gap-2 relative group w-full">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors shadow-sm bg-red-100 border-2 border-red-500 text-red-600 z-10">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="sm:text-center flex flex-col items-start sm:items-center w-full">
                        <h5 class="text-sm font-bold text-red-700">Ditolak</h5>
                        <p class="text-[10px] text-slate-500 font-semibold mt-1">${logsMap['ditolak']?.date || ''}</p>
                        <p class="text-[9px] text-slate-400 truncate w-full max-w-[100px] sm:max-w-none"><i class="fas fa-user mr-1"></i>${logsMap['ditolak']?.user || ''}</p>
                    </div>
                </div>
            `;
        } else {
            if (rejectedAlert) rejectedAlert.classList.add('hidden');
            
            const statusLevels = { 'pending': 1, 'disetujui': 2, 'diproses': 3, 'selesai': 4 };
            const currentStep = statusLevels[currentStatus] || 1;
            
            let percentage = 0;
            if (currentStep === 2) percentage = 33;
            if (currentStep === 3) percentage = 66;
            if (currentStep === 4) percentage = 100;
            
            const isMobile = window.innerWidth < 640;
            if (isMobile) {
                stepperProgress.style.height = `${percentage}%`;
                stepperProgress.style.width = '100%';
                stepperProgress.classList.remove('scale-y-0');
                stepperProgress.classList.add('scale-y-100');
            } else {
                stepperProgress.style.width = `${percentage}%`;
                stepperProgress.style.height = '100%';
                stepperProgress.classList.remove('scale-x-0');
                stepperProgress.classList.add('scale-x-100');
            }

            const stepDefinitions = [
                { id: 1, key: 'pending', title: 'Diajukan', desc: 'Menunggu Persetujuan', icon: 'fa-file-alt' },
                { id: 2, key: 'disetujui', title: 'Diterima', desc: 'Disetujui Gudang', icon: 'fa-check' },
                { id: 3, key: 'diproses', title: 'Diproses', desc: 'Sedang Disiapkan', icon: 'fa-cog' },
                { id: 4, key: 'selesai', title: 'Selesai', desc: 'Terkirim', icon: 'fa-box' }
            ];

            let stepsHtml = '';
            stepDefinitions.forEach(step => {
                let iconClass = '';
                let titleClass = '';
                
                if (step.id < currentStep) {
                    iconClass = 'bg-emerald-500 border-emerald-500 text-white';
                    titleClass = 'text-emerald-700';
                } else if (step.id === currentStep) {
                    iconClass = 'bg-white border-emerald-500 text-emerald-600 ring-4 ring-emerald-50';
                    titleClass = 'text-emerald-700';
                } else {
                    iconClass = 'bg-white border-slate-300 text-slate-400';
                    titleClass = 'text-slate-500';
                }

                const logData = logsMap[step.key] || {};
                const dateHtml = logData.date ? `<p class="text-[10px] text-slate-500 font-semibold mt-1">${logData.date}</p>` : `<p class="text-[10px] text-slate-400 mt-1">${step.desc}</p>`;
                const userHtml = logData.user ? `<p class="text-[9px] text-slate-400 truncate w-full max-w-[100px] sm:max-w-none mt-0.5"><i class="fas fa-user mr-1"></i>${logData.user}</p>` : '';

                stepsHtml += `
                    <div class="flex sm:flex-col items-center gap-3 sm:gap-2 relative group flex-1">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-colors shadow-sm z-10 ${iconClass}">
                            <i class="fas ${step.icon}"></i>
                        </div>
                        <div class="sm:text-center flex flex-col items-start sm:items-center w-full">
                            <h5 class="text-sm font-bold ${titleClass}">${step.title}</h5>
                            ${dateHtml}
                            ${userHtml}
                        </div>
                    </div>
                `;
            });
            stepperSteps.innerHTML = stepsHtml;
        }
    }

    // 5. Items List per Project
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
                        <div class="mt-3 mb-2 flex items-center gap-2 px-2">
                            <i class="fas ${catIcon} text-slate-600 text-xs"></i>
                            <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider">${cat}</h4>
                        </div>
                    `;
                    groupedItems[cat].forEach(item => {
                        const hasKemasan = item.satuan_kemasan && String(item.satuan_kemasan).trim() !== '';
                        const kf = parseFloat(item.konversi_faktor) || 1;
                        const isDifferentName = hasKemasan && String(item.satuan_kemasan).toLowerCase() !== String(item.satuan).toLowerCase();
                        
                        const stokBase = parseFloat(item.stok_aktual || 0);
                        const isKurang = stokBase < parseFloat(item.jumlah);
                        
                        const warningHtml = isKurang ? `<span class="px-2 py-0.5 text-[9px] font-bold bg-rose-100 text-rose-800 border border-rose-200 rounded uppercase ml-2 shadow-sm">Stok Kurang</span>` : '';
                        const stokColor = isKurang ? 'text-rose-600' : 'text-emerald-600';
                        
                        let displayStokValue = stokBase;
                        let displayStokUnit = item.satuan;
                        if (hasKemasan && kf > 1) {
                            displayStokValue = stokBase / kf;
                            displayStokValue = Number.isInteger(displayStokValue) ? displayStokValue : parseFloat(displayStokValue.toFixed(2));
                            displayStokUnit = item.satuan_kemasan;
                        }
                        
                        const overLimitBadge = (item.is_over_limit === '1' || item.is_over_limit === 1)
                            ? `<span class="px-2 py-0.5 text-[9px] font-bold bg-red-100 text-red-700 border border-red-200 rounded uppercase ml-2 shadow-sm"><i class="fas fa-exclamation-triangle mr-1"></i> Over: ${parseFloat(item.jumlah_over_limit)} ${item.satuan}</span>`
                            : '';

                        let jumlahDisplayHtml = `<span class="text-sm font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-lg shadow-sm whitespace-nowrap">${parseFloat(item.jumlah)} <span class="text-[10px] font-semibold text-emerald-500 ml-0.5">${item.satuan}</span></span>`;
                        
                        if (hasKemasan && (kf > 1 || isDifferentName)) {
                            const jumlahKemasan = parseFloat(item.jumlah) / kf;
                            // avoid long decimals
                            const qtyKemasanFmt = Number.isInteger(jumlahKemasan) ? jumlahKemasan : jumlahKemasan.toFixed(2).replace(/\.?0+$/, '');
                            
                            jumlahDisplayHtml = `
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="text-sm font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-lg shadow-sm whitespace-nowrap">${qtyKemasanFmt} <span class="text-[10px] font-semibold text-emerald-500 ml-0.5">${item.satuan_kemasan}</span></span>
                                    <span class="text-[9px] text-slate-500 font-medium bg-slate-50 px-2 py-0.5 rounded border border-slate-200 shadow-sm whitespace-nowrap"><i class="fas fa-exchange-alt mr-1 text-slate-400"></i> ${parseFloat(item.jumlah)} ${item.satuan}</span>
                                </div>
                            `;
                        }

                        itemsHtml += `
                            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0 px-2 hover:bg-slate-50 transition-colors rounded-lg">
                                <div class="flex flex-col">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <span class="text-sm font-bold text-slate-800">${item.nama_barang}</span>
                                        ${overLimitBadge}
                                    </div>
                                    <span class="text-[10px] font-medium text-slate-500 mt-0.5 flex flex-wrap items-center gap-1">Stok Gudang: <span class="font-bold ${stokColor}">${displayStokValue} ${displayStokUnit}</span> ${warningHtml}</span>
                                </div>
                                <div class="text-right flex flex-col items-end justify-center">
                                    ${jumlahDisplayHtml}
                                </div>
                            </div>
                        `;
                    });
                }
            });

            bodyHtml += `
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm mb-4 last:mb-0">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center gap-2">
                        <i class="fas fa-building text-slate-500 text-sm"></i>
                        <h3 class="text-slate-800 font-bold text-sm tracking-wide">${proj.nama_proyek}</h3>
                    </div>
                    <div class="p-2">
                        ${itemsHtml}
                    </div>
                </div>
            `;
        });
    }

    elModalBody.innerHTML = bodyHtml || '<p class="text-sm text-slate-500 text-center py-4">Tidak ada detail item.</p>';

    // 6. Action Buttons in Footer
    if (elActions) {
        let actionsHtml = '';
        const isAuthorized = userRole && ['gudang', 'admin', 'purchasing'].includes(userRole.toLowerCase());

        if (isAuthorized) {
            if (req.status === 'pending') {
                actionsHtml += `
                    <button type="button" data-id="${req.id}" data-action="ditolak" class="btn-change-status px-5 py-2 text-sm font-bold text-rose-600 bg-rose-50 border border-rose-200 hover:bg-rose-500 hover:text-white rounded-xl shadow-sm focus:outline-none transition-all flex items-center gap-2">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                    <button type="button" data-id="${req.id}" data-action="disetujui" class="btn-change-status px-5 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-600 rounded-xl shadow-sm shadow-emerald-600/30 focus:outline-none transition-all flex items-center gap-2">
                        <i class="fas fa-check"></i> Terima Permintaan
                    </button>
                `;
            } else if (req.status === 'disetujui') {
                actionsHtml += `
                    <button type="button" data-id="${req.id}" data-action="diproses" class="btn-change-status px-5 py-2 text-sm font-bold text-white bg-cyan-500 hover:bg-cyan-600 border border-cyan-500 rounded-xl shadow-sm shadow-cyan-500/30 focus:outline-none transition-all flex items-center gap-2">
                        <i class="fas fa-cog"></i> Proses Pengiriman
                    </button>
                `;
            } else if (req.status === 'diproses') {
                actionsHtml += `
                    <button type="button" data-id="${req.id}" data-action="selesai" class="btn-change-status px-5 py-2 text-sm font-bold text-white bg-emerald-500 hover:bg-emerald-600 border border-emerald-500 rounded-xl shadow-sm shadow-emerald-500/30 focus:outline-none transition-all flex items-center gap-2">
                        <i class="fas fa-clipboard-check"></i> Tandai Selesai (Diterima Lapangan)
                    </button>
                `;
            }

            // Auto-Procure Logic
            if (req.status === 'pending' || req.status === 'disetujui') {
                let hasKurang = false;
                req.projects.forEach(proj => {
                    proj.items.forEach(item => {
                        if (parseFloat(item.stok_aktual || 0) < parseFloat(item.jumlah)) hasKurang = true;
                    });
                });

                if (hasKurang) {
                    actionsHtml = `
                        <button type="button" data-id="${req.id}" class="btn-auto-procure px-5 py-2 text-sm font-bold text-indigo-600 bg-indigo-50 border border-indigo-200 hover:bg-indigo-600 hover:text-white rounded-xl shadow-sm focus:outline-none transition-all flex items-center gap-2 mr-auto order-first sm:order-none">
                            <i class="fas fa-shopping-cart"></i> Ajukan Pengadaan
                        </button>
                    ` + actionsHtml;
                }
            }
        }
        elActions.innerHTML = actionsHtml;
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

                const sisaVolumeVal = parseFloat(item.sisa_volume || 0);
                const isOverLimit = parseFloat(item.jumlah) > Math.max(0, sisaVolumeVal);
                const inputClass = isOverLimit 
                    ? 'border-red-500 focus:border-red-500 bg-red-50' 
                    : 'border-slate-300 focus:border-blue-500 bg-slate-50';
                const errorDisplay = isOverLimit ? '' : 'hidden';
                const excess = parseFloat(item.jumlah) - Math.max(0, sisaVolumeVal);
                const errorText = isOverLimit ? `Over: ${excess}` : '';

                itemsRowsHtml += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-center text-xs font-semibold text-slate-500">${itemIdx + 1}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-800 text-sm">${item.nama_barang}</span>
                            </div>
                            <p class="text-[9px] text-slate-400 font-semibold mt-0.5">Merk: ${item.merk || '-'} • Spek: ${item.spesifikasi || '-'}</p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-1 bg-emerald-50 inline-block px-1.5 py-0.5 rounded border border-emerald-100">
                                Stok Gudang: ${(item.satuan_kemasan && parseFloat(item.konversi_faktor || 1) > 1) 
                                    ? (Number.isInteger(parseFloat(item.stok_aktual || 0) / parseFloat(item.konversi_faktor)) ? (parseFloat(item.stok_aktual || 0) / parseFloat(item.konversi_faktor)) : (parseFloat(item.stok_aktual || 0) / parseFloat(item.konversi_faktor)).toFixed(2)) + ' ' + item.satuan_kemasan 
                                    : (item.stok_aktual || 0) + ' ' + item.satuan}
                            </p>
                        </td>
                        <td class="px-4 py-3 text-center w-24">
                            ${categoryBadge}
                        </td>
                        <td class="px-4 py-3 w-28 text-center">
                            <div class="flex flex-col items-center">
                                <input type="number" step="0.01" min="0.01" value="${item.jumlah}" data-block-id="${row.id}" data-idx="${itemIdx}" class="input-qty w-20 px-2 py-1 text-center font-bold text-slate-800 border rounded focus:outline-none transition-colors ${inputClass}">
                                <span class="error-qty text-[10px] text-red-500 font-semibold mt-1 ${errorDisplay}">${errorText}</span>
                            </div>
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
