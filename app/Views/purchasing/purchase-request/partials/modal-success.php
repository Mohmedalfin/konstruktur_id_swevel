<!-- Success PO Modal -->
<div id="modalSuccessPO" class="fixed inset-0 z-50 hidden bg-black/70 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 relative flex flex-col items-center p-8 text-center max-h-[90vh]">
        
        <!-- Big Green Check -->
        <div class="w-24 h-24 bg-[#dcfce3] rounded-full flex items-center justify-center text-[#22c55e] text-5xl mb-6 shadow-sm">
            <i class="fa-solid fa-check"></i>
        </div>

        <h2 class="text-2xl font-black text-[#1e293b] mb-6">Pembuatan PO Berhasil!</h2>

        <!-- PO List Container -->
        <div class="w-full flex flex-col gap-4 overflow-y-auto pb-4" id="success_po_list">
            <!-- Injected via JS -->
        </div>

        <!-- Buttons -->
        <div class="w-full flex justify-between gap-4 mt-6 pt-4">
            <button type="button" class="w-1/2 bg-white border-2 border-gray-500 text-gray-600 hover:bg-gray-50 font-bold text-[14px] py-3 px-4 rounded transition-colors" onclick="window.location.reload()">
                Kembali ke Daftar PR
            </button>
            <a href="<?= base_url('purchasing/po-tracking') ?>" class="w-1/2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white font-bold text-[14px] py-3 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                Lacak Status PO <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
