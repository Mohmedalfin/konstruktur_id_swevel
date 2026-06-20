<!-- Modal Rekonsiliasi Sisa Material -->
<div id="modal-reconcile-proyek" class="fixed inset-0 z-[80] hidden bg-black/50 items-center justify-center font-sans pointer-events-auto">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl mx-4 relative flex flex-col min-h-[600px] max-h-[90vh]">
        <!-- Header -->
        <div class="flex justify-between items-center py-4 px-6 border-b bg-[#0f172a] rounded-t-xl">
            <div>
                <h3 id="modal-reconcile-title" class="font-bold text-white text-lg flex items-center gap-2">
                    <i class="fa-solid fa-boxes-packing text-emerald-400"></i>
                    Rekonsiliasi Sisa Material
                </h3>
                <p class="text-sm text-slate-300 mt-1">Proyek: <strong id="reconcile-project-name" class="text-white"></strong></p>
            </div>
            <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white focus:outline-none focus:bg-slate-700 transition-colors disabled:opacity-50 disabled:pointer-events-none" aria-label="Close" onclick="closeReconcileModal()">
                <span class="sr-only">Tutup</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30 flex-1">
        <div class="mb-5 bg-blue-50/50 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center size-8 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="text-sm font-bold text-blue-900">Panduan Rekonsiliasi Material</h3>
            </div>
            <p class="text-xs text-blue-800 mb-4 leading-relaxed">
                Proyek akan ditandai selesai. Seluruh <strong>Sisa Aktual</strong> material di lapangan wajib dialokasikan hingga tuntas (Balance) ke dalam opsi berikut:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-white rounded-lg p-3 border border-blue-100 shadow-sm">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="fa-solid fa-warehouse text-emerald-600 text-xs"></i>
                        <span class="text-xs font-bold text-slate-800">Retur (Kembali ke Gudang)</span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-tight">Pengembalian fisik material ke Gudang Utama. Akan menambah kembali stok pusat.</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-blue-100 shadow-sm">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="fa-solid fa-truck-fast text-blue-600 text-xs"></i>
                        <span class="text-xs font-bold text-slate-800">Mutasi (Oper ke Proyek Lain)</span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-tight">Pemindahan material ke proyek aktif lain yang memiliki RAB untuk material ini.</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-blue-100 shadow-sm">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="fa-solid fa-trash-can text-red-500 text-xs"></i>
                        <span class="text-xs font-bold text-slate-800">Waste (Terbuang/Susut)</span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-tight">Material yang rusak, mengeras, hilang, atau susut tak terpakai di lapangan.</p>
                </div>
            </div>
        </div>
        
        <div class="border border-[#e2e8f0] rounded-xl overflow-x-auto overflow-y-hidden bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#0f172a]">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-slate-100 uppercase tracking-wider">Nama Material</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-slate-100 uppercase tracking-wider w-32">Sisa Aktual</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-slate-100 uppercase tracking-wider w-32">Retur</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-slate-100 uppercase tracking-wider min-w-[300px]">Mutasi (Pilih Proyek)</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-slate-100 uppercase tracking-wider w-32">Waste</th>
                        <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold text-slate-100 uppercase tracking-wider w-24">Status</th>
                    </tr>
                </thead>
                    <tbody id="table-reconcile-body" class="divide-y divide-slate-200 bg-white">
                        <!-- Rows rendered by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-white rounded-b-xl">
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none" onclick="closeReconcileModal()">
                Batal
            </button>
            <button type="button" id="btn-proses-reconcile" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors focus:outline-none shadow-sm inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check-circle"></i>
                <span>Proses & Selesaikan Proyek</span>
            </button>
        </div>
    </div>
</div>

<script>
    function closeReconcileModal() {
        const modal = document.getElementById('modal-reconcile-proyek');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }
    
    // Expose closeReconcileModal globally for window access
    window.closeReconcileModal = closeReconcileModal;
</script>
