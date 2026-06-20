<!-- PO Detail Modal -->
<div id="modalDetailPO" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 relative flex flex-col max-h-[95vh] overflow-hidden">

        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-file-invoice-dollar text-base md:text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base md:text-lg" id="detail_po_number">PO-XXXX-XX-XXX</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-slate-400 text-[10px] md:text-xs" id="detail_supplier_name">Supplier: -</p>
                        <div id="detail_status_container" class="inline-flex scale-[0.8] origin-left"></div>
                    </div>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeDetailModal()">
                <i class="fas fa-times"></i>
            </button>
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
                <div id="cta_box" class="mt-8 border-t border-dashed border-slate-200 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800" id="cta_title">Tindakan Selanjutnya</h4>
                        <p class="text-xs text-slate-500 mt-0.5" id="cta_desc">Klik tombol di samping untuk memperbarui status logistik PO ini.</p>
                    </div>
                    <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold text-sm py-2 px-6 rounded-lg transition-all shadow-sm focus:ring-2 focus:ring-primary/20 shrink-0" id="cta_btn">
                        <i class="fa-solid fa-check text-xs"></i> <span id="cta_btn_text">Tandai Sedang Dikirim</span>
                    </button>
                </div>
            </div>

            <!-- Ringkasan Material PO -->
            <div>
                <h3 class="text-[17px] font-bold text-[#1e293b] mb-4">Ringkasan Material PO</h3>
                <div class="overflow-hidden border border-slate-200 rounded-xl bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-[#0f172a] text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider">Nama Material</th>
                                <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Volume</th>
                                <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Satuan</th>
                                <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider">Spesifikasi</th>
                                <th scope="col" class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200" id="detail_items_body">
                            <!-- Items injected via JS -->
                        </tbody>
                        <tfoot class="bg-slate-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-left text-sm font-black text-slate-800 uppercase">Total Harga</td>
                                <td class="px-4 py-3 text-right text-sm font-black text-primary" id="detail_total_harga">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
