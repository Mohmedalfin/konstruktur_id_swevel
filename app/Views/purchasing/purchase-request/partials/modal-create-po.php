<!-- Create PO Modal -->
<div id="modalCreatePO" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 relative flex flex-col max-h-[95vh] overflow-hidden">
        
        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-file-signature text-base md:text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base md:text-lg">Buat Purchase Order (PO)</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-slate-400 text-[10px] md:text-xs" id="create_po_pr_number">Nomor PR: -</p>
                    </div>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeCreatePOModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-[#0f172a] text-white">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">
                                <input type="checkbox" id="checkAllItems" class="rounded border-slate-600 bg-slate-800 text-primary focus:ring-primary focus:ring-offset-slate-900 cursor-pointer">
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider">Nama Material</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Volume</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Satuan</th>
                            <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Spesifikasi</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider w-64">Pilih Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white" id="create_po_items_body">
                        <!-- Pending items injected via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" class="inline-flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 text-slate-600 font-semibold text-sm py-2 px-5 rounded-lg transition-all focus:ring-2 focus:ring-slate-100" onclick="closeCreatePOModal()">
                Batal
            </button>
            <button type="button" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold text-sm py-2 px-5 rounded-lg transition-all shadow-sm focus:ring-2 focus:ring-primary/20" onclick="submitCreatePO()">
                <i class="fa-solid fa-file-circle-plus"></i> Buat PO Terpilih
            </button>
        </div>
    </div>
</div>
