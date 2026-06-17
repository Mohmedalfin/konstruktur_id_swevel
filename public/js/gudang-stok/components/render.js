export function renderStats(stats) {
    if (!stats) return;
    const totalEl = document.getElementById('stat-total');
    const amanEl = document.getElementById('stat-aman');
    const kritisEl = document.getElementById('stat-kritis');
    const kosongEl = document.getElementById('stat-kosong');

    if (totalEl) totalEl.textContent = stats.total ?? 0;
    if (amanEl) amanEl.textContent = stats.aman ?? 0;
    if (kritisEl) kritisEl.textContent = stats.kritis ?? 0;
    if (kosongEl) kosongEl.textContent = stats.kosong ?? 0;
}

export function renderStokList(items) {
    const container = document.getElementById('stok-table-body');
    if (!container) return;

    if (!Array.isArray(items) || items.length === 0) {
        container.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-12 text-center bg-white">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-box-open text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Belum ada data stok</p>
                    <p class="text-xs mt-1 text-slate-400">Tidak ada data yang sesuai dengan filter saat ini.</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    items.forEach(item => {
        let stokAktualClass = 'text-slate-800';
        let statusBadgeClass = 'bg-slate-50 text-slate-600 border-slate-100';
        let statusDotClass = 'bg-slate-500';
        let statusText = 'Unknown';
        
        const aktual = parseFloat(item.stok_aktual);
        const minimum = parseFloat(item.stok_minimum);

        if (aktual <= 0) {
            stokAktualClass = 'text-red-600';
            statusBadgeClass = 'bg-red-50 text-red-600 border-red-100';
            statusDotClass = 'bg-red-500';
            statusText = 'Kosong';
        } else if (aktual <= minimum) {
            stokAktualClass = 'text-amber-600';
            statusBadgeClass = 'bg-amber-50 text-amber-600 border-amber-100';
            statusDotClass = 'bg-amber-500';
            statusText = 'Kritis';
        } else {
            stokAktualClass = 'text-slate-800';
            statusBadgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
            statusDotClass = 'bg-emerald-500';
            statusText = 'Aman';
        }

        const displayMinimum = minimum % 1 === 0 ? parseInt(minimum) : minimum;
        const displayAktual = aktual % 1 === 0 ? parseInt(aktual) : aktual;

        html += `
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-500">${item.kode_barang}</td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-800">${item.nama_barang}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-semibold">${item.jenis_item}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center font-medium">${item.satuan}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center font-medium text-slate-400">${displayMinimum}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center font-bold ${stokAktualClass}">${displayAktual}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ${statusBadgeClass}">
                        <span class="w-1.5 h-1.5 rounded-full ${statusDotClass}"></span> ${statusText}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button type="button" 
                            class="btn-edit-minimum inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg transition-all focus:outline-none shadow-sm" 
                            data-id="${item.id_barang}" 
                            data-nama="${item.nama_barang}" 
                            data-satuan="${item.satuan}"
                            data-minimum="${displayMinimum}"
                            data-hs-overlay="#modal-edit-minimum">
                        <i class="fas fa-edit"></i>
                        <span>Ubah Min</span>
                    </button>
                </td>
            </tr>
        `;
    });

    container.innerHTML = html;
}
