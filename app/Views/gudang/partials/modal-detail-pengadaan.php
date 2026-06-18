<!-- Modal Detail Pengadaan -->
<div id="modal-detail-pengadaan" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" id="modal-detail-overlay"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-4xl border border-slate-100 opacity-0 scale-95" id="modal-detail-panel">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-6 flex items-start justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-indigo-500/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-1/4 w-24 h-24 rounded-full bg-blue-500/10 blur-xl"></div>
                
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl border border-indigo-500/30 text-indigo-400 flex items-center justify-center shrink-0 bg-gradient-to-br from-indigo-500/10 to-indigo-600/5 shadow-[0_0_15px_rgba(99,102,241,0.15)]">
                        <i class="fas fa-file-invoice text-2xl drop-shadow-md"></i>
                    </div>
                    <div class="space-y-1.5 pt-0.5">
                        <h3 class="text-white font-bold text-lg sm:text-xl tracking-wide drop-shadow-sm flex items-center gap-2">
                            Detail Pengajuan
                            <span id="detail-pr-number" class="text-[10px] font-bold bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-md border border-indigo-400/30">PR/...</span>
                        </h3>
                        <p class="text-xs text-slate-400" id="detail-pr-date">Tanggal: -</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white transition-all duration-200 focus:outline-none shrink-0 relative z-10 backdrop-blur-sm" id="btn-close-detail-modal">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto custom-scrollbar bg-white">
                
                <!-- Keterangan -->
                <div class="mb-6 bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan / Catatan</h4>
                    <p id="detail-keterangan" class="text-sm text-slate-700 italic">-</p>
                </div>

                <!-- Visual Stepper Timeline -->
                <div class="mb-8 px-4" id="stepper-container">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Status Pengajuan</h4>
                    <div class="relative">
                        <!-- Connecting Line -->
                        <div class="absolute left-[15px] top-4 bottom-4 w-0.5 bg-slate-200 sm:left-0 sm:right-0 sm:top-[15px] sm:bottom-auto sm:h-0.5 sm:w-full z-0">
                            <!-- Progress line (filled by JS) -->
                            <div id="stepper-progress" class="absolute left-0 top-0 w-full h-full bg-indigo-500 transition-all duration-500 z-0 origin-left scale-x-0 sm:scale-y-100"></div>
                        </div>

                        <!-- Steps -->
                        <div class="relative z-10 flex flex-col sm:flex-row justify-between gap-6 sm:gap-0">
                            
                            <!-- Step 1: Draft/Pending -->
                            <div class="flex sm:flex-col items-center gap-3 sm:gap-2 relative group step-item" data-step="1">
                                <div class="step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-slate-400 text-xs font-bold transition-colors bg-white shadow-sm">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="sm:text-center">
                                    <h5 class="text-sm font-bold text-slate-700 step-title">Diajukan</h5>
                                    <p class="text-[10px] text-slate-500 step-desc">Menunggu Persetujuan</p>
                                </div>
                            </div>

                            <!-- Step 2: Approved -->
                            <div class="flex sm:flex-col items-center gap-3 sm:gap-2 relative group step-item" data-step="2">
                                <div class="step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-slate-400 text-xs font-bold transition-colors bg-white shadow-sm">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="sm:text-center">
                                    <h5 class="text-sm font-bold text-slate-700 step-title">Disetujui</h5>
                                    <p class="text-[10px] text-slate-500 step-desc">Lolos Review</p>
                                </div>
                            </div>

                            <!-- Step 3: PO Processed -->
                            <div class="flex sm:flex-col items-center gap-3 sm:gap-2 relative group step-item" data-step="3">
                                <div class="step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-slate-400 text-xs font-bold transition-colors bg-white shadow-sm">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="sm:text-center">
                                    <h5 class="text-sm font-bold text-slate-700 step-title">Diproses PO</h5>
                                    <p class="text-[10px] text-slate-500 step-desc">Purchasing Action</p>
                                </div>
                            </div>

                            <!-- Step 4: Completed -->
                            <div class="flex sm:flex-col items-center gap-3 sm:gap-2 relative group step-item" data-step="4">
                                <div class="step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-slate-400 text-xs font-bold transition-colors bg-white shadow-sm">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="sm:text-center">
                                    <h5 class="text-sm font-bold text-slate-700 step-title">Selesai</h5>
                                    <p class="text-[10px] text-slate-500 step-desc">Barang Diterima</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <!-- Alert if Rejected -->
                    <div id="detail-rejected-alert" class="hidden mt-4 bg-red-50 border border-red-200 rounded-lg p-3 flex gap-3 items-start text-sm">
                        <i class="fas fa-times-circle text-red-500 mt-0.5"></i>
                        <div>
                            <span class="font-bold text-red-800">Pengajuan Ditolak</span>
                            <p class="text-red-700 text-xs mt-0.5">Pengajuan ini telah ditolak dan tidak akan diproses lebih lanjut.</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Daftar Item</h4>
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="overflow-x-auto min-h-[200px]">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 w-10 text-center">#</th>
                                        <th scope="col" class="px-4 py-3 min-w-[200px]">Nama Barang</th>
                                        <th scope="col" class="px-4 py-3 w-32 text-center">Volume Ajuan</th>
                                        <th scope="col" class="px-4 py-3 min-w-[150px]">Keterangan Item</th>
                                        <th scope="col" class="px-4 py-3 w-32 text-center">Status Item</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-items-tbody" class="divide-y divide-slate-100 bg-white">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end rounded-b-2xl">
                <button type="button" id="btn-close-detail-footer" class="px-5 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200 bg-slate-100 rounded-xl transition-all focus:outline-none">
                    Tutup
                </button>
            </div>
            
        </div>
    </div>
</div>
