<div id="modal-detail-permintaan" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-6xl sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-white border border-slate-200 shadow-2xl rounded-2xl pointer-events-auto w-full overflow-hidden max-h-[calc(100vh-1.5rem)] sm:max-h-[calc(100vh-2.5rem)]">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-6 flex items-start justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-blue-500/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-1/4 w-24 h-24 rounded-full bg-amber-500/10 blur-xl"></div>
                
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 bg-gradient-to-br from-amber-500/10 to-amber-600/5 shadow-[0_0_15px_rgba(245,158,11,0.15)]">
                        <i class="fas fa-file-invoice text-2xl drop-shadow-md"></i>
                    </div>
                    <div class="space-y-1.5 pt-0.5">
                        <h2 class="text-white font-bold text-lg sm:text-xl tracking-wide drop-shadow-sm" id="detail-nomor-tanggal">Memuat...</h2>
                        <div id="detail-status-badge-container" class="flex items-center mt-1">
                            <span class="px-3 py-1 bg-cyan-500/20 border border-cyan-400/30 text-cyan-300 rounded-full text-xs font-bold shadow-sm inline-block tracking-wide">Diproses</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white transition-all duration-200 focus:outline-none shrink-0 relative z-10 backdrop-blur-sm" data-hs-overlay="#modal-detail-permintaan">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-[#f8fafc]">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Items grouped by Project -->
                    <div class="lg:col-span-2 space-y-6" id="detail-modal-body">
                        <div class="text-center py-10 text-slate-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm font-semibold">Memuat rincian...</p>
                        </div>
                    </div>

                    <!-- Right Column: Info, Alamat, Timeline, and Actions -->
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-6 sticky top-0 max-h-[calc(100vh-12rem)] overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-slate-50 [&::-webkit-scrollbar-thumb]:bg-slate-300">
                            
                            <!-- Info Kontraktor -->
                            <div>
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm mb-3">
                                    <i class="far fa-user text-slate-400 text-sm"></i>
                                    <span>Info Kontraktor</span>
                                </div>
                                <div class="grid grid-cols-2 gap-y-2.5 text-xs font-semibold text-slate-700 pt-1">
                                    <div class="text-slate-400">Kontraktor</div>
                                    <div id="detail-contractor-name" class="text-right text-slate-800">-</div>
                                    
                                    <div class="text-slate-400">Tanggal Request</div>
                                    <div id="detail-request-date" class="text-right text-slate-800">-</div>
                                </div>
                            </div>
                            
                            <hr class="border-slate-100">
                            
                            <!-- Alamat Proyek -->
                            <div>
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm mb-3">
                                    <i class="fas fa-map-marker-alt text-slate-400 text-sm"></i>
                                    <span>Alamat</span>
                                </div>
                                <div id="detail-project-addresses" class="space-y-3.5 text-xs font-semibold text-slate-700 pt-1">
                                    <!-- Dynamic project names & locations -->
                                </div>
                            </div>
                            
                            <hr class="border-slate-100">

                            <!-- Riwayat Status Timeline -->
                            <div>
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm mb-4">
                                    <i class="far fa-clock text-slate-400 text-sm"></i>
                                    <span>Riwayat Status</span>
                                </div>
                                <div class="relative border-l border-slate-200 ml-2.5 pl-5 space-y-6" id="detail-modal-timeline">
                                    <!-- Dynamic status timeline -->
                                </div>
                            </div>

                            <!-- (Telah dipindahkan ke dalam timeline) -->
                        </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
