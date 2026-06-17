<!-- Modal Detail Permintaan -->
<div id="modal-detail-permintaan" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" aria-hidden="true" id="modal-detail-permintaan-overlay"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-4xl border border-slate-100 opacity-0 scale-95" id="modal-detail-permintaan-panel">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-6 flex items-start justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-blue-500/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-1/4 w-24 h-24 rounded-full bg-amber-500/10 blur-xl"></div>
                
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 bg-gradient-to-br from-amber-500/10 to-amber-600/5 shadow-[0_0_15px_rgba(245,158,11,0.15)]">
                        <i class="fas fa-file-invoice text-2xl drop-shadow-md"></i>
                    </div>
                    <div class="space-y-1.5 pt-0.5">
                        <h3 class="text-white font-bold text-lg sm:text-xl tracking-wide drop-shadow-sm flex items-center gap-2">
                            Detail Permintaan
                            <span id="detail-nomor-permintaan" class="text-[10px] font-bold bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-md border border-amber-400/30">REQ/...</span>
                        </h3>
                        <p class="text-xs text-slate-400" id="detail-tanggal-permintaan">Tanggal: -</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white transition-all duration-200 focus:outline-none shrink-0 relative z-10 backdrop-blur-sm" id="btn-close-detail-permintaan-header">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto custom-scrollbar bg-white">
                
                <!-- Info Grid (Pemohon, Alamat, Catatan) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" id="detail-info-grid">
                    <!-- Dinamis diisi dari JS -->
                </div>

                <!-- Visual Stepper Timeline -->
                <div class="mb-8 px-4" id="stepper-permintaan-container">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Status Permintaan</h4>
                    <div class="relative">
                        <!-- Connecting Line -->
                        <div class="absolute left-[15px] top-4 bottom-4 w-0.5 bg-slate-200 sm:left-0 sm:right-0 sm:top-[15px] sm:bottom-auto sm:h-0.5 sm:w-full z-0">
                            <!-- Progress line (filled by JS) -->
                            <div id="stepper-permintaan-progress" class="absolute left-0 top-0 w-full h-full bg-emerald-500 transition-all duration-500 z-0 origin-left scale-x-0 sm:scale-y-100"></div>
                        </div>

                        <!-- Steps -->
                        <div class="relative z-10 flex flex-col sm:flex-row justify-between gap-6 sm:gap-0" id="stepper-permintaan-steps">
                            <!-- Langkah-langkah digenerate via JS -->
                        </div>
                    </div>
                    
                    <!-- Alert if Rejected -->
                    <div id="detail-permintaan-rejected-alert" class="hidden mt-4 bg-red-50 border border-red-200 rounded-lg p-3 flex gap-3 items-start text-sm">
                        <i class="fas fa-times-circle text-red-500 mt-0.5"></i>
                        <div>
                            <span class="font-bold text-red-800">Permintaan Ditolak</span>
                            <p class="text-red-700 text-xs mt-0.5" id="detail-permintaan-rejected-note">Permintaan ini telah ditolak dan tidak akan diproses lebih lanjut.</p>
                        </div>
                    </div>
                </div>

                <!-- Items Container -->
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Daftar Item per Proyek</h4>
                    <div class="space-y-4" id="detail-modal-body">
                        <!-- Dinamis diisi dari JS -->
                    </div>
                </div>

            </div>

            <!-- Footer for Actions -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between rounded-b-2xl">
                <!-- Action Buttons Container -->
                <div id="detail-modal-actions" class="flex flex-wrap items-center gap-3">
                    <!-- Dinamis diisi dari JS -->
                </div>
                <!-- Close Button -->
                <button type="button" id="btn-close-detail-permintaan-footer" class="px-5 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200 bg-slate-100 rounded-xl transition-all focus:outline-none">
                    Tutup
                </button>
            </div>
            
        </div>
    </div>
</div>
