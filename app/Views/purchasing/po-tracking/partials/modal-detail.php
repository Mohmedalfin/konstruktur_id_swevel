<!-- PO Detail Modal -->
<div id="modalDetailPO" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 relative flex flex-col max-h-[95vh]">
        <!-- Close Button (Floating) -->
        <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors size-8 flex justify-center items-center rounded-lg hover:bg-gray-100 z-10" onclick="closeDetailModal()">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <!-- Header -->
        <div class="p-6 pb-2 relative">
            <h2 class="text-2xl font-black text-[#1e293b]" id="detail_po_number">PO-XXXX-XX-XXX</h2>
            <p class="text-[15px] font-bold text-[#334155] mt-1" id="detail_supplier_name">Supplier: -</p>
            
            <div class="mt-4" id="detail_status_container">
                <!-- Status badge injected via JS -->
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto pt-4 space-y-6">
            
            <!-- Pelacakan Logistik Card -->
            <div class="bg-[#f8fafc] border border-gray-200 rounded-xl p-5">
                <h3 class="text-[17px] font-bold text-[#1e293b] mb-6">Pelacakan Logistik</h3>
                
                <!-- Stepper -->
                <div class="relative flex justify-between items-center w-full max-w-2xl mx-auto mb-8 px-4" id="stepper_container">
                    <!-- Line -->
                    <div class="absolute top-6 left-10 right-10 h-0.5 bg-gray-300 z-0"></div>
                    
                    <!-- Step 1: PO Dibuat -->
                    <div class="relative z-10 flex flex-col items-center group w-1/4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm transition-colors" id="step_1_icon">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-[13px] font-bold text-[#1e293b]">PO Dibuat</p>
                            <p class="text-[11px] text-gray-500 font-medium" id="step_1_date">-</p>
                        </div>
                    </div>

                    <!-- Step 2: Diproses Supplier -->
                    <div class="relative z-10 flex flex-col items-center group w-1/4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm transition-colors" id="step_2_icon">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-[13px] font-bold" id="step_2_title">Diproses Supplier</p>
                            <p class="text-[11px] text-gray-500 font-medium leading-tight" id="step_2_desc">-</p>
                        </div>
                    </div>

                    <!-- Step 3: Sedang Dikirim -->
                    <div class="relative z-10 flex flex-col items-center group w-1/4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm transition-colors" id="step_3_icon">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-[13px] font-bold text-gray-500" id="step_3_title">Sedang Dikirim</p>
                        </div>
                    </div>

                    <!-- Step 4: Tiba di Lokasi -->
                    <div class="relative z-10 flex flex-col items-center group w-1/4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm transition-colors bg-gray-300 text-white" id="step_4_icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-[13px] font-bold text-gray-500" id="step_4_title">Tiba di Lokasi</p>
                        </div>
                    </div>
                </div>

                <!-- Call To Action Box (Hidden if Selesai) -->
                <div id="cta_box" class="bg-white border border-gray-300 rounded-lg p-4 flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="text-[#0f172a] text-3xl ml-2">
                            <i class="fa-solid fa-box-open" id="cta_icon"></i>
                        </div>
                        <div>
                            <h4 class="text-[15px] font-bold text-[#0f172a]" id="cta_title">Konfirmasi Pengiriman</h4>
                            <p class="text-[12px] font-medium text-gray-600 mt-0.5" id="cta_desc">Klik tombol di samping jika supplier sudah mengonfirmasi pengiriman barang</p>
                        </div>
                    </div>
                    <button type="button" class="bg-[#0061ff] hover:bg-blue-700 text-white font-bold text-[13px] py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors shadow-sm" id="cta_btn">
                        <i class="fa-solid fa-truck"></i> <span id="cta_btn_text">Tandai Sedang Dikirim</span>
                    </button>
                </div>
            </div>

            <!-- Ringkasan Material PO -->
            <div>
                <h3 class="text-[17px] font-bold text-[#1e293b] mb-4">Ringkasan Material PO</h3>
                <div class="overflow-hidden border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th scope="col" class="px-2 py-3 text-left text-[13px] font-bold text-[#0f172a]">Nama Material</th>
                                <th scope="col" class="px-2 py-3 text-center text-[13px] font-bold text-[#0f172a]">Volume</th>
                                <th scope="col" class="px-2 py-3 text-center text-[13px] font-bold text-[#0f172a]">Satuan</th>
                                <th scope="col" class="px-2 py-3 text-center text-[13px] font-bold text-[#0f172a]">Spesifikasi</th>
                                <th scope="col" class="px-2 py-3 text-right text-[13px] font-bold text-[#0f172a]">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="detail_items_body">
                            <!-- Items injected via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="px-2 py-4 text-left text-[14px] font-black text-[#0f172a]">Total Harga</td>
                                <td class="px-2 py-4 text-right text-[14px] font-black text-[#0f172a]" id="detail_total_harga">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
