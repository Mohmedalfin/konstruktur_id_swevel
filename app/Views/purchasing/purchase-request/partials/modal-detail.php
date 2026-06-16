<!-- PR Detail Modal -->
<div id="modalDetailPR" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 relative flex flex-col max-h-[95vh]">
        <!-- Close Button -->
        <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors size-8 flex justify-center items-center rounded-lg hover:bg-gray-100 z-10" onclick="closeDetailModal()">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <!-- Header -->
        <div class="p-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-black text-[#1e293b]">Detail Permintaan Barang</h2>
            <p class="text-[14px] font-bold text-[#475569] mt-1" id="detail_pr_number">Nomor PR: -</p>
            <div class="mt-3" id="detail_status_container"></div>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto">
            <div class="border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold w-12">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-[13px] font-bold">Nama Material</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold">Volume</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold">Satuan</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold">Spesifikasi</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold w-28">Status</th>
                            <th scope="col" class="px-3 py-3 text-center text-[13px] font-bold w-32">No. PO</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 bg-white" id="detail_items_body">
                        <!-- Items injected via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-100 flex justify-center" id="detail_footer">
            <button type="button" class="bg-[#bbf7d0] hover:bg-[#86efac] text-[#166534] font-bold text-[15px] py-2 px-6 rounded transition-colors shadow-sm" onclick="openCreatePOModal()">
                Buat PO <i class="fa-solid fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
</div>
