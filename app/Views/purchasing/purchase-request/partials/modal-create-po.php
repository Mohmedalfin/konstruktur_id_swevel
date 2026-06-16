<!-- Create PO Modal -->
<div id="modalCreatePO" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white shadow-xl w-full max-w-4xl mx-4 relative flex flex-col max-h-[95vh]">
        
        <!-- Header (Dark) -->
        <div class="bg-[#111827] p-6 text-center">
            <h2 class="text-3xl font-bold text-white tracking-widest uppercase">PURCHASE ORDER</h2>
        </div>

        <!-- Sub Header -->
        <div class="p-6 pb-2 border-b border-gray-200">
            <h3 class="text-lg font-bold text-[#1e293b]">Detail Permintaan Barang</h3>
            <p class="text-[14px] font-bold text-[#475569] mt-0.5" id="create_po_pr_number">Nomor PR: -</p>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto">
            <div class="border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-center w-12">
                                <input type="checkbox" id="checkAllItems" class="rounded border-gray-400 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-[13px] font-bold">Nama Material</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold">Volume</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold">Satuan</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold">Spesifikasi</th>
                            <th scope="col" class="px-3 py-3 text-left text-[13px] font-bold w-64">Pilih Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 bg-white" id="create_po_items_body">
                        <!-- Pending items injected via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-5 bg-gray-50 border-t border-gray-200 flex justify-center gap-4">
            <button type="button" class="bg-[#fca5a5] hover:bg-[#f87171] text-[#991b1b] font-bold text-[14px] py-2.5 px-8 rounded transition-colors shadow-sm" onclick="closeCreatePOModal()">
                Batal
            </button>
            <button type="button" class="bg-[#2563eb] hover:bg-[#1d4ed8] text-white font-bold text-[14px] py-2.5 px-6 rounded flex items-center gap-2 transition-colors shadow-sm" onclick="submitCreatePO()">
                <i class="fa-solid fa-file-circle-plus"></i> Buat PO untuk Item Terpilih
            </button>
        </div>
    </div>
</div>
