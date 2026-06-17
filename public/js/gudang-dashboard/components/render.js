export function renderWelcome(userName) {
    const el = document.getElementById('welcome-greeting');
    if (el) {
        el.innerHTML = `Halo, <span class="text-indigo-600 font-extrabold">${userName}</span> 👋`;
    }
}

export function renderDateTime() {
    const dateEl = document.getElementById('current-date');
    const timeEl = document.getElementById('current-time');
    
    if (!dateEl || !timeEl) return;

    const updateTime = () => {
        const now = new Date();
        const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Jakarta' };
        dateEl.textContent = now.toLocaleDateString('id-ID', optionsDate);
        
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        timeEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
    };

    updateTime();
    setInterval(updateTime, 1000);
}

export function renderStats(stats) {
    if (!stats) return;

    const elements = {
        'stat-total': stats.total_barang,
        'stat-kritis': stats.stok_kritis,
        'stat-permintaan': stats.permintaan_pending,
        'stat-pengadaan': stats.pengadaan_aktif
    };

    for (const [id, value] of Object.entries(elements)) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? 0;
    }
}

export function renderKritisTable(items) {
    const container = document.getElementById('kritis-table-body');
    if (!container) return;

    if (!Array.isArray(items) || items.length === 0) {
        container.innerHTML = `
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-emerald-400 text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Semua Stok Aman</p>
                    <p class="text-xs text-slate-400">Tidak ada barang dengan stok kritis saat ini.</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    items.forEach(item => {
        const aktual = parseFloat(item.stok_aktual);
        const minimum = parseFloat(item.stok_minimum);
        
        const displayAktual = aktual % 1 === 0 ? parseInt(aktual) : aktual;
        const displayMinimum = minimum % 1 === 0 ? parseInt(minimum) : minimum;

        let statusClass = 'text-amber-600';
        if (aktual <= 0) {
            statusClass = 'text-red-600 font-bold';
        }

        const baseUrl = window.DASHBOARD_INIT?.baseUrl || '/';
        const urlPengadaan = `${baseUrl}gudang/pengadaan?action=create&id_barang=${item.id_barang}`;

        html += `
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-5 py-3 whitespace-nowrap font-mono text-xs text-slate-500 border-b border-slate-50">${item.kode_barang}</td>
                <td class="px-5 py-3 whitespace-nowrap font-semibold text-slate-800 border-b border-slate-50">${item.nama_barang}</td>
                <td class="px-5 py-3 whitespace-nowrap text-center ${statusClass} border-b border-slate-50">${displayAktual} ${item.satuan}</td>
                <td class="px-5 py-3 whitespace-nowrap text-center font-medium text-slate-500 border-b border-slate-50">${displayMinimum} ${item.satuan}</td>
                <td class="px-5 py-3 whitespace-nowrap text-right border-b border-slate-50">
                    <a href="${urlPengadaan}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-all focus:outline-none shadow-sm">
                        <i class="fas fa-plus"></i> Ajukan
                    </a>
                </td>
            </tr>
        `;
    });

    container.innerHTML = html;
}

export function renderActivities(activities) {
    const container = document.getElementById('activity-timeline');
    if (!container) return;

    if (!Array.isArray(activities) || activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-slate-400">
                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-history text-slate-300 text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-600">Belum ada aktivitas</p>
                <p class="text-xs text-slate-400">Belum ada riwayat transaksi terbaru.</p>
            </div>
        `;
        return;
    }

    let html = '<div class="relative border-l border-slate-200 ml-3 space-y-6">';
    
    const timeAgo = (dateStr) => {
        const date = new Date(dateStr);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        if (diffMins < 1) return 'Baru saja';
        if (diffMins < 60) return `${diffMins} menit lalu`;
        const diffHrs = Math.floor(diffMins / 60);
        if (diffHrs < 24) return `${diffHrs} jam lalu`;
        return date.toLocaleDateString('id-ID');
    };

    activities.forEach(act => {
        let icon = '';
        let iconBg = '';
        let title = '';
        let desc = '';

        const ts = act.created_at || act.tanggal;
        const relativeTime = timeAgo(ts);

        const statusLower = String(act.status).toLowerCase();
        let statusBadgeClass = 'text-slate-600';
        
        // Define colors based on status
        if (statusLower.includes('pending') || statusLower.includes('menunggu')) {
            iconBg = 'bg-amber-100 text-amber-600 border-amber-200';
            statusBadgeClass = 'bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide';
        } else if (statusLower.includes('proses') || statusLower.includes('ordered') || statusLower.includes('dikirim')) {
            iconBg = 'bg-blue-100 text-blue-600 border-blue-200';
            statusBadgeClass = 'bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide';
        } else if (statusLower.includes('selesai') || statusLower.includes('diterima') || statusLower.includes('disetujui')) {
            iconBg = 'bg-emerald-100 text-emerald-600 border-emerald-200';
            statusBadgeClass = 'bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide';
        } else if (statusLower.includes('tolak') || statusLower.includes('batal')) {
            iconBg = 'bg-red-100 text-red-600 border-red-200';
            statusBadgeClass = 'bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide';
        } else {
            iconBg = 'bg-slate-100 text-slate-600 border-slate-200';
            statusBadgeClass = 'bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide';
        }

        const operatorHtml = `<span class="font-bold text-indigo-600">${act.nama_operator}</span>`;
        const statusHtml = `<span class="${statusBadgeClass}">${act.status}</span>`;

        if (act.tipe === 'permintaan') {
            icon = 'fa-clipboard-list';
            title = `Permintaan Material <span class="font-bold text-slate-800">${act.no_ref}</span>`;
            desc = `Diajukan oleh ${operatorHtml} &bull; ${statusHtml}`;
        } else if (act.tipe === 'pengadaan') {
            icon = 'fa-shopping-cart';
            title = `Pengadaan (PR) <span class="font-bold text-slate-800">${act.no_ref}</span>`;
            desc = `Dibuat oleh ${operatorHtml} &bull; ${statusHtml}`;
        }

        html += `
            <div class="relative pl-6">
                <!-- Timeline Dot -->
                <div class="absolute -left-1.5 top-0 w-3 h-3 rounded-full border-2 border-white ${iconBg.split(' ')[0]} ring-2 ring-slate-100"></div>
                
                <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 shadow-sm flex items-start gap-3">
                    <div class="w-8 h-8 rounded-md flex items-center justify-center shrink-0 border ${iconBg}">
                        <i class="fas ${icon} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">${relativeTime}</p>
                        <p class="text-sm text-slate-800">${title}</p>
                        <p class="text-xs text-slate-500 mt-0.5">${desc}</p>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

export function renderHealthChart(chartHealth) {
    const container = document.getElementById('health-chart-container');
    if (!container || !chartHealth) return;

    const { aman, kritis, kosong } = chartHealth;
    const total = aman + kritis + kosong;

    if (total === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-slate-400 w-full">
                <i class="fas fa-chart-pie text-3xl mb-3 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada data</p>
                <p class="text-xs">Tambahkan master barang terlebih dahulu.</p>
            </div>
        `;
        return;
    }

    // Buat HTML canvas dan legends
    container.innerHTML = `
        <div class="donut-wrap">
            <canvas id="stokHealthDonut" width="160" height="160"></canvas>
            <div class="donut-center">
                <div class="dc-num">${total}</div>
                <div class="dc-lbl">Total<br>Barang</div>
            </div>
        </div>
        <div class="flex-1 min-w-[120px]">
            <div class="legend-row">
                <span class="legend-dot" style="background:#10b981"></span>
                <span class="text-slate-600">Aman</span>
                <span class="legend-num">${aman}</span>
            </div>
            <div class="legend-row">
                <span class="legend-dot" style="background:#f59e0b"></span>
                <span class="text-slate-600">Kritis</span>
                <span class="legend-num">${kritis}</span>
            </div>
            <div class="legend-row">
                <span class="legend-dot" style="background:#ef4444"></span>
                <span class="text-slate-600">Kosong</span>
                <span class="legend-num">${kosong}</span>
            </div>
        </div>
    `;

    const ctx = document.getElementById('stokHealthDonut');
    if (!ctx) return;

    // Pastikan Chart tersedia
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded.');
        return;
    }

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'Kritis', 'Kosong'],
            datasets: [{
                data: [aman, kritis, kosong],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: { 
                legend: { display: false }, 
                tooltip: { 
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.raw} item`
                    }
                }
                
            },

            animation: { animateRotate: true, duration: 800 }
        }
    });
}
