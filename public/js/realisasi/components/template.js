export function renderCategoryRow(item) {
    const icon = item.expanded ? 'fa-minus' : 'fa-plus';
    return `
        <tr class="bg-[#415b82] text-white border-b border-white/10" data-id="${item.id}">
            <td class="px-3 md:px-5 py-3.5 text-center">
                <button type="button" class="toggle-category focus:outline-none w-[20px] h-[20px] rounded-full border border-white inline-flex items-center justify-center hover:bg-white/20 transition-colors" data-id="${item.id}">
                    <i class="fas ${icon} text-[10px]"></i>
                </button>
            </td>
            <td colspan="6" class="px-3 md:px-5 py-3.5 text-xs font-bold uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-1 h-4 bg-amber-500 rounded-sm"></span>
                    ${item.uraian}
                </div>
            </td>
        </tr>
    `;
}

export function renderItemRow(item, depth = 0) {
    const chevron     = item.expandedItem ? 'fa-chevron-up' : 'fa-chevron-down';
    const hasChildren = item.children && item.children.length > 0;

    const noPadding     = `padding-left: ${(depth * 0.5) + 1.5}rem`;
    const uraianIndent  = `padding-left: ${depth * 1.5}rem`;

    const actionCell = `<button type="button" class="toggle-item text-slate-800 hover:text-primary transition-colors focus:outline-none" data-id="${item.id}">
                            <i class="fas ${chevron} font-bold text-sm"></i>
                        </button>`;

    return `
        <tr class="bg-white border-b border-[#e2e8f0] hover:bg-slate-50 transition-colors" data-item-id="${item.id}" data-depth="${depth}">
            <td class="px-1 md:px-2 py-3.5 text-left font-medium text-slate-600 text-sm whitespace-nowrap">
                <div style="${noPadding}">
                    <span class="tabular-nums">${item.no}</span>
                </div>
            </td>
            <td class="px-3 md:px-5 py-3.5 font-medium text-slate-700 text-sm min-w-[200px]">
                <div style="${uraianIndent}" class="flex items-start gap-1.5">
                    ${depth > 0 ? `<span class="text-slate-300 flex-shrink-0">└─</span>` : ''}
                    <span>${item.uraian}</span>
                </div>
            </td>
            <td class="px-3 md:px-5 py-3.5 text-center text-slate-700 font-medium text-sm">${item.satuan}</td>
            <td class="px-3 md:px-5 py-3.5 text-center text-slate-700 font-medium text-sm">${item.volume}</td>
            <td class="px-3 md:px-5 py-3.5 text-center text-slate-700 font-medium text-sm">${item.volumeTercapai}</td>
            <td class="px-3 md:px-5 py-3.5 text-center text-slate-700 font-medium text-sm">${renderProgressBar(item.progress)}</td>
            <td class="px-3 md:px-5 py-3.5 text-center">${actionCell}</td>
        </tr>
    `;
}

function renderProgressBar(progress) {
    const pct = parseFloat(progress) || 0;
    const color = pct >= 100 ? 'bg-green-500' : pct > 0 ? 'bg-amber-500' : 'bg-slate-200';
    return `
        <div class="flex items-center gap-2 justify-center min-w-[80px]">
            <div class="flex-1 bg-slate-100 rounded-full h-1.5 max-w-[60px]">
                <div class="${color} h-1.5 rounded-full" style="width:${Math.min(pct, 100)}%"></div>
            </div>
            <span class="text-xs font-semibold text-slate-600 w-9 text-right">${pct}%</span>
        </div>
    `;
}

export function renderItemLogTable(item) {
    if (!item.logs || item.logs.length === 0) {
        return `
            <tr class="bg-slate-50 border-b border-[#e2e8f0]" data-log-parent="${item.id}">
                <td colspan="7" class="p-0">
                    <div class="text-center py-4 text-sm text-slate-400 italic border-x border-[#e2e8f0]">Belum ada progress dicatat.</div>
                </td>
            </tr>
        `;
    }

    const logsHtml = item.logs.map((log, i) => `
        <tr class="bg-white border-b border-[#e2e8f0]">
            <td class="px-3 md:px-5 py-2.5 text-center text-sm font-medium text-slate-700">${log.no}</td>
            <td class="px-3 md:px-5 py-2.5 text-center text-sm font-medium text-slate-700">${log.tanggal}</td>
            <td class="px-3 md:px-5 py-2.5 text-center text-sm font-medium text-slate-700">${item.satuan}</td>
            <td class="px-3 md:px-5 py-2.5 text-center text-sm font-medium text-slate-700">${log.volumeTercapai}</td>
            <td class="px-3 md:px-5 py-2.5 text-center text-sm font-medium text-slate-700">${log.progress}</td>
            <td class="px-3 md:px-5 py-2.5 text-center text-sm font-medium text-slate-700 truncate max-w-[150px]" title="${log.keterangan}">${log.keterangan}</td>
            <td class="px-3 md:px-5 py-2.5 text-center">
                <button type="button" class="text-red-400 hover:text-white bg-red-50 hover:bg-red-500 px-2 py-1 rounded transition-colors btn-delete-log" data-log-id="${log.id_realisasi}">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </td>
        </tr>
    `).join('');

    return `
        <tr class="bg-slate-50 border-b border-[#e2e8f0]" data-log-parent="${item.id}">
            <td colspan="7" class="p-0 border-x border-[#e2e8f0]">
                <div class="w-full">
                    <table class="w-full text-left border-collapse">
                        <colgroup>
                            <col style="width: 5rem">
                            <col style="width: 10rem">
                            <col style="width: 8rem">
                            <col style="width: 10rem">
                            <col style="width: 10rem">
                            <col class="min-w-[150px]">
                            <col style="width: 6rem">
                        </colgroup>
                        <thead>
                            <tr class="bg-[#1e293b] text-white">
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">No</th>
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">Tanggal</th>
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">Satuan</th>
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">Vol. Tercapai</th>
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">Progress</th>
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">Keterangan</th>
                                <th class="px-3 md:px-5 py-3 text-center text-xs font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${logsHtml}
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    `;
}


export function renderSDMDateRow(item) {
    const icon    = item.expanded ? 'fa-minus' : 'fa-plus';
    const chevron = item.expanded ? 'fa-chevron-up' : 'fa-chevron-down';
    return `
        <tr class="bg-[#415b82] text-white border-b border-white/10 cursor-pointer transition-colors hover:bg-[#415b82]/90 group" data-sdm-id="${item.id}">
            <td class="px-5 py-3.5 text-center">
                <button type="button" class="toggle-sdm focus:outline-none w-[20px] h-[20px] rounded-full border border-white inline-flex items-center justify-center hover:bg-white/20 transition-colors" data-id="${item.id}">
                    <i class="fas ${icon} text-[10px]"></i>
                </button>
            </td>
            <td class="px-5 py-3.5 font-bold tracking-wider">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-1 h-4 bg-amber-500 rounded-sm"></span>
                    ${item.tanggal}
                </div>
            </td>
            <td class="px-5 py-3.5 text-center">
                <i class="fas ${chevron} text-white/50 group-hover:text-white transition-colors"></i>
            </td>
        </tr>
    `;
}

export function renderSDMDetailArea(item) {
    const activeTab = item.activeTab || 'bahan';
    const getTabClass = (tabName) => activeTab === tabName
        ? "px-6 py-1.5 rounded-full bg-[#0f172a] text-white text-xs font-bold shadow-sm focus:outline-none"
        : "px-6 py-1.5 rounded-full bg-white border border-gray-300 text-slate-600 hover:text-[#1e293b] text-xs font-semibold focus:outline-none transition-colors sdm-subtab-btn";

    return `
        <tr class="bg-[#e2e8f0]/50 border-b border-gray-200" data-sdm-detail="${item.id}">
            <td colspan="3" class="p-0">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex gap-2">
                        <button class="${getTabClass('bahan')}" data-tab="bahan" data-id="${item.id}">Bahan</button>
                        <button class="${getTabClass('alat')}" data-tab="alat" data-id="${item.id}">Alat</button>
                        <button class="${getTabClass('tenaga')}" data-tab="tenaga" data-id="${item.id}">Tenaga Kerja</button>
                        <button class="${getTabClass('dokumentasi')}" data-tab="dokumentasi" data-id="${item.id}">Dokumentasi</button>
                    </div>
                </div>
                <div class="bg-white border-y border-gray-200 overflow-hidden w-full">
                    ${renderSDMSubTable(item, activeTab)}
                </div>
            </td>
        </tr>
    `;
}

export function renderSDMSubTable(item, type) {
    const data = item[type] || [];
    if (data.length === 0) {
        return `<div class="py-10 text-center text-slate-500 text-sm italic bg-white">Belum ada data ${type} dicatat untuk tanggal ini.</div>`;
    }

    let headerHtml = '';
    let rowsHtml   = '';

    if (type === 'dokumentasi') {
        const photosHtml = data.map(url => renderTimelinePhoto(url)).join('');
        return `<div class="p-5 flex flex-wrap gap-4 bg-slate-50">${photosHtml}</div>`;
    }

    const typeLabel = type.charAt(0).toUpperCase() + type.slice(1);
    
    headerHtml = `
        <tr class="bg-[#0f172a] text-white font-bold uppercase tracking-wider text-[10px] md:text-[11px]">
            <th class="px-5 py-2.5 text-center w-[5%]">No</th>
            <th class="px-5 py-2.5 w-[25%]">${typeLabel}</th>
            <th class="px-5 py-2.5 text-center w-[10%]">Qty</th>
            <th class="px-5 py-2.5 text-center w-[10%]">Satuan</th>
            <th class="px-5 py-2.5 text-center w-[15%]">Spesifikasi</th>
            <th class="px-5 py-2.5 text-center w-[15%]">Merk</th>
            <th class="px-5 py-2.5 w-[20%]">Keterangan</th>
            <th class="px-5 py-2.5 text-center w-[10%]">Aksi</th>
        </tr>
    `;
    
    rowsHtml = data.map((row, i) => `
        <tr class="border-b border-gray-100 hover:bg-slate-50 transition-colors text-[12px] md:text-[13px]">
            <td class="px-5 py-3 text-center text-slate-600">${i + 1}</td>
            <td class="px-5 py-3 font-medium text-[#1e293b]">${row.nama}</td>
            <td class="px-5 py-3 text-center font-bold text-[#1e293b]">${row.qty}</td>
            <td class="px-5 py-3 text-center text-slate-500">${row.satuan}</td>
            <td class="px-5 py-3 text-center text-slate-500">${row.spesifikasi || '-'}</td>
            <td class="px-5 py-3 text-center text-slate-500">${row.merk || '-'}</td>
            <td class="px-5 py-3 text-slate-500">${row.keterangan || '-'}</td>
            <td class="px-5 py-3 text-center">
                <button type="button" class="btn-delete-sdm-item w-7 h-7 flex items-center justify-center rounded bg-white text-red-500 hover:bg-red-50 border border-gray-200 shadow-sm transition-colors focus:outline-none" data-id="${row.id_realisasi_sdm_item}">
                    <i class="fas fa-trash text-[10px]"></i>
                </button>
            </td>
        </tr>
    `).join('');

    return `
        <table class="w-full text-left">
            <thead>${headerHtml}</thead>
            <tbody>${rowsHtml}</tbody>
        </table>
    `;
}

export function renderTimelineItem(dateGroup) {
    const tasksHtml = dateGroup.tasks && dateGroup.tasks.length > 0 
        ? dateGroup.tasks.map(renderTimelineTask).join('') 
        : '<li class="text-slate-500 italic">Tidak ada catatan pekerjaan.</li>';

    const photosHtml = dateGroup.photos && dateGroup.photos.length > 0 
        ? dateGroup.photos.map(renderTimelinePhoto).join('') 
        : '<p class="text-sm text-slate-500 italic">Tidak ada foto dokumentasi.</p>';

    return `
        <div class="relative mb-8 timeline-item" data-date="${dateGroup.date}">
            <div class="absolute -left-[32px] top-1.5 w-5 h-5 rounded-full bg-[#f59e0b] border-[4px] border-white shadow-sm z-10"></div>
            
            <h3 class="text-md font-semibold text-[#1e293b] mb-4">${dateGroup.date}</h3>

            <div class="space-y-3">
                <div class="rounded-xl p-5 border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-100 text-amber-600">
                            <i class="fas fa-tasks text-[11px]"></i>
                        </span>
                        <h4 class="font-bold text-[#1e293b] text-sm tracking-wide">Daftar Pekerjaan</h4>
                        <span class="ml-auto text-[10px] font-semibold text-slate-400 uppercase tracking-widest">${dateGroup.tasks ? dateGroup.tasks.length : 0} item</span>
                    </div>
                    <div class="space-y-3">
                        ${tasksHtml}
                    </div>
                </div>

                <div class="bg-[#e2e8f0]/60 rounded-lg p-5 border border-[#cbd5e1]/50">
                    <div class="flex items-center gap-2.5 mb-3">
                        <h4 class="font-semibold text-[#1e293b] text-sm">Dokumentasi</h4>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        ${photosHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
}

export function renderTimelineTask(task) {
    const hasKet = task.keterangan && task.keterangan !== '-' && task.keterangan.trim() !== '';
    const ketBlock = hasKet
        ? `<div class="mt-2 pt-2 border-t border-slate-100">
               <div class="flex items-start gap-1.5">
                   <i class="fas fa-quote-left text-[8px] text-slate-300 mt-1 flex-shrink-0"></i>
                   <p class="text-[12px] text-slate-500 leading-relaxed italic">${task.keterangan}</p>
               </div>
           </div>`
        : '';

    return `
        <div class="flex gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100 hover:border-amber-200 hover:bg-amber-50/30 transition-colors">
            <div class="flex-shrink-0 mt-0.5">
                <div class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5"></div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <p class="text-[13px] font-semibold text-slate-700 leading-snug">${task.uraian}</p>
                    <span class="flex-shrink-0 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        <i class="fas fa-check-circle text-[9px]"></i> ${task.volume}
                    </span>
                </div>
                ${ketBlock}
            </div>
        </div>
    `;
}

export function renderTimelinePhoto(url) {
    const fullUrl = url.startsWith('http') ? url : `/${url.replace(/^\//, '')}`;
    return `
        <div
            class="group relative w-24 h-24 rounded-xl overflow-hidden border-2 border-white shadow-md cursor-pointer hover:scale-105 hover:shadow-lg transition-all duration-200"
            onclick="window._openPhotoLightbox('${fullUrl}')"
        >
            <img src="${fullUrl}" class="w-full h-full object-cover" alt="Dokumentasi">
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-colors flex items-center justify-center">
                <i class="fas fa-expand text-white opacity-0 group-hover:opacity-100 text-lg transition-opacity drop-shadow"></i>
            </div>
        </div>
    `;
}

export function renderSDMEmpty() {
    return `
        <tr>
            <td colspan="3" class="px-5 py-20 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100 shadow-sm">
                        <i class="fas fa-clipboard-list text-2xl text-slate-300"></i>
                    </div>
                    <h3 class="text-slate-800 font-bold text-base mb-1 text-slate-700">Belum Ada Riwayat Penggunaan</h3>
                    <p class="text-slate-500 text-sm max-w-[300px] mx-auto leading-relaxed">Silakan klik tombol <span class="font-bold text-slate-700">"Tambah Penggunaan".</p>
                </div>
            </td>
        </tr>
    `;
}

export function renderTimelineEmpty() {
    return `
        <div class="bg-[#e2e8f0]/60 rounded-lg p-8 border border-[#cbd5e1]/50 flex flex-col items-center justify-center min-h-[150px] text-center ml-[-32px]">
            <i class="fas fa-calendar-times text-3xl text-slate-400 mb-3"></i>
            <p class="text-slate-500 font-semibold text-sm">Belum ada log dokumentasi yang dicatat.</p>
        </div>
    `;
}


