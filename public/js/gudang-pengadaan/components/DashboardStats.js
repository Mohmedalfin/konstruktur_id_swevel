export class DashboardStats {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
    }

    render(state) {
        if (!this.container) return;

        if (state.loading.stats && !state.stats) {
            this.container.innerHTML = `
                <div class="col-span-2 lg:col-span-4 text-center py-4 text-slate-400">
                    <i class="fas fa-spinner fa-spin"></i> Memuat statistik...
                </div>
            `;
            return;
        }

        const stats = state.stats || { total: 0, pending: 0, processing: 0, completed: 0 };

        this.container.innerHTML = `
            <!-- Total Pengajuan -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Total Pengajuan</p>
                        <h3 class="text-2xl font-black text-slate-800 leading-none">${stats.total}</h3>
                    </div>
                </div>
            </div>

            <!-- Menunggu Persetujuan/Draft -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Draft / Pending</p>
                        <h3 class="text-2xl font-black text-slate-800 leading-none">${stats.pending}</h3>
                    </div>
                </div>
            </div>

            <!-- Diproses PO -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Diproses PO</p>
                        <h3 class="text-2xl font-black text-slate-800 leading-none">${stats.processing}</h3>
                    </div>
                </div>
            </div>

            <!-- Selesai -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-box-open text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Selesai / Masuk</p>
                        <h3 class="text-2xl font-black text-slate-800 leading-none">${stats.completed}</h3>
                    </div>
                </div>
            </div>
        `;
        
        // Handle critical stock panel
        const alertPanel = document.getElementById('alert-panel-container');
        const countSpan = document.getElementById('kritis-count');
        if (alertPanel && countSpan && state.criticalItems) {
            const count = state.criticalItems.length;
            if (count > 0) {
                countSpan.textContent = count;
                alertPanel.classList.remove('hidden');
            } else {
                alertPanel.classList.add('hidden');
            }
        }
    }
}
