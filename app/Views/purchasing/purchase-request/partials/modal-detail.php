<!-- PR Detail Modal -->
<div id="modalDetailPR" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 relative flex flex-col max-h-[95vh] overflow-hidden">
        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-file-invoice text-base md:text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base md:text-lg">Detail Permintaan Barang</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-slate-400 text-[10px] md:text-xs" id="detail_pr_number">Nomor PR: -</p>
                        <div id="detail_status_container" class="inline-flex scale-[0.8] origin-left"></div>
                    </div>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeDetailModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-[#0f172a] text-white">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider">Nama Material</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Volume</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Satuan</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Spesifikasi</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider w-28">Status</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider w-32">No. PO</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white" id="detail_items_body">
                        <!-- Items injected via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end" id="detail_footer">
            <button type="button" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold text-sm py-2 px-5 rounded-lg transition-all shadow-sm focus:ring-2 focus:ring-primary/20" onclick="openCreatePOModal()">
                Buat PO <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </div>
</div>
